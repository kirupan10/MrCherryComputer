<?php

namespace App\Http\Controllers;

use App\Models\CreditPurchase;
use App\Models\CreditPurchasePayment;
use App\Models\Cheque;
use App\Models\BusinessTransaction;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    private function resolveView(string $page): string
    {
        $shopType = active_shop_type() ?? 'tech';
        $shopTypeView = "shop-types.{$shopType}.credit-purchases.{$page}";
        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        $techView = "shop-types.tech.credit-purchases.{$page}";
        if (view()->exists($techView)) {
            return $techView;
        }

        return "credit-purchases.{$page}";
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Display a listing of credit purchases
     */
    public function index(Request $request)
    {
        $shopId = $this->currentUser()->shop_id;
        $status = $request->get('status');
        $purchaseType = $request->get('purchase_type');
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = CreditPurchase::where('shop_id', $shopId)->with('createdBy', 'payments');

        // Filter by status
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

        // Order by date (latest first)
        $purchases = $query
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // Calculate totals
        $totals = CreditPurchase::where('shop_id', $shopId)
            ->select(
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('SUM(due_amount) as due_amount')
            )
            ->first();

        // Count suppliers with pending or partial credit
        $suppliersPendingCount = CreditPurchase::where('shop_id', $shopId)
            ->whereIn('status', ['pending', 'partial'])
            ->distinct('vendor_name')
            ->count('vendor_name');

        return view($this->resolveView('index'), compact('purchases', 'totals', 'status', 'purchaseType', 'search', 'fromDate', 'toDate', 'suppliersPendingCount'));
    }

    /**
     * Show the form for creating a new credit purchase
     */
    public function create()
    {
        $shopId = $this->currentUser()->shop_id;
        $vendors = \App\Models\Vendor::where('shop_id', $shopId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view($this->resolveView('create'), compact('vendors'));
    }

    /**
     * Store a newly created credit purchase
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'total_amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date|before_or_equal:today',
            'credit_days' => 'required_if:purchase_type,credit|nullable|integer|min:1',
            'purchase_type' => 'required|in:cash,cheque,credit',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'nullable|json',
            // Cheque fields
            'cheque_number' => 'required_if:purchase_type,cheque|nullable|string|max:255',
            'cheque_date' => 'required_if:purchase_type,cheque|nullable|date',
            'bank_name' => 'required_if:purchase_type,cheque|nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'drawer_name' => 'nullable|string|max:255',
        ]);

        $shopId = $this->currentUser()->shop_id;
        $createdBy = auth()->id();

        // Get vendor details
        $vendor = \App\Models\Vendor::findOrFail($validated['vendor_id']);

        // Calculate payment status based on purchase type
        $purchaseDate = Carbon::parse($validated['purchase_date']);
        $isCashPayment = $validated['purchase_type'] === 'cash';

        // For cash purchases: instant payment completion
        // For credit/cheque: pending payment
        $paidAmount = $isCashPayment ? $validated['total_amount'] : 0;
        $dueAmount = $isCashPayment ? 0 : $validated['total_amount'];
        $status = $isCashPayment ? 'paid' : 'pending';
        $creditDays = $validated['credit_days'] ?? 0;
        $dueDate = $isCashPayment ? $purchaseDate : $purchaseDate->copy()->addDays($creditDays);

        $purchase = CreditPurchase::create([
            'shop_id' => $shopId,
            'created_by' => $createdBy,
            'vendor_id' => $validated['vendor_id'],
            'vendor_name' => $vendor->name,
            'vendor_phone' => $vendor->phone,
            'vendor_email' => $vendor->email,
            'vendor_address' => $vendor->address,
            'total_amount' => $validated['total_amount'],
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'purchase_date' => $purchaseDate,
            'due_date' => $dueDate,
            'credit_days' => $creditDays,
            'status' => $status,
            'purchase_type' => $validated['purchase_type'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'items' => $validated['items'] ?? null,
        ]);

        // For cash purchases, create payment record
        if ($isCashPayment) {
            CreditPurchasePayment::create([
                'credit_purchase_id' => $purchase->id,
                'payment_date' => $purchaseDate,
                'amount' => $validated['total_amount'],
                'payment_method' => 'cash',
                'notes' => 'Cash payment recorded automatically on purchase',
                'created_by' => $createdBy,
            ]);
        }

        // For cheque purchases, create cheque record
        if ($validated['purchase_type'] === 'cheque') {
            Cheque::create([
                'shop_id' => $shopId,
                'created_by' => $createdBy,
                'cheque_number' => $validated['cheque_number'],
                'bank_name' => $validated['bank_name'],
                'branch_name' => $validated['branch_name'] ?? null,
                'cheque_date' => Carbon::parse($validated['cheque_date']),
                'amount' => $validated['total_amount'],
                'related_to' => 'vendor_payment',
                'related_id' => $purchase->id,
                'drawer_name' => $validated['drawer_name'] ?? $vendor->name,
                'payee_name' => $this->currentUser()->shop->name ?? 'Company',
                'status' => 'pending',
                'notes' => 'Cheque received for vendor purchase #' . $purchase->id,
                'reference_number' => $validated['reference_number'] ?? null,
            ]);
        }

        // Update vendor balance
        $vendor->updateBalances();

        // Record in business transactions for cash and cheque purchases
        if ($isCashPayment || $validated['purchase_type'] === 'cheque') {
            BusinessTransaction::create([
                'shop_id' => $shopId,
                'created_by' => $createdBy,
                'transaction_date' => $purchaseDate,
                'transaction_type' => $validated['purchase_type'] === 'cheque' ? 'cheque_payment' : 'cash_payment',
                'vendor_name' => $vendor->name,
                'reference_number' => $validated['reference_number'] ?? null,
                'receipt_number' => $validated['purchase_type'] === 'cheque' ? $validated['cheque_number'] : null,
                'paid_by' => $validated['purchase_type'],
                'paid_by_user_id' => $createdBy,
                'total_amount' => $validated['total_amount'],
                'net_amount' => $validated['total_amount'],
                'description' => ucfirst($validated['purchase_type']) . ' purchase from vendor: ' . $vendor->name . ' - Purchase #' . $purchase->id,
                'category' => 'purchase',
                'status' => 'completed',
            ]);
        }

        // Generate success message based on purchase type
        if ($isCashPayment) {
            $successMessage = 'Cash purchase created and payment completed successfully';
        } elseif ($validated['purchase_type'] === 'cheque') {
            $successMessage = 'Purchase created successfully and cheque details recorded in Cheque Management';
        } else {
            $successMessage = 'Credit purchase created successfully';
        }

        return redirect()->route('purchases.show', $purchase->id)
                        ->with('success', $successMessage);
    }

    /**
     * Display the specified credit purchase
     */
    public function show(CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $creditPurchase->load(['createdBy', 'payments.createdBy']);
        return view($this->resolveView('show'), compact('creditPurchase'));
    }

    /**
     * Show the form for editing the specified credit purchase
     */
    public function edit(CreditPurchase $creditPurchase)
    {
        // Staff members cannot edit purchases
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to edit purchases.');
        }

        // Check authorization
        if ($creditPurchase->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        return view($this->resolveView('edit'), compact('creditPurchase'));
    }

    /**
     * Update the specified credit purchase
     */
    public function update(Request $request, CreditPurchase $creditPurchase)
    {
        // Staff members cannot update purchases
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update purchases.');
        }

        // Check authorization
        if ($creditPurchase->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date|before_or_equal:today',
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

        // Recalculate status based on due amount
        $status = 'pending';
        if ($creditPurchase->paid_amount > 0 && $newDueAmount > 0) {
            $status = 'partial';
        } elseif ($newDueAmount <= 0) {
            $status = 'paid';
            $newDueAmount = 0;
        }

        $creditPurchase->update([
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
            $vendor = \App\Models\Vendor::where('shop_id', $this->currentUser()->shop_id)
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

        return redirect()->route('purchases.show', $creditPurchase->id)
                        ->with('success', 'Credit purchase updated successfully');
    }

    /**
     * Delete a credit purchase (only shop owners and managers)
     */
    public function destroy(CreditPurchase $creditPurchase)
    {
        // Check authorization
        $user = $this->currentUser();

        if ($creditPurchase->shop_id !== $user->shop_id) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($user->role, ['shop_owner', 'manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to delete purchases.');
        }

        try {
            DB::beginTransaction();

            // Store purchase data for audit log
            $purchaseData = $creditPurchase->toArray();
            $purchaseData['payments'] = $creditPurchase->payments->toArray();

            // Delete associated payments and reverse their effect
            foreach ($creditPurchase->payments as $payment) {
                // Store payment data before deletion
                $paymentData = $payment->toArray();

                // Delete the payment
                $payment->delete();

                // Log the payment deletion
                AuditLog::log(
                    'delete',
                    'CreditPurchasePayment',
                    $payment->id,
                    "Payment of {$payment->payment_amount} deleted as part of purchase #{$creditPurchase->id} deletion",
                    $paymentData
                );
            }

            // Delete associated cheques
            $cheques = Cheque::where('shop_id', $user->shop_id)
                ->where('related_to', 'vendor_payment')
                ->where('related_id', $creditPurchase->id)
                ->get();

            foreach ($cheques as $cheque) {
                $chequeData = $cheque->toArray();
                $cheque->delete();

                AuditLog::log(
                    'delete',
                    'Cheque',
                    $cheque->id,
                    "Cheque #{$cheque->cheque_number} deleted as part of purchase #{$creditPurchase->id} deletion",
                    $chequeData
                );
            }

            // Delete associated business transactions
            $transactions = BusinessTransaction::where('shop_id', $user->shop_id)
                ->where('description', 'like', "%Purchase #{$creditPurchase->id}%")
                ->get();

            foreach ($transactions as $transaction) {
                $transactionData = $transaction->toArray();
                $transaction->delete();

                AuditLog::log(
                    'delete',
                    'BusinessTransaction',
                    $transaction->id,
                    "Business transaction deleted as part of purchase #{$creditPurchase->id} deletion",
                    $transactionData
                );
            }

            // Log the main purchase deletion
            AuditLog::log(
                'delete',
                'CreditPurchase',
                $creditPurchase->id,
                "Purchase #{$creditPurchase->id} from vendor {$creditPurchase->vendor_name} (Amount: {$creditPurchase->total_amount}) deleted by " . $user->name,
                $purchaseData
            );

            // Store vendor info before deleting
            $vendorId = $creditPurchase->vendor_id;
            $vendorName = $creditPurchase->vendor_name;

            // Delete the purchase (soft delete)
            $creditPurchase->delete();

            // Update vendor balance after deletion
            $vendor = null;
            if ($vendorId) {
                $vendor = \App\Models\Vendor::find($vendorId);
            } else {
                // Try to find vendor by name if vendor_id is not set
                $vendor = \App\Models\Vendor::where('shop_id', $user->shop_id)
                                             ->where('name', $vendorName)
                                             ->first();
            }

            if ($vendor) {
                $vendor->updateBalances();
            }

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully. All related payments and transactions have been removed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }

    /**
     * Record a payment for the credit purchase
     */
    public function recordPayment(Request $request, CreditPurchase $creditPurchase)
    {
        // Check authorization
        if ($creditPurchase->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:Cash,Check,Bank Transfer,Card,Other',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'create_transaction' => 'nullable|boolean',
        ]);

        // Ensure payment doesn't exceed remaining due amount
        if ($validated['payment_amount'] > $creditPurchase->due_amount) {
            return back()->withErrors(['payment_amount' => 'Payment amount exceeds remaining balance']);
        }

        // Record the payment
        $payment = CreditPurchasePayment::create([
            'credit_purchase_id' => $creditPurchase->id,
            'created_by' => auth()->id(),
            'shop_id' => $creditPurchase->shop_id,
            'payment_amount' => $validated['payment_amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'],
            'notes' => $validated['notes'],
        ]);

        // Create linked business transaction if requested
        if ($request->input('create_transaction', false)) {
            BusinessTransaction::create([
                'shop_id' => $creditPurchase->shop_id,
                'created_by' => auth()->id(),
                'transaction_date' => $validated['payment_date'],
                'transaction_type' => 'vendor_payment',
                'vendor_name' => $creditPurchase->vendor_name,
                'receipt_number' => $validated['payment_reference'],
                'reference_number' => "CP-{$creditPurchase->id}",
                'paid_by' => $validated['payment_method'],
                'total_amount' => $validated['payment_amount'],
                'discount_amount' => 0,
                'net_amount' => $validated['payment_amount'],
                'description' => $validated['notes'] ?? "Payment for purchase from {$creditPurchase->vendor_name}",
                'category' => 'Purchase Payment',
                'status' => 'completed',
                'items' => json_encode([[
                    'description' => "Payment for Credit Purchase #{$creditPurchase->id}",
                    'amount' => $validated['payment_amount'],
                    'credit_purchase_id' => $creditPurchase->id,
                    'payment_id' => $payment->id,
                ]]),
            ]);
        }

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
            $vendor = \App\Models\Vendor::where('shop_id', $this->currentUser()->shop_id)
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
        $shopId = $this->currentUser()->shop_id;
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
