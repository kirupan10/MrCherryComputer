<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    private function userHasRole(string $role): bool
    {
        $user = Auth::user();

        if (!$user || !method_exists($user, 'hasRole')) {
            return false;
        }

        return (bool) call_user_func([$user, 'hasRole'], $role);
    }

    public function index(Request $request)
    {
        $query = Expense::with(['category', 'creator', 'approver']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->latest('expense_date')->paginate(20);
        $categories = ExpenseCategory::active()->get();

        $stats = [
            'total_expenses' => Expense::where('status', 'approved')->sum('amount'),
            'pending_count' => Expense::where('status', 'pending')->count(),
            'this_month' => Expense::where('status', 'approved')
                ->whereMonth('expense_date', now()->month)
                ->sum('amount'),
        ];

        return view('expenses.index', compact('expenses', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = ExpenseCategory::active()->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,paid,approved',
        ]);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $validated['receipt_image'] = $request->file('receipt')->store('expenses', 'public');
        }

        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['created_by'] = Auth::id();

        // Only admins can create approved expenses.
        if (($validated['status'] ?? 'pending') === 'approved') {
            if ($this->userHasRole('admin')) {
                $validated['approved_by'] = Auth::id();
            } else {
                $validated['status'] = 'pending';
            }
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'creator', 'approver']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        // Only allow editing pending expenses
        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be edited.']);
        }

        // Authorization check (only creator or admin can edit)
        if (!$this->userHasRole('admin') && $expense->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = ExpenseCategory::active()->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Only allow updating pending expenses
        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be updated.']);
        }

        // Authorization check
        if (!$this->userHasRole('admin') && $expense->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,paid,approved',
        ]);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $validated['receipt_image'] = $request->file('receipt')->store('expenses', 'public');
        }

        $targetStatus = $validated['status'] ?? $expense->status;

        if ($targetStatus === 'approved') {
            if ($this->userHasRole('admin')) {
                $validated['approved_by'] = Auth::id();
            } else {
                $validated['status'] = 'pending';
                $validated['approved_by'] = null;
            }
        } else {
            $validated['approved_by'] = null;
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function approve(Expense $expense)
    {
        // Only admin can approve
        if (!$this->userHasRole('admin')) {
            abort(403, 'Only administrators can approve expenses.');
        }

        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be approved.']);
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Expense approved successfully.');
    }

    public function reject(Request $request, Expense $expense)
    {
        // Only admin can reject
        if (!$this->userHasRole('admin')) {
            abort(403, 'Only administrators can reject expenses.');
        }

        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be rejected.']);
        }

        $rejectionReason = trim((string) $request->input('rejection_reason', 'Rejected by administrator'));

        $expense->update([
            'status' => 'paid',
            'description' => trim(($expense->description ?? '') . "\nRejection note: " . $rejectionReason),
        ]);

        return back()->with('success', 'Expense rejected and marked as paid.');
    }

    public function destroy(Expense $expense)
    {
        // Only admin can delete, and only pending/rejected expenses
        if (!$this->userHasRole('admin')) {
            abort(403, 'Only administrators can delete expenses.');
        }

        if ($expense->status === 'approved') {
            return back()->withErrors(['error' => 'Cannot delete approved expenses.']);
        }

        // Delete receipt
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
