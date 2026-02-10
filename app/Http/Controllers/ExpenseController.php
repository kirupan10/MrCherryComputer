<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'createdBy', 'approvedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
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
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card,upi,bank_transfer,cheque',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $validated['receipt'] = $request->file('receipt')->store('expenses', 'public');
        }

        $validated['status'] = 'pending';
        $validated['created_by'] = Auth::id();

        // Auto-approve for admin role
        if (Auth::user()->hasRole('admin')) {
            $validated['status'] = 'approved';
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'createdBy', 'approvedBy']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        // Only allow editing pending expenses
        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be edited.']);
        }

        // Authorization check (only creator or admin can edit)
        if (!Auth::user()->hasRole('admin') && $expense->created_by !== Auth::id()) {
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
        if (!Auth::user()->hasRole('admin') && $expense->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card,upi,bank_transfer,cheque',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($expense->receipt) {
                Storage::disk('public')->delete($expense->receipt);
            }
            $validated['receipt'] = $request->file('receipt')->store('expenses', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function approve(Expense $expense)
    {
        // Only admin can approve
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Only administrators can approve expenses.');
        }

        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be approved.']);
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Expense approved successfully.');
    }

    public function reject(Request $request, Expense $expense)
    {
        // Only admin can reject
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Only administrators can reject expenses.');
        }

        if ($expense->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending expenses can be rejected.']);
        }

        $expense->update([
            'status' => 'rejected',
            'notes' => $request->input('rejection_reason', '') . "\n" . $expense->notes,
        ]);

        return back()->with('success', 'Expense rejected.');
    }

    public function destroy(Expense $expense)
    {
        // Only admin can delete, and only pending/rejected expenses
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Only administrators can delete expenses.');
        }

        if ($expense->status === 'approved') {
            return back()->withErrors(['error' => 'Cannot delete approved expenses.']);
        }

        // Delete receipt
        if ($expense->receipt) {
            Storage::disk('public')->delete($expense->receipt);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
