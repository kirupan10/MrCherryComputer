<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Http\Controllers\Controller;
use App\ShopTypes\Tech\Models\TechProduct;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Traits\HasShopFeatures;
use Picqer\Barcode\BarcodeGeneratorSVG;

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

        return view('products.index', compact('products', 'categories', 'productCards'));
    }

    public function create()
    {
        $shop = $this->getCurrentShop();

        $categories = Category::where('shop_id', $shop->id)->orderBy('name')->get();
        $units = Unit::query()
            ->whereNull('shop_id')
            ->orWhere('shop_id', $shop->id)
            ->orderBy('name')
            ->get();
        $warranties = Warranty::where('shop_id', $shop->id)->orderBy('name')->get();

        return view('products.create', compact('categories', 'units', 'warranties'));
    }

    public function store(Request $request)
    {
        $shop = $this->getCurrentShop();
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:' . $productTable . ',code',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'warranty_id' => 'nullable|exists:warranties,id',
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

        if (empty($validated['code'])) {
            $validated['code'] = $this->generateProductCode($productTable, (int) $shop->id);
        }
        $validated['slug'] = $this->generateProductSlug($productTable, (string) $validated['name'], (int) $shop->id);
        $validated['shop_id'] = $shop->id;
        $validated['created_by'] = auth()->id();

        $product = TechProduct::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tech product created successfully',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'slug' => $product->slug,
                    'selling_price' => $product->selling_price,
                    'buying_price' => $product->buying_price,
                    'quantity' => $product->quantity,
                    'unit_id' => $product->unit_id,
                ],
            ]);
        }

        return redirect()
            ->route('tech.products.show', $product)
            ->with('success', 'Tech product created successfully');
    }

    public function show(TechProduct $product)
    {
        $this->authorize('view', $product);

        $product->load(['category', 'unit', 'serialNumbers', 'warrantyClaims', 'repairJobs']);

        $generator = new BarcodeGeneratorSVG();
        $barcodeValue = $product->barcode ?? $product->code;
        $barcodeType = ($product->barcode && strlen($product->barcode) == 12)
            ? $generator::TYPE_EAN_13
            : $generator::TYPE_CODE_128;
        $barcode = $generator->getBarcode($barcodeValue, $barcodeType, 3, 80);

        return view('products.show', compact('product', 'barcode'));
    }

    public function edit(TechProduct $product)
    {
        $this->authorize('update', $product);

        $shop = $this->getCurrentShop();
        $categories = Category::where('shop_id', $shop->id)->orderBy('name')->get();
        $units = Unit::query()
            ->whereNull('shop_id')
            ->orWhere('shop_id', $shop->id)
            ->orderBy('name')
            ->get();
        $warranties = Warranty::where('shop_id', $shop->id)->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units', 'warranties'));
    }

    public function update(Request $request, TechProduct $product)
    {
        $this->authorize('update', $product);
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:' . $productTable . ',code,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'warranty_id' => 'nullable|exists:warranties,id',
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

        if (empty($validated['code'])) {
            $validated['code'] = $product->code ?: $this->generateProductCode($productTable, (int) $product->shop_id);
        }
        $validated['slug'] = $this->generateProductSlug(
            $productTable,
            (string) $validated['name'],
            (int) $product->shop_id,
            (int) $product->id
        );

        if (Schema::hasColumn($productTable, 'updated_by')) {
            $validated['updated_by'] = auth()->id();
        }

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

    private function generateProductCode(string $table, int $shopId): string
    {
        $lastCode = DB::table($table)
            ->where('shop_id', $shopId)
            ->where('code', 'like', 'PRD%')
            ->orderByDesc('id')
            ->value('code');

        $nextNumber = 1;
        if ($lastCode && preg_match('/^PRD(\d+)/', $lastCode, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'PRD' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    private function generateProductSlug(string $table, string $name, int $shopId, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if ($baseSlug === '') {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = DB::table($table)
                ->where('shop_id', $shopId)
                ->where('slug', $slug);

            if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }
}
