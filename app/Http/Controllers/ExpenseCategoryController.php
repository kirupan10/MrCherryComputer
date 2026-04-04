<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseCategory::withCount('expenses');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $expenseCategories = $query->latest()->paginate(20)->withQueryString();

        return view('expense-categories.index', compact('expenseCategories'));
    }

    public function create()
    {
        return view('expense-categories.create');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->loadCount('expenses');

        $recentExpenses = $expenseCategory->expenses()
            ->latest()
            ->limit(10)
            ->get(['id', 'expense_date', 'amount', 'status', 'description']);

        return view('expense-categories.show', compact('expenseCategory', 'recentExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        // Check if category has expenses
        if ($expenseCategory->expenses()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with existing expenses.']);
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}
