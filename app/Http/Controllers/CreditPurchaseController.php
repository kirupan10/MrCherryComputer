<?php

namespace App\Http\Controllers;

use App\Models\CreditPurchase;
use App\Models\CreditPurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditPurchaseController extends Controller
{
    /**
     * Display a listing of credit purchases
     */
    public function index(Request $request)
    {
        $shopId = auth()->user()->shop_id;
        $status = $request->get('status', 'pending'); // Default to pending
        $purchaseType = $request->get('purchase_type');
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = CreditPurchase::where('shop_id', $shopId)->with('createdBy', 'payments');

        // Filter by status - if 'all' is selected or empty, show all statuses
        if ($status && in_array($status, ['pending', 'partial', 'paid'])) {
            $query->where('status', $status);
        }

        // Filter by purchase type
        if ($purchaseType && in_array($purchaseType, ['cash', 'cheque', 'credit'])) {
            $query->where('purchase_type', $purchaseType);
        }

        // Search by vendor name or reference number
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($fromDate) {
            $query->whereDate('purchase_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('purchase_date', '<=', $toDate);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(20)->appends($request->all());

        // Calculate totals for filtered results
        $totals = $query->select(
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('SUM(due_amount) as due_amount')
            )
            ->first();

        return view('credit-purchases.index', compact('purchases', 'totals', 'status', 'purchaseType', 'search', 'fromDate', 'toDate'));
    }

    /**
     * Show the form for creating a new credit purchase
     */
    public function create()
    {
        return view('credit-purchases.create');
    }

    /**
     * Store a newly created credit purchase
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_address' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'credit_days' => 'required_if:purchase_type,credit|nullable|integer|min:1',
            'purchase_type' => 'required|in:cash,cheque,credit',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'nullable|json',
        ]);

        $shopId = auth()->user()->shop_id;
        $createdBy = auth()->id();

        // Calculate due date
        $purchaseDate = Carbon::parse($validated['purchase_date']);
        $dueDate = $purchaseDate->copy()->addDays($validated['credit_days']);

        $purchase = CreditPurchase::create([
            'shop_id' => $shopId,
            'created_by' => $createdBy,
            'vendor_name' => $validated['vendor_name'],
            'vendor_phone' => $validated['vendor_phone'],
            'vendor_email' => $validated['vendor_email'],
            'vendor_address' => $validated['vendor_address'],
            'total_amount' => $validated['total_amount'],
            'paid_amount' => 0,
            'due_amount' => $validated['total_amount'],
            'purchase_date' => $purchaseDate,
            'due_date' => $dueDate,
            'credit_days' => $validated['credit_days'],
            'status' => 'pending',
            'purchase_type' => $validated['purchase_type'],
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
            'items' => $validated['items'] ?? null,
        ]);

        return redirect()->route('credit-purchases.show', $purchase->id)
                        ->with('success', 'Credit purchase created successfully');
    }

    /**
     * Display the specified credit purchase
     */
    public function show(CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== auth()->user()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $creditPurchase->load('createdBy', 'payments.createdBy');
        return view('credit-purchases.show', compact('creditPurchase'));
    }

    /**
     * Show the form for editing the specified credit purchase
     */
    public function edit(CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== auth()->user()->shop_id) {
            abort(403, 'Unauthorized');
        }

        return view('credit-purchases.edit', compact('creditPurchase'));
    }

    /**
     * Update the specified credit purchase
     */
    public function update(Request $request, CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== auth()->user()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_address' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'credit_days' => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // If total amount changed, adjust due amount
        $amountDifference = $validated['total_amount'] - $creditPurchase->total_amount;
        $newDueAmount = $creditPurchase->due_amount + $amountDifference;

        // Calculate new due date
        $purchaseDate = Carbon::parse($validated['purchase_date']);
        $dueDate = $purchaseDate->copy()->addDays($validated['credit_days']);

        // Recalculate status based on paid amount vs new total amount
        $status = 'pending';
        if ($creditPurchase->paid_amount > 0) {
            $status = 'partial';
        }
        if ($newDueAmount <= 0) {
            $status = 'paid';
            $newDueAmount = 0;
        }

        $creditPurchase->update([
            'vendor_name' => $validated['vendor_name'],
            'vendor_phone' => $validated['vendor_phone'],
            'vendor_email' => $validated['vendor_email'],
            'vendor_address' => $validated['vendor_address'],
            'total_amount' => $validated['total_amount'],
            'due_amount' => $newDueAmount,
            'purchase_date' => $purchaseDate,
            'due_date' => $dueDate,
            'credit_days' => $validated['credit_days'],
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
            'status' => $status,
        ]);

        // Update vendor balance
        $vendor = null;
        if ($creditPurchase->vendor_id) {
            $vendor = \App\Models\Vendor::find($creditPurchase->vendor_id);
        } else {
            // Try to find vendor by name if vendor_id is not set
            $vendor = \App\Models\Vendor::where('shop_id', auth()->user()->shop_id)
                                         ->where('name', $validated['vendor_name'])
                                         ->first();
            // If found, save the vendor_id for future use
            if ($vendor) {
                $creditPurchase->update(['vendor_id' => $vendor->id]);
            }
        }
        
        if ($vendor) {
            $vendor->updateBalances();
        }

        return redirect()->route('credit-purchases.show', $creditPurchase->id)
                        ->with('success', 'Credit purchase updated successfully');
    }

    /**
     * Delete the specified credit purchase
     */
    public function destroy(CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== auth()->user()->shop_id) {
            abort(403, 'Unauthorized');
        }

        // Store vendor info before deleting
        $vendorId = $creditPurchase->vendor_id;
        $vendorName = $creditPurchase->vendor_name;
        $shopId = $creditPurchase->shop_id;

        $creditPurchase->delete();

        // Update vendor balance after deletion
        $vendor = null;
        if ($vendorId) {
            $vendor = \App\Models\Vendor::find($vendorId);
        } else {
            // Try to find vendor by name
            $vendor = \App\Models\Vendor::where('shop_id', $shopId)
                                         ->where('name', $vendorName)
                                         ->first();
        }
        
        if ($vendor) {
            $vendor->updateBalances();
        }

        return redirect()->route('credit-purchases.index')
                        ->with('success', 'Credit purchase deleted successfully');
    }

    /**
     * Record a payment for the credit purchase
     */
    public function recordPayment(Request $request, CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== auth()->user()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:Cash,Check,Bank Transfer,Card,Other',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Ensure payment doesn't exceed remaining due amount
        if ($validated['payment_amount'] > $creditPurchase->due_amount) {
            return back()->withErrors(['payment_amount' => 'Payment amount exceeds remaining balance']);
        }

        // Record the payment
        CreditPurchasePayment::create([
            'credit_purchase_id' => $creditPurchase->id,
            'created_by' => auth()->id(),
            'shop_id' => $creditPurchase->shop_id,
            'payment_amount' => $validated['payment_amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'],
            'notes' => $validated['notes'],
        ]);

        // Update credit purchase amounts
        $newPaidAmount = $creditPurchase->paid_amount + $validated['payment_amount'];
        $newDueAmount = $creditPurchase->total_amount - $newPaidAmount;

        // Update status
        $status = 'partial';
        if ($newDueAmount <= 0) {
            $status = 'paid';
            $newDueAmount = 0;
        }

        $creditPurchase->update([
            'paid_amount' => $newPaidAmount,
            'due_amount' => $newDueAmount,
            'status' => $status,
        ]);

        // Update vendor balance
        $vendor = null;
        if ($creditPurchase->vendor_id) {
            $vendor = \App\Models\Vendor::find($creditPurchase->vendor_id);
        } else {
            // Try to find vendor by name if vendor_id is not set
            $vendor = \App\Models\Vendor::where('shop_id', auth()->user()->shop_id)
                                         ->where('name', $creditPurchase->vendor_name)
                                         ->first();
            // If found, save the vendor_id for future use
            if ($vendor) {
                $creditPurchase->update(['vendor_id' => $vendor->id]);
            }
        }
        
        if ($vendor) {
            $vendor->updateBalances();
        }

        return back()->with('success', 'Payment recorded successfully');
    }

    /**
     * Get summary statistics for credit purchases
     */
    public function getSummary(Request $request)
    {
        $shopId = auth()->user()->shop_id;
        $month = $request->get('month', now()->format('Y-m'));

        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            $selectedDate = now();
        }

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();

        // Total credit purchases for the month
        $totalPurchases = CreditPurchase::where('shop_id', $shopId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Total paid in the month
        $totalPaid = CreditPurchasePayment::where('shop_id', $shopId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('payment_amount');

        // Outstanding credit purchases
        $outstandingCount = CreditPurchase::where('shop_id', $shopId)
            ->whereIn('status', ['pending', 'partial'])
            ->count();

        $outstandingAmount = CreditPurchase::where('shop_id', $shopId)
            ->whereIn('status', ['pending', 'partial'])
            ->sum('due_amount');

        // Overdue purchases
        $overdueCount = CreditPurchase::where('shop_id', $shopId)
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $overdueAmount = CreditPurchase::where('shop_id', $shopId)
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', now()->toDateString())
            ->sum('due_amount');

        return compact('totalPurchases', 'totalPaid', 'outstandingCount', 'outstandingAmount', 'overdueCount', 'overdueAmount');
    }
}
