<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Http\Controllers\Controller;
use App\ShopTypes\Tech\Models\TechWarrantyClaim;
use App\ShopTypes\Tech\Models\TechSerialNumber;
use App\ShopTypes\Tech\Models\TechProduct;
use App\Models\Customer;
use App\Traits\HasShopFeatures;
use Illuminate\Http\Request;

class TechWarrantyController extends Controller
{
    use HasShopFeatures;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('shop.tenant');
        $this->requireShopFeature('warranty');
    }

    /**
     * Display a listing of warranty claims.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TechWarrantyClaim::class);

        $query = TechWarrantyClaim::with(['product', 'serialNumber', 'customer'])
            ->forCurrentShop();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('serialNumber', function ($q) use ($search) {
                        $q->where('serial_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('claim_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('claim_date', '<=', $request->to_date);
        }

        $claims = $query->latest('claim_date')->paginate(20);

        return view('shop-types.tech.warranty.index', compact('claims'));
    }

    /**
     * Show the form for creating a new warranty claim.
     */
    public function create(Request $request)
    {
        $this->authorize('create', TechWarrantyClaim::class);

        $customers = Customer::forCurrentShop()->orderBy('name')->get();

        $selectedSerial = null;
        if ($request->filled('serial_number_id')) {
            $selectedSerial = TechSerialNumber::forCurrentShop()
                ->with('product')
                ->findOrFail($request->serial_number_id);
        }

        return view('shop-types.tech.warranty.create', compact('customers', 'selectedSerial'));
    }

    /**
     * Store a newly created warranty claim.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TechWarrantyClaim::class);
        $serialNumberTable = (new TechSerialNumber())->getTable();

        $validated = $request->validate([
            'tech_serial_number_id' => 'required|exists:' . $serialNumberTable . ',id',
            'customer_id' => 'required|exists:customers,id',
            'claim_date' => 'required|date',
            'issue_description' => 'required|string',
            'customer_complaint' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,in_progress,completed',
            'resolution_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        // Get serial number and product
        $serialNumber = TechSerialNumber::forCurrentShop()
            ->findOrFail($validated['tech_serial_number_id']);

        // Check if product is under warranty
        if (!$serialNumber->isUnderWarranty()) {
            return back()
                ->withInput()
                ->withErrors(['tech_serial_number_id' => 'This product is not under warranty.']);
        }

        // Generate claim number
        $lastClaim = TechWarrantyClaim::forCurrentShop()
            ->latest('id')
            ->first();

        $claimNumber = 'WC-' . str_pad(($lastClaim ? $lastClaim->id + 1 : 1), 6, '0', STR_PAD_LEFT);

        $validated['shop_id'] = auth()->user()->currentShop->id;
        $validated['tech_product_id'] = $serialNumber->tech_product_id;
        $validated['claim_number'] = $claimNumber;
        $validated['created_by'] = auth()->id();

        $claim = TechWarrantyClaim::create($validated);

        return redirect()
            ->route('tech.warranty.show', $claim)
            ->with('success', 'Warranty claim created successfully.');
    }

    /**
     * Display the specified warranty claim.
     */
    public function show(TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('view', $warrantyClaim);

        $warrantyClaim->load([
            'product',
            'serialNumber',
            'customer',
            'createdBy',
            'approvedBy',
            'completedBy'
        ]);

        return view('shop-types.tech.warranty.show', compact('warrantyClaim'));
    }

    /**
     * Show the form for editing the warranty claim.
     */
    public function edit(TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('update', $warrantyClaim);

        // Cannot edit completed or rejected claims
        if (in_array($warrantyClaim->status, ['completed', 'rejected'])) {
            return back()->withErrors(['error' => 'Cannot edit completed or rejected warranty claims.']);
        }

        $customers = Customer::forCurrentShop()->orderBy('name')->get();

        return view('shop-types.tech.warranty.edit', compact('warrantyClaim', 'customers'));
    }

    /**
     * Update the specified warranty claim.
     */
    public function update(Request $request, TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('update', $warrantyClaim);

        // Cannot update completed or rejected claims
        if (in_array($warrantyClaim->status, ['completed', 'rejected'])) {
            return back()->withErrors(['error' => 'Cannot update completed or rejected warranty claims.']);
        }

        $validated = $request->validate([
            'issue_description' => 'required|string',
            'customer_complaint' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,in_progress,completed',
            'resolution_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $warrantyClaim->update($validated);

        return redirect()
            ->route('tech.warranty.show', $warrantyClaim)
            ->with('success', 'Warranty claim updated successfully.');
    }

    /**
     * Approve the warranty claim.
     */
    public function approve(Request $request, TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('update', $warrantyClaim);

        if ($warrantyClaim->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending claims can be approved.']);
        }

        $validated = $request->validate([
            'resolution_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $warrantyClaim->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'resolution_notes' => $validated['resolution_notes'] ?? $warrantyClaim->resolution_notes,
            'estimated_cost' => $validated['estimated_cost'] ?? $warrantyClaim->estimated_cost,
        ]);

        return back()->with('success', 'Warranty claim approved successfully.');
    }

    /**
     * Reject the warranty claim.
     */
    public function reject(Request $request, TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('update', $warrantyClaim);

        if ($warrantyClaim->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending claims can be rejected.']);
        }

        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $warrantyClaim->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'resolution_notes' => $validated['resolution_notes'],
        ]);

        return back()->with('success', 'Warranty claim rejected.');
    }

    /**
     * Complete the warranty claim.
     */
    public function complete(Request $request, TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('update', $warrantyClaim);

        if (!in_array($warrantyClaim->status, ['approved', 'in_progress'])) {
            return back()->withErrors(['error' => 'Only approved or in-progress claims can be completed.']);
        }

        $validated = $request->validate([
            'resolution_notes' => 'required|string',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $warrantyClaim->update([
            'status' => 'completed',
            'completed_by' => auth()->id(),
            'completed_at' => now(),
            'resolution_notes' => $validated['resolution_notes'],
            'actual_cost' => $validated['actual_cost'] ?? $warrantyClaim->actual_cost,
        ]);

        return back()->with('success', 'Warranty claim completed successfully.');
    }

    /**
     * Remove the specified warranty claim.
     */
    public function destroy(TechWarrantyClaim $warrantyClaim)
    {
        $this->authorize('delete', $warrantyClaim);

        // Can only delete pending claims
        if ($warrantyClaim->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending warranty claims can be deleted.']);
        }

        $warrantyClaim->delete();

        return redirect()
            ->route('tech.warranty.index')
            ->with('success', 'Warranty claim deleted successfully.');
    }
}
