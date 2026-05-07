<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Http\Controllers\Controller;
use App\ShopTypes\Tech\Models\TechSerialNumber;
use App\ShopTypes\Tech\Models\TechProduct;
use App\Traits\HasShopFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechSerialNumberController extends Controller
{
    use HasShopFeatures;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('shop.tenant');
        $this->requireShopFeature('products');
        $this->requireShopFeature('serial_numbers');
    }

    /**
     * Display a listing of serial numbers.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TechSerialNumber::class);

        $query = TechSerialNumber::with(['product', 'customer'])
            ->forCurrentShop();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('imei_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('tech_product_id', $request->product_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warranty
        if ($request->filled('warranty_status')) {
            if ($request->warranty_status === 'active') {
                $query->whereNotNull('warranty_start_date')
                    ->whereNotNull('warranty_end_date')
                    ->where('warranty_end_date', '>=', now());
            } elseif ($request->warranty_status === 'expired') {
                $query->whereNotNull('warranty_end_date')
                    ->where('warranty_end_date', '<', now());
            }
        }

        $serialNumbers = $query->latest()->paginate(20);

        return view('shop-types.tech.serial-numbers.index', compact('serialNumbers'));
    }

    /**
     * Show serial numbers for a specific product.
     */
    public function byProduct(TechProduct $product)
    {
        $this->authorize('view', $product);

        $serialNumbers = $product->serialNumbers()
            ->with('customer')
            ->latest()
            ->paginate(20);

        return view('shop-types.tech.serial-numbers.by-product', compact('product', 'serialNumbers'));
    }

    /**
     * Show the form for creating a new serial number.
     */
    public function create(Request $request)
    {
        $this->authorize('create', TechSerialNumber::class);

        $products = TechProduct::forCurrentShop()
            ->where('track_serial_numbers', true)
            ->orderBy('name')
            ->get();

        $selectedProduct = null;
        if ($request->filled('product_id')) {
            $selectedProduct = TechProduct::forCurrentShop()
                ->findOrFail($request->product_id);
        }

        return view('shop-types.tech.serial-numbers.create', compact('products', 'selectedProduct'));
    }

    /**
     * Store a newly created serial number.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TechSerialNumber::class);
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'tech_product_id' => 'required|exists:' . $productTable . ',id',
            'serial_number' => 'required|string|max:255',
            'imei_number' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'status' => 'required|in:in_stock,sold,returned,defective',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'warranty_start_date' => 'nullable|date',
            'warranty_end_date' => 'nullable|date|after_or_equal:warranty_start_date',
            'notes' => 'nullable|string',
        ]);

        // Check for duplicate serial number
        $exists = TechSerialNumber::forCurrentShop()
            ->where('serial_number', $validated['serial_number'])
            ->where('tech_product_id', $validated['tech_product_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => 'This serial number already exists for this product.']);
        }

        $validated['shop_id'] = auth()->user()->currentShop->id;

        $serialNumber = TechSerialNumber::create($validated);

        return redirect()
            ->route('tech.serial-numbers.show', $serialNumber)
            ->with('success', 'Serial number added successfully.');
    }

    /**
     * Bulk import serial numbers.
     */
    public function bulkImport(Request $request)
    {
        $this->authorize('create', TechSerialNumber::class);
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'tech_product_id' => 'required|exists:' . $productTable . ',id',
            'serial_numbers' => 'required|string',
            'status' => 'required|in:in_stock,sold,returned,defective',
            'warranty_months' => 'nullable|integer|min:0',
        ]);

        $product = TechProduct::forCurrentShop()->findOrFail($validated['tech_product_id']);

        if (!$product->track_serial_numbers) {
            return back()->withErrors(['tech_product_id' => 'Serial number tracking is not enabled for this product.']);
        }

        $serialNumbers = array_filter(
            array_map('trim', explode("\n", $validated['serial_numbers']))
        );

        $imported = 0;
        $errors = [];

        DB::transaction(function () use ($serialNumbers, $validated, $product, &$imported, &$errors) {
            foreach ($serialNumbers as $serial) {
                // Skip if already exists
                if (TechSerialNumber::forCurrentShop()
                    ->where('serial_number', $serial)
                    ->where('tech_product_id', $product->id)
                    ->exists()
                ) {
                    $errors[] = "Duplicate: {$serial}";
                    continue;
                }

                $data = [
                    'shop_id' => auth()->user()->currentShop->id,
                    'tech_product_id' => $product->id,
                    'serial_number' => $serial,
                    'status' => $validated['status'],
                ];

                // Set warranty dates if specified
                if (!empty($validated['warranty_months'])) {
                    $data['warranty_start_date'] = now();
                    $data['warranty_end_date'] = now()->addMonths($validated['warranty_months']);
                }

                TechSerialNumber::create($data);
                $imported++;
            }
        });

        $message = "Successfully imported {$imported} serial number(s).";
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' duplicate(s) skipped.';
        }

        return back()->with('success', $message);
    }

    /**
     * Display the specified serial number.
     */
    public function show(TechSerialNumber $serialNumber)
    {
        $this->authorize('view', $serialNumber);

        $serialNumber->load(['product', 'customer', 'order', 'warrantyClaims', 'repairJobs']);

        return view('shop-types.tech.serial-numbers.show', compact('serialNumber'));
    }

    /**
     * Show the form for editing the serial number.
     */
    public function edit(TechSerialNumber $serialNumber)
    {
        $this->authorize('update', $serialNumber);

        $products = TechProduct::forCurrentShop()
            ->where('track_serial_numbers', true)
            ->orderBy('name')
            ->get();

        return view('shop-types.tech.serial-numbers.edit', compact('serialNumber', 'products'));
    }

    /**
     * Update the specified serial number.
     */
    public function update(Request $request, TechSerialNumber $serialNumber)
    {
        $this->authorize('update', $serialNumber);
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'tech_product_id' => 'required|exists:' . $productTable . ',id',
            'serial_number' => 'required|string|max:255',
            'imei_number' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'status' => 'required|in:in_stock,sold,returned,defective',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'warranty_start_date' => 'nullable|date',
            'warranty_end_date' => 'nullable|date|after_or_equal:warranty_start_date',
            'notes' => 'nullable|string',
        ]);

        // Check for duplicate serial number (excluding current)
        $exists = TechSerialNumber::forCurrentShop()
            ->where('serial_number', $validated['serial_number'])
            ->where('tech_product_id', $validated['tech_product_id'])
            ->where('id', '!=', $serialNumber->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => 'This serial number already exists for this product.']);
        }

        $serialNumber->update($validated);

        return redirect()
            ->route('tech.serial-numbers.show', $serialNumber)
            ->with('success', 'Serial number updated successfully.');
    }

    /**
     * Remove the specified serial number.
     */
    public function destroy(TechSerialNumber $serialNumber)
    {
        $this->authorize('delete', $serialNumber);

        // Check if serial number is associated with orders, claims, or repairs
        if ($serialNumber->order_id ||
            $serialNumber->warrantyClaims()->count() > 0 ||
            $serialNumber->repairJobs()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete serial number that has associated orders, warranty claims, or repair jobs.']);
        }

        $serialNumber->delete();

        return redirect()
            ->route('tech.serial-numbers.index')
            ->with('success', 'Serial number deleted successfully.');
    }
}
