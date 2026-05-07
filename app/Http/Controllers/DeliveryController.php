<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Expense;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id ?? null;

        $query = Delivery::query()->with('expense');

        // Shop filter is automatically applied by ShopScope
        // But we verify shop_id for security
        if (!$shopId && !$request->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        // Filter by direction
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('tracking_number', 'like', '%' . $request->search . '%')
                  ->orWhere('from_location', 'like', '%' . $request->search . '%')
                  ->orWhere('to_location', 'like', '%' . $request->search . '%')
                  ->orWhere('received_by', 'like', '%' . $request->search . '%');
            });
        }

        $deliveries = $query->orderByDesc('delivery_date')->orderByDesc('created_at')->paginate(20);

        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        return view('deliveries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'direction' => 'required|in:incoming,outgoing,dropship',
            'tracking_number' => 'nullable|string',
            'from_location' => 'nullable|string',
            'to_location' => 'nullable|string',
            'received_by' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'details' => 'nullable|array',
            'shop_id' => 'nullable|integer',
        ]);

        // Create expense based on direction and payment type
        // Incoming + COD = we pay (create expense)
        // Outgoing + Paid = we pay (create expense)
        // All other cases = no expense
        $expense = null;
        $shouldCreateExpense = (
            ($data['direction'] === 'incoming' && $data['payment_type'] === 'COD') ||
            ($data['direction'] === 'outgoing' && $data['payment_type'] === 'Paid')
        ) && isset($data['cost']) && $data['cost'] > 0;

        if ($shouldCreateExpense) {
            $expense = Expense::create([
                'type' => 'Delivery Cost',
                'amount' => $data['cost'],
                'expense_date' => $data['delivery_date'] ?? now(),
                'notes' => $data['notes'] ?? 'Auto-created from delivery management - ' . ucfirst($data['direction']) . ' delivery',
                'details' => $data['details'] ?? [],
                'shop_id' => $data['shop_id'] ?? $request->user()->shop_id ?? null,
                'created_by' => $request->user()->id ?? null,
            ]);
        }

        $delivery = Delivery::create([
            'direction' => $data['direction'],
            'tracking_number' => $data['tracking_number'],
            'from_location' => $data['from_location'],
            'to_location' => $data['to_location'],
            'received_by' => $data['received_by'],
            'delivery_date' => $data['delivery_date'] ?? now(),
            'payment_type' => $data['payment_type'],
            'cost' => $data['cost'],
            'notes' => $data['notes'],
            'details' => $data['details'] ?? [],
            'expense_id' => $expense?->id,
            'shop_id' => $data['shop_id'] ?? $request->user()->shop_id ?? null,
            'created_by' => $request->user()->id ?? null,
        ]);

        return redirect()->route('deliveries.index')->with('success', 'Delivery recorded successfully');
    }

    public function show(Delivery $delivery)
    {
        // Authorization check - ensure user can only view deliveries from their shop
        if ($delivery->shop_id && $delivery->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this delivery.');
        }

        $delivery->load('expense');
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        // Authorization check - ensure user can only edit deliveries from their shop
        if ($delivery->shop_id && $delivery->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this delivery.');
        }

        return view('deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        // Authorization check - ensure user can only update deliveries from their shop
        if ($delivery->shop_id && $delivery->shop_id !== $request->user()->shop_id) {
            abort(403, 'Unauthorized access to this delivery.');
        }

        $data = $request->validate([
            'direction' => 'required|in:incoming,outgoing,dropship',
            'tracking_number' => 'nullable|string',
            'from_location' => 'nullable|string',
            'to_location' => 'nullable|string',
            'received_by' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'details' => 'nullable|array',
        ]);

        // Determine if expense should be created/updated
        $shouldCreateExpense = (
            ($data['direction'] === 'incoming' && $data['payment_type'] === 'COD') ||
            ($data['direction'] === 'outgoing' && $data['payment_type'] === 'Paid')
        ) && isset($data['cost']) && $data['cost'] > 0;

        if ($shouldCreateExpense) {
            if ($delivery->expense_id) {
                // Update existing expense
                $delivery->expense->update([
                    'amount' => $data['cost'],
                    'expense_date' => $data['delivery_date'] ?? $delivery->expense->expense_date,
                    'notes' => $data['notes'] ?? $delivery->expense->notes,
                    'details' => $data['details'] ?? [],
                ]);
            } else {
                // Create new expense
                $expense = Expense::create([
                    'type' => 'Delivery Cost',
                    'amount' => $data['cost'],
                    'expense_date' => $data['delivery_date'] ?? now(),
                    'notes' => $data['notes'] ?? 'Auto-created from delivery management',
                    'details' => $data['details'] ?? [],
                    'shop_id' => $delivery->shop_id ?? $request->user()->shop_id ?? null,
                    'created_by' => $request->user()->id ?? null,
                ]);
                $data['expense_id'] = $expense->id;
            }
        } elseif ($delivery->expense_id && (!isset($data['cost']) || $data['cost'] == 0)) {
            // Delete expense if cost is removed
            $delivery->expense->delete();
            $data['expense_id'] = null;
        }

        $delivery->update($data);

        return redirect()->route('deliveries.edit', $delivery)->with('success', 'Delivery updated successfully');
    }

    public function destroy(Delivery $delivery)
    {
        // Authorization check - ensure user can only delete deliveries from their shop
        if ($delivery->shop_id && $delivery->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this delivery.');
        }

        // Delete linked expense if exists
        if ($delivery->expense_id) {
            $delivery->expense->delete();
        }

        $delivery->delete();

        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted successfully');
    }
}
