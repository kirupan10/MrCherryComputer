<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\CreditPurchase;
use App\Models\CreditPurchasePayment;
use App\Models\BusinessTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    private function resolveView(string $page): string
    {
        return "vendors.{$page}";
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Display a listing of vendors
     */
    public function index(Request $request)
    {
        $shopId = $this->currentUser()->shop_id;
        $search = $request->get('search');
        $purchaseStatus = $request->get('purchase_status', 'pending'); // Default to pending

        $query = Vendor::where('shop_id', $shopId)->with('creator');

        // Search by name, company, phone, or email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by purchase status based on outstanding_balance
        if ($purchaseStatus === 'pending') {
            // Show only vendors with pending balance (outstanding_balance > 0)
            $query->where('outstanding_balance', '>', 0);
        } elseif ($purchaseStatus === 'completed') {
            // Show only vendors with no outstanding balance (outstanding_balance = 0)
            $query->where('outstanding_balance', '=', 0);
        }
        // If 'all' is selected, show all vendors without balance filter

        // Order by pending balance first, then by name
        $vendors = $query->orderByRaw('CASE WHEN outstanding_balance > 0 THEN 0 ELSE 1 END')
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->appends($request->all());

        // Calculate totals
        $stats = [
            'total_vendors' => Vendor::where('shop_id', $shopId)->count(),
            'pending_vendors' => Vendor::where('shop_id', $shopId)->where('outstanding_balance', '>', 0)->count(),
            'total_outstanding' => Vendor::where('shop_id', $shopId)->sum('outstanding_balance'),
        ];

        return view($this->resolveView('index'), compact('vendors', 'stats', 'search', 'purchaseStatus'));
    }

    /**
     * Show the form for creating a new vendor
     */
    public function create()
    {
        return view($this->resolveView('create'));
    }

    /**
     * Store a newly created vendor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $shopId = $this->currentUser()->shop_id;
        $createdBy = auth()->id();

        $vendor = Vendor::create([
            'shop_id' => $shopId,
            'created_by' => $createdBy,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'tax_number' => $validated['tax_number'] ?? null,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Handle AJAX requests (from modal)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'phone' => $vendor->phone,
                    'email' => $vendor->email,
                    'address' => $vendor->address,
                    'company_name' => $vendor->company_name,
                ],
                'message' => 'Supplier created successfully',
            ]);
        }

        return redirect()->route('vendors.show', $vendor->id)
            ->with('success', 'Supplier created successfully');
    }

    /**
     * Display the specified vendor
     */
    public function show(Vendor $vendor)
    {
        // Check authorization
        if ($vendor->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        // Load vendor with creator and credit purchases sorted by status and date
        $vendor->load(['creator']);

        // Load credit purchases ordered by status priority (pending, partial, paid), then by date
        $vendor->setRelation('creditPurchases',
            $vendor->creditPurchases()
                ->orderByRaw("CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'partial' THEN 2
                    WHEN status = 'paid' THEN 3
                    ELSE 4
                END")
                ->orderBy('purchase_date', 'desc')
                ->orderBy('id', 'desc')
                ->with('payments')
                ->get()
        );

        // Get purchase statistics
        $purchaseStats = [
            'total_purchases' => $vendor->creditPurchases()->count(),
            'pending_purchases' => $vendor->creditPurchases()->whereIn('status', ['pending', 'partial'])->count(),
            'total_amount' => $vendor->total_purchases,
            'total_paid' => $vendor->total_paid,
            'outstanding' => $vendor->outstanding_balance,
        ];

        return view($this->resolveView('show'), compact('vendor', 'purchaseStats'));
    }

    /**
     * Show the form for editing the vendor
     */
    public function edit(Vendor $vendor)
    {
        // Staff members cannot edit vendors
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to edit suppliers.');
        }

        // Check authorization
        if ($vendor->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        return view($this->resolveView('edit'), compact('vendor'));
    }

    /**
     * Update the specified vendor
     */
    public function update(Request $request, Vendor $vendor)
    {
        // Staff members cannot update vendors
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update suppliers.');
        }

        // Check authorization
        if ($vendor->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.show', $vendor->id)
            ->with('success', 'Supplier updated successfully');
    }

    /**
     * Remove the specified vendor
     */
    public function destroy(Vendor $vendor)
    {
        // Staff members cannot delete vendors
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to delete suppliers.');
        }

        // Check authorization
        if ($vendor->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        // Check if vendor has associated purchases
        if ($vendor->creditPurchases()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with existing purchases');
        }

        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Supplier deleted successfully');
    }

    /**
     * Search vendors for autocomplete
     */
    public function search(Request $request)
    {
        $shopId = $this->currentUser()->shop_id;
        $search = $request->get('q', '');

        $vendors = Vendor::where('shop_id', $shopId)
            ->where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'company_name', 'phone', 'email', 'address']);

        return response()->json($vendors);
    }

    /**
     * Record a payment for a vendor with FIFO allocation
     */
    public function recordPayment(Request $request, Vendor $vendor)
    {
        // Check authorization
        if ($vendor->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $vendor->outstanding_balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Get the payment amount
            $remainingPayment = (float) $validated['amount'];
            $allocatedPurchases = [];

            // Get all credit purchases for this vendor with outstanding balance (FIFO - oldest first)
            $creditPurchases = CreditPurchase::where('vendor_id', $vendor->id)
                ->where('shop_id', $this->currentUser()->shop_id)
                ->where('due_amount', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Allocate payment to purchases using FIFO method
            foreach ($creditPurchases as $purchase) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $dueAmount = (float) $purchase->due_amount;

                // Calculate how much to allocate to this purchase
                $allocationAmount = min($remainingPayment, $dueAmount);

                // Create payment record for this purchase
                CreditPurchasePayment::create([
                    'credit_purchase_id' => $purchase->id,
                    'shop_id' => $this->currentUser()->shop_id,
                    'created_by' => auth()->id(),
                    'payment_amount' => $allocationAmount,
                    'payment_date' => $validated['payment_date'],
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => $validated['reference_number'],
                    'notes' => $validated['notes'] ?? 'Payment from vendor: ' . $vendor->name,
                ]);

                // Update purchase amounts
                $purchase->paid_amount = bcadd($purchase->paid_amount ?? '0', $allocationAmount, 2);
                $purchase->due_amount = bcsub($purchase->due_amount ?? '0', $allocationAmount, 2);

                // Update purchase status
                if ($purchase->due_amount <= 0) {
                    $purchase->status = 'paid';
                } elseif ($purchase->paid_amount > 0) {
                    $purchase->status = 'partial';
                }

                $purchase->save();

                // Track for success message
                $allocatedPurchases[] = [
                    'id' => $purchase->id,
                    'reference' => $purchase->reference_number ?? 'Purchase #' . $purchase->id,
                    'amount' => $allocationAmount,
                ];

                // Reduce remaining payment
                $remainingPayment -= $allocationAmount;
            }

            // Update vendor balances using DB query to bypass strict type casting
            DB::table('vendors')
                ->where('id', $vendor->id)
                ->update([
                    'total_paid' => DB::raw('total_paid + ' . $validated['amount']),
                    'outstanding_balance' => DB::raw('outstanding_balance - ' . $validated['amount']),
                    'updated_at' => now(),
                ]);

            // Refresh vendor model to get updated values
            $vendor->refresh();

            // Record in business transactions
            BusinessTransaction::create([
                'shop_id' => $this->currentUser()->shop_id,
                'created_by' => auth()->id(),
                'transaction_date' => $validated['payment_date'],
                'transaction_type' => 'vendor_payment',
                'vendor_name' => $vendor->name,
                'reference_number' => $validated['reference_number'] ?? null,
                'paid_by' => $validated['payment_method'],
                'paid_by_user_id' => auth()->id(),
                'total_amount' => $validated['amount'],
                'net_amount' => $validated['amount'],
                'description' => 'Payment to vendor: ' . $vendor->name . (' - ' . ($validated['notes'] ?? 'Vendor payment')),
                'category' => 'vendor_payment',
                'status' => 'completed',
            ]);

            DB::commit();

            // Build success message with allocation details
            $allocationDetails = [];
            foreach ($allocatedPurchases as $allocated) {
                $allocationDetails[] = $allocated['reference'] . ': LKR ' . number_format($allocated['amount'], 2);
            }

            $message = 'Payment of LKR ' . number_format($validated['amount'], 2) . ' recorded successfully.';
            if (!empty($allocationDetails)) {
                $message .= ' Allocated to: ' . implode(', ', $allocationDetails);
            }

            return redirect()->route('vendors.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('vendors.index')
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }
}
