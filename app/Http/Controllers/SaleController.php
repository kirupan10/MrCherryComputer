<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
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
        $query = Sale::with(['customer', 'user', 'payments'])
            ->withCount('items');

        // Role-based filtering
        if ($this->userHasRole('cashier')) {
            $query->where('created_by', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('sale_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('sale_date', '<=', $request->to_date);
        }

        $statsQuery = clone $query;

        $sales = $query->latest()->paginate(20);

        $stats = [
            'total_sales' => (clone $statsQuery)->sum('total_amount'),
            'today_sales' => (clone $statsQuery)->whereDate('sale_date', today())->sum('total_amount'),
            'completed_count' => (clone $statsQuery)->where('status', 'completed')->count(),
        ];

        return view('sales.index', compact('sales', 'stats'));
    }

    public function show(Sale $sale)
    {
        // Authorization check
        if ($this->userHasRole('cashier') && $sale->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this sale.');
        }

        $sale->load(['customer', 'items.product.unit', 'payments', 'user']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if (!in_array($sale->status, ['pending', 'cancelled'])) {
            return back()->withErrors(['error' => 'Only pending or cancelled sales can be edited.']);
        }

        // Authorization check
        if ($this->userHasRole('cashier') && $sale->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this sale.');
        }

        $sale->load(['customer', 'items.product', 'payments']);

        return view('sales.edit', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        if (!in_array($sale->status, ['pending', 'cancelled'])) {
            return back()->withErrors(['error' => 'Only pending or cancelled sales can be updated.']);
        }

        if ($this->userHasRole('cashier') && $sale->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this sale.');
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,upi,bank_transfer,mixed',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $dueAmount = max(0, (float) $sale->total_amount - (float) $validated['paid_amount']);

        $sale->update([
            'customer_id' => $validated['customer_id'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'paid_amount' => $validated['paid_amount'],
            'due_amount' => $dueAmount,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale details updated successfully.');
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $sale->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $sale->notes,
        ]);

        return back()->with('success', 'Sale status updated successfully.');
    }

    public function invoice($id)
    {
        $sale = Sale::with(['customer', 'items.product.unit', 'payments', 'soldBy'])
            ->findOrFail($id);

        // Authorization check
        if ($this->userHasRole('cashier') && $sale->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        return view('sales.invoice', compact('sale'));
    }

    public function downloadInvoice($id)
    {
        $sale = Sale::with(['customer', 'items.product.unit', 'payments', 'soldBy'])
            ->findOrFail($id);

        // Authorization check
        if ($this->userHasRole('cashier') && $sale->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $pdf = Pdf::loadView('sales.invoice-pdf', compact('sale'));
        return $pdf->download("invoice-{$sale->invoice_number}.pdf");
    }

    public function destroy(Sale $sale)
    {
        // Only admin can delete sales
        if (!$this->userHasRole('admin')) {
            abort(403, 'Only administrators can delete sales.');
        }

        if ($sale->status === 'completed') {
            return back()->withErrors(['error' => 'Cannot delete completed sales. Please cancel first.']);
        }

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sale deleted successfully.');
    }
}
