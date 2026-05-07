<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id ?? null;
        $category = $request->input('category');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $base = Expense::query();
        if ($shopId) {
            $base->where('shop_id', $shopId);
        }

        // Canonical category list — must match the options in the expense create form.
        $expenseCategories = collect([
            'Rent',
            'Electricity',
            'Repairs',
            'Supplies',
            'Internet',
            'Transport',
            'Office Supplies',
            'Salaries',
            'Marketing designing & Video Editing',
            'Delivery Cost',
            'Food',
            'Other',
        ]);

        // Per-category totals scoped to the same date window but NOT filtered by category,
        // so every category always shows its spending amount.
        $categoryTotalsBase = Expense::query();
        if ($shopId) {
            $categoryTotalsBase->where('shop_id', $shopId);
        }
        if ($startDate) {
            $categoryTotalsBase->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate) {
            $categoryTotalsBase->whereDate('expense_date', '<=', $endDate);
        }
        if (!$startDate && !$endDate) {
            $categoryTotalsBase->whereMonth('expense_date', now()->month)
                               ->whereYear('expense_date', now()->year);
        }
        $categoryTotals = $categoryTotalsBase
            ->whereNotNull('type')
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        if ($category) {
            $base->where(function($q) use ($category) {
                $q->where('type', $category)
                  ->orWhere('details->category', $category);
            });
        }
        if ($startDate) {
            $base->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate) {
            $base->whereDate('expense_date', '<=', $endDate);
        }

        // If no date filters, default to current month
        if (!$startDate && !$endDate) {
            $base->whereMonth('expense_date', now()->month)
                 ->whereYear('expense_date', now()->year);
        }

        // Calculate totals from ALL expenses (before pagination)
        $totalExpenses = (clone $base)->sum('amount');
        $totalRecords = (clone $base)->count();

        // Paginate expenses (10 per page)
        $paginatedExpenses = $base->with('delivery')
                                  ->orderBy('expense_date', 'desc')
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10)
                                  ->withQueryString();

        // Group by month-year (for current page only)
        $expensesByMonth = $paginatedExpenses->getCollection()->groupBy(function($expense) {
            return $expense->expense_date->format('F Y');
        });

        return view('expenses.index', [
            'expensesByMonth' => $expensesByMonth,
            'totalExpenses' => $totalExpenses,
            'totalRecords' => $totalRecords,
            'paginatedExpenses' => $paginatedExpenses,
            'expenseCategories' => $expenseCategories,
            'categoryTotals' => $categoryTotals,
        ]);
    }

    /**
     * Display a specific expense.
     */
    public function show(Expense $expense)
    {
        // Authorization check
        if ($expense->shop_id && $expense->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $expense->load('creator');
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show expense create form.
     */
    public function create(Request $request)
    {
        $shopId = $request->user()->shop_id ?? null;

        $base = Expense::query();
        if ($shopId) {
            $base->where('shop_id', $shopId);
        }

        // Use KpiService which calls stored procedure to get per-shop expense aggregates
        $kpiService = new \App\Services\KpiService();
        $expenseKpis = $kpiService->getExpenseKpisByShop($shopId);

        $totalExpenses = $expenseKpis->total_expenses ?? 0;
        $monthTotal = $expenseKpis->last_30_days_expenses ?? 0;
        $weekTotal = 0; // week-level cached proc not implemented; compute on demand if needed
        $typesCount = $expenseKpis->types_count ?? 0;

    $recent = (clone $base)->latest('expense_date')->latest()->limit(10)->get();

        return view('expenses.create', [
            'totalExpenses' => $totalExpenses,
            'monthTotal' => $monthTotal,
            'weekTotal' => $weekTotal,
            'typesCount' => $typesCount,
            'expenses' => $recent,
        ]);
    }

    /**
     * Show edit form for an expense.
     */
    public function edit(Expense $expense)
    {
        // Staff members cannot edit expenses
        if (auth()->user()->isEmployee()) {
            abort(403, 'Staff members do not have permission to edit expenses.');
        }

        // Authorization check
        if ($expense->shop_id && $expense->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update an expense.
     */
    public function update(Request $request, Expense $expense)
    {
        // Staff members cannot update expenses
        if (auth()->user()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update expenses.');
        }

        // Authorization check
        if ($expense->shop_id && $expense->shop_id !== $request->user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $data = $request->validate([
            'type' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'details' => 'nullable|array',
        ]);

        $expense->update([
            'type' => $data['type'] ?? $expense->type,
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'] ?? $expense->expense_date,
            'notes' => $data['notes'] ?? $expense->notes,
            'details' => isset($data['details']) ? array_filter($data['details']) : $expense->details,
        ]);

        return redirect()->route('expenses.edit', $expense)->with('status', 'Expense updated');
    }
    /**
     * Store an expense record.
     * Expected payload: type, amount (decimal), expense_date, notes, details
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'details' => 'nullable|array',
        ]);


        $expense = Expense::create([
            'type' => $data['type'] ?? null,
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'] ?? now(),
            'notes' => $data['notes'] ?? null,
            'details' => !empty($data['details']) ? array_filter($data['details']) : null,
            'shop_id' => $request->user()->shop_id ?? null,
            'created_by' => $request->user()->id ?? null,
        ]);

        // Log this expense as an outgoing transaction
        \App\Models\BusinessTransaction::create([
            'shop_id' => $expense->shop_id,
            'created_by' => $expense->created_by,
            'transaction_date' => $expense->expense_date,
            'transaction_type' => 'expense',
            // Use expense type as vendor/supplier for clarity
            'vendor_name' => $expense->type,
            'receipt_number' => null,
            'reference_number' => null,
            'paid_by' => null,
            'paid_by_user_id' => null,
            'total_amount' => $expense->amount,
            'discount_amount' => 0,
            'net_amount' => $expense->amount,
            'description' => $expense->notes,
            'items' => null,
            'category' => $expense->type,
            'status' => 'completed',
            'attachment_path' => null,
        ]);

        // If the client expects JSON (API or AJAX), return JSON. Otherwise redirect
        // to the expense create page so the user can add more records.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok', 'expense_id' => $expense->id], 201);
        }

        return redirect()
            ->route('expenses.create')
            ->with('success', 'Expense recorded successfully');
    }

    /**
     * Delete an expense.
     */
    public function destroy(Expense $expense)
    {
        // Staff members cannot delete expenses
        if (auth()->user()->isEmployee()) {
            abort(403, 'Staff members do not have permission to delete expenses.');
        }

        // Authorization check
        if ($expense->shop_id && $expense->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
