<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Http\Controllers\Controller;
use App\ShopTypes\Tech\Models\TechProduct;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warranty;
use Illuminate\Http\Request;
use App\Traits\HasShopFeatures;

class TechProductController extends Controller
{
    use HasShopFeatures;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->requireShopFeature('products');
            return $next($request);
        });
    }

    public function index()
    {
        $shop = $this->getCurrentShop();

        $products = TechProduct::forShop($shop->id)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->paginate(20);

        $categories = Category::where('shop_id', $shop->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $productCards = [
            'total_products' => TechProduct::forShop($shop->id)->count(),
            'stock_value' => (float) TechProduct::forShop($shop->id)
                ->selectRaw('COALESCE(SUM(quantity * selling_price), 0) as total')
                ->value('total'),
            'low_stock' => TechProduct::forShop($shop->id)
                ->whereColumn('quantity', '<=', 'quantity_alert')
                ->count(),
            'categories' => $categories->count(),
        ];

        return view('shop-types.tech.products.index', compact('products', 'categories', 'productCards'));
    }

    public function create()
    {
        $shop = $this->getCurrentShop();

        $categories = Category::where('shop_id', $shop->id)->orderBy('name')->get();
        $units = Unit::where('shop_id', $shop->id)->orderBy('name')->get();
        $warranties = Warranty::all(['id', 'name', 'duration', 'slug']);

        return view('shop-types.tech.products.create', compact('categories', 'units', 'warranties'));
    }

    public function store(Request $request)
    {
        $shop = $this->getCurrentShop();
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:' . $productTable . ',code',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'quantity' => 'required|integer|min:0',
            'quantity_alert' => 'required|integer|min:0',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'track_serial_numbers' => 'boolean',
            'warranty_months' => 'nullable|integer|min:0',
            'warranty_type' => 'nullable|string|in:manufacturer,store,extended',
            'specifications' => 'nullable|array',
            'is_repairable' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Convert prices to cents
        $validated['buying_price'] = $validated['buying_price'] * 100;
        $validated['selling_price'] = $validated['selling_price'] * 100;
        $validated['shop_id'] = $shop->id;
        $validated['created_by'] = auth()->id();

        $product = TechProduct::create($validated);

        return redirect()
            ->route('tech.products.show', $product)
            ->with('success', 'Tech product created successfully');
    }

    public function show(TechProduct $product)
    {
        $this->authorize('view', $product);

        $product->load(['category', 'unit', 'serialNumbers', 'warrantyClaims', 'repairJobs']);

        return view('shop-types.tech.products.show', compact('product'));
    }

    public function edit(TechProduct $product)
    {
        $this->authorize('update', $product);

        $shop = $this->getCurrentShop();
        $categories = Category::where('shop_id', $shop->id)->orderBy('name')->get();
        $units = Unit::where('shop_id', $shop->id)->orderBy('name')->get();
        $warranties = Warranty::all(['id', 'name', 'duration', 'slug']);

        return view('shop-types.tech.products.edit', compact('product', 'categories', 'units', 'warranties'));
    }

    public function update(Request $request, TechProduct $product)
    {
        $this->authorize('update', $product);
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:' . $productTable . ',code,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'quantity' => 'required|integer|min:0',
            'quantity_alert' => 'required|integer|min:0',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'track_serial_numbers' => 'boolean',
            'warranty_months' => 'nullable|integer|min:0',
            'warranty_type' => 'nullable|string|in:manufacturer,store,extended',
            'specifications' => 'nullable|array',
            'is_repairable' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['buying_price'] = $validated['buying_price'] * 100;
        $validated['selling_price'] = $validated['selling_price'] * 100;
        $validated['updated_by'] = auth()->id();

        $product->update($validated);

        return redirect()
            ->route('tech.products.show', $product)
            ->with('success', 'Tech product updated successfully');
    }

    public function destroy(TechProduct $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()
            ->route('tech.products.index')
            ->with('success', 'Tech product deleted successfully');
    }
}
