<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'soldBy', 'payments'])
            ->withCount('items');

        // Role-based filtering
        if (Auth::user()->hasRole('cashier')) {
            $query->where('sold_by', Auth::id());
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(20);

        $stats = [
            'total_sales' => Sale::sum('total_amount'),
            'today_sales' => Sale::whereDate('created_at', today())->sum('total_amount'),
            'completed_count' => Sale::where('status', 'completed')->count(),
        ];

        return view('sales.index', compact('sales', 'stats'));
    }

    public function show(Sale $sale)
    {
        // Authorization check
        if (Auth::user()->hasRole('cashier') && $sale->sold_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this sale.');
        }

        $sale->load(['customer', 'items.product.unit', 'payments', 'soldBy']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        // Only allow editing pending or draft sales
        if (!in_array($sale->status, ['pending', 'draft'])) {
            return back()->withErrors(['error' => 'Cannot edit completed sales.']);
        }

        // Authorization check
        if (Auth::user()->hasRole('cashier') && $sale->sold_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this sale.');
        }

        $sale->load(['customer', 'items.product']);

        return view('sales.edit', compact('sale'));
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
        if (Auth::user()->hasRole('cashier') && $sale->sold_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        return view('sales.invoice', compact('sale'));
    }

    public function downloadInvoice($id)
    {
        $sale = Sale::with(['customer', 'items.product.unit', 'payments', 'soldBy'])
            ->findOrFail($id);

        // Authorization check
        if (Auth::user()->hasRole('cashier') && $sale->sold_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $pdf = Pdf::loadView('sales.invoice-pdf', compact('sale'));
        return $pdf->download("invoice-{$sale->invoice_number}.pdf");
    }

    public function destroy(Sale $sale)
    {
        // Only admin can delete sales
        if (!Auth::user()->hasRole('admin')) {
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
