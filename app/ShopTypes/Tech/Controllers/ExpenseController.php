<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\Expense;

class ExpenseController extends \App\Http\Controllers\ExpenseController
{
    protected function createRoute(): string
    {
        return 'tech.expenses.create';
    }

    protected function indexRoute(): string
    {
        return 'tech.expenses.index';
    }

    protected function editRoute(Expense $expense): string
    {
        return route('tech.expenses.edit', $expense);
    }
}
