<?php

namespace App\Http\Controllers;

use App\Models\WarrantyClaim;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class WarrantyClaimController extends Controller
{
    private function resolveWarrantyView(string $page): string
    {
        $user = auth()->user();
        $shop = $user ? $user->getActiveShop() : null;
        $shopType = $shop && $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : null;

        if ($shopType) {
            $shopView = "shop-types.{$shopType}.warranty.{$page}";
            if (view()->exists($shopView)) {
                return $shopView;
            }
        }

        return "warranty-claims.{$page}";
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $shop = $user->getActiveShop();

        if (!$shop && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        // ShopScope automatically filters by shop_id
        $query = WarrantyClaim::with(['product', 'customer', 'creator']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $phoneSearch = preg_replace('/[\s\-\(\)]+/', '', $search);
            $query->where(function($q) use ($search, $phoneSearch) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search, $phoneSearch) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                      // Additional search with spaces removed
                      if ($phoneSearch !== $search && !empty($phoneSearch)) {
                          $customerQuery->orWhere('phone', 'like', "%{$phoneSearch}%");
                      }
                  })
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('sending_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sending_date', '<=', $request->date_to);
        }

        // Sending method filter
        if ($request->filled('sending_method')) {
            $query->where('sending_method', $request->sending_method);
        }

        $perPage = $request->get('per_page', 20);
        $warrantyClaims = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // Get statistics (ShopScope automatically filters)
        $stats = [
            'total' => WarrantyClaim::count(),
            'pending' => WarrantyClaim::where('status', 'pending')->count(),
            'in_progress' => WarrantyClaim::where('status', 'in_progress')->count(),
            'completed' => WarrantyClaim::where('status', 'completed')->count(),
        ];

        return view($this->resolveWarrantyView('index'), compact('warrantyClaims', 'stats'));
    }

    public function create()
    {
        // Load ALL products including out-of-stock items for warranty claims
        // Warranty claims can be filed for products regardless of current stock status
        $products = Product::where('shop_id', Auth::user()->shop_id)
            ->orderBy('name')
            ->get();
        $customers = Customer::where('shop_id', Auth::user()->shop_id)
            ->orderBy('name')
            ->get();
        return view($this->resolveWarrantyView('create'), compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'serial_number' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'sending_date' => 'nullable|date',
            'sending_method' => 'required|in:courier,handover,bus',
            'tracking_number' => 'nullable|string|max:255',
            'claim_receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'issue_description' => 'required|string',
            'status' => 'required|in:pending,sent,in_progress,repaired,replaced,rejected,completed',
            'expected_return_date' => 'nullable|date',
            'actual_return_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $shop = $user->getActiveShop() ?: \App\Models\Shop::first();

        $validated['shop_id'] = $shop?->id;
        $validated['created_by'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('claim_receipt_file')) {
            $file = $request->file('claim_receipt_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('warranty_claims', $filename, 'public');
            $validated['claim_receipt_file'] = $path;
        }

        WarrantyClaim::create($validated);

        return redirect()->route('warranty-claims.index')
            ->with('success', 'Warranty claim created successfully.');
    }

    public function show(WarrantyClaim $warrantyClaim)
    {
        $warrantyClaim->load(['product', 'customer', 'order', 'creator']);

        return view($this->resolveWarrantyView('show'), compact('warrantyClaim'));
    }

    public function edit(WarrantyClaim $warrantyClaim)
    {
        $products = Product::where('shop_id', Auth::user()->shop_id)
            ->orderBy('name')
            ->get();
        $customers = Customer::where('shop_id', Auth::user()->shop_id)
            ->orderBy('name')
            ->get();
        return view($this->resolveWarrantyView('edit'), compact('warrantyClaim', 'products', 'customers'));
    }

    public function update(Request $request, WarrantyClaim $warrantyClaim)
    {
        // Authorization check
        if ($warrantyClaim->shop_id && $warrantyClaim->shop_id !== $request->user()->shop_id) {
            abort(403, 'Unauthorized access to this warranty claim.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'serial_number' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'sending_date' => 'nullable|date',
            'sending_method' => 'required|in:courier,handover,bus',
            'tracking_number' => 'nullable|string|max:255',
            'claim_receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'issue_description' => 'required|string',
            'status' => 'required|in:pending,sent,in_progress,repaired,replaced,rejected,completed',
            'expected_return_date' => 'nullable|date',
            'actual_return_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('claim_receipt_file')) {
            // Delete old file if exists
            if ($warrantyClaim->claim_receipt_file) {
                Storage::disk('public')->delete($warrantyClaim->claim_receipt_file);
            }

            $file = $request->file('claim_receipt_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('warranty_claims', $filename, 'public');
            $validated['claim_receipt_file'] = $path;
        }

        $warrantyClaim->update($validated);

        return redirect()->route('warranty-claims.index')
            ->with('success', 'Warranty claim updated successfully.');
    }

    public function destroy(WarrantyClaim $warrantyClaim)
    {
        // Authorization check
        if ($warrantyClaim->shop_id && $warrantyClaim->shop_id !== request()->user()->shop_id) {
            abort(403, 'Unauthorized access to this warranty claim.');
        }

        // Delete associated file if exists
        if ($warrantyClaim->claim_receipt_file) {
            Storage::disk('public')->delete($warrantyClaim->claim_receipt_file);
        }

        $warrantyClaim->delete();

        return redirect()->route('warranty-claims.index')
            ->with('success', 'Warranty claim deleted successfully.');
    }

    // API endpoint to search products
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        $user = auth()->user();
        $shop = $user->getActiveShop() ?: \App\Models\Shop::first();

        $products = Product::where('shop_id', $shop?->id)
            ->where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'code']);

        return response()->json($products);
    }

    // API endpoint to search customers
    public function searchCustomers(Request $request)
    {
        $search = $request->get('q', '');
        $phoneSearch = preg_replace('/[\s\-\(\)]+/', '', $search);
        $user = auth()->user();
        $shop = $user->getActiveShop() ?: \App\Models\Shop::first();

        $customers = Customer::where('shop_id', $shop?->id)
            ->where(function($q) use ($search, $phoneSearch) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
                // Additional search with spaces removed
                if ($phoneSearch !== $search && !empty($phoneSearch)) {
                    $q->orWhere('phone', 'like', "%{$phoneSearch}%");
                }
            })
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    // Get serial numbers for a product
    public function getProductSerialNumbers(Request $request, $productId)
    {
        $serialNumbers = \App\Models\OrderDetails::where('product_id', $productId)
            ->whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->distinct()
            ->pluck('serial_number');

        return response()->json($serialNumbers);
    }
}

