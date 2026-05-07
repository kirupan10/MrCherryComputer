<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;
use App\Models\User;
use App\Notifications\PriceUpdateNotification;

class ProductController extends Controller
{
    public function index()
    {
        // Get active shop
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopId = $activeShop ? $activeShop->id : null;

        // Check if there are ANY products for empty state detection
        $hasProducts = Product::exists();

        // Load minimal products only for empty state check
        // The Livewire component handles actual product display
        $products = $hasProducts
            ? Product::with(['category:id,name', 'unit:id,name', 'creator:id,name'])
                ->latest()
                ->limit(1) // Just need 1 to check if not empty
                ->get()
            : collect();

        // Use KpiService to compute lightweight counts (avoids loading collections in view)
        $kpi = new KpiService();

        $cards = [
            'total_products' => $kpi->totalProducts($shopId),
            'in_stock' => $kpi->inStockCount(10, $shopId),
            'stock_value' => $kpi->inStockValue($shopId),
            'low_stock' => $kpi->lowStockCount($shopId),
            'categories' => $kpi->categoriesCount($shopId),
        ];

        // Get all categories for quick access shortcuts
        $categories = Category::all(['id', 'name'])->take(5);

        return view('products.index', [
            'products' => $products,
            'productCards' => $cards,
            'categories' => $categories,
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopType = $activeShop && $activeShop->shop_type ? $activeShop->shop_type->value : 'tech';

        $categories = Category::all(['id', 'name', 'slug']);
        $units = Unit::all(['id', 'name', 'slug']);
        $warranties = \App\Models\Warranty::all(['id', 'name', 'duration', 'slug']);

        if ($request->has('category')) {
            $categories = Category::whereSlug($request->get('category'))->get(['id', 'name', 'slug']);
        }

        if ($request->has('unit')) {
            $units = Unit::whereSlug($request->get('unit'))->get();
        }

        $viewName = "shop-types.{$shopType}.products.create";
        if (!view()->exists($viewName)) {
            $viewName = 'shop-types.tech.products.create';
        }

        return view($viewName, [
            'categories' => $categories,
            'units' => $units,
            'warranties' => $warranties,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        // Remove code validation - let the model auto-generate SKU
        // If code is provided and exists, let the model handle uniqueness
        $existingProduct = null;
        if ($request->filled('code')) {
            $existingProduct = Product::where('code', $request->get('code'))->first();

            if ($existingProduct) {
                // Clear the code to let model auto-generate
                $request->merge(['code' => null]);
            }
        }

        try {
            $product = Product::create($request->all());

            /**
             * Handle image upload
             */
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                // Validate file before uploading
                if ($file->isValid()) {
                    $file->storeAs('products/', $filename, 'public');
                    $product->update([
                        'product_image' => $filename
                    ]);
                } else {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid image file'
                        ], 422);
                    }
                    return back()->withErrors(['product_image' => 'Invalid image file']);
                }
            }

            $message = 'Product has been created with code: ' . $product->code;

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'product' => $product
                ]);
            }

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Product creation error: ' . $e->getMessage());

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong while creating the product'
                ], 500);
            }

            return back()->withErrors(['error' => 'Something went wrong while creating the product']);
        }
    }

    // Helper method to generate a unique product code
    private function generateUniqueCode()
    {
        // Delegate to the model's PRD-based generator
        return Product::generateSku();
    }

    public function show(Product $product)
    {
        // Generate a barcode using SVG for better quality
        $generator = new BarcodeGeneratorSVG();

        // Use the product's barcode number for EAN-13, or code for CODE-128
        $barcodeValue = $product->barcode ?? $product->code;

        // If barcode exists and is 12 digits, use EAN-13, otherwise CODE-128
        $barcodeType = ($product->barcode && strlen($product->barcode) == 12)
            ? $generator::TYPE_EAN_13
            : $generator::TYPE_CODE_128;

        $barcode = $generator->getBarcode($barcodeValue, $barcodeType, 3, 80);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    public function edit(Product $product)
    {
        if (!auth()->user()->hasShopPermission('edit_product')) {
            abort(403, 'You do not have permission to edit products.');
        }

        return view('products.edit', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'warranties' => \App\Models\Warranty::all(['id', 'name', 'duration']),
            'product' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        if (!auth()->user()->hasShopPermission('edit_product')) {
            abort(403, 'You do not have permission to edit products.');
        }

        $data = $request->except('product_image');

        $product->update($data);

        if ($request->hasFile('product_image')) {

            // Delete old image if exists
            if ($product->product_image) {
                \Storage::disk('public')->delete('products/' . $product->product_image);
            }

            // Prepare new image
            $file = $request->file('product_image');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            // Store new image to public storage
            $file->storeAs('products/', $fileName, 'public');

            // Save new image name to database
            $product->update([
                'product_image' => $fileName
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been updated!');
    }

    public function addStock(Request $request, $productSlug)
    {
        try {
            // Find the product by slug instead of ID
            $product = Product::where('slug', $productSlug)->firstOrFail();

            if (!auth()->user()->hasShopPermission('add_stock')) {
                if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'You do not have permission to add stock.'], 403);
                }
                abort(403, 'You do not have permission to add stock.');
            }


            $validated = $request->validate([
                'add_quantity' => 'required|numeric|min:0.001|max:1000000000',
                'notes' => 'nullable|string|max:500',
            ]);

            $oldQuantity = (float) $product->quantity;
            $addQuantity = round((float) $validated['add_quantity'], 3);
            $newQuantity = round($oldQuantity + $addQuantity, 3);

            // Use increment for atomic update (prevents race conditions)
            $product->increment('quantity', $addQuantity);

            $message = "Stock updated successfully! Added {$addQuantity} units. New stock: {$newQuantity}";

            // Always return JSON for AJAX requests
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'new_quantity' => $newQuantity
                ]);
            }

            // Fallback for non-AJAX requests
            return redirect()
                ->route('products.index')
                ->with('success', $message);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Product not found', [
                'product_slug' => $productSlug ?? null,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return redirect()->back()->with('error', 'Product not found');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors());

        } catch (\Exception $e) {
            \Log::error('Stock update failed', [
                'product_slug' => $productSlug ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update stock');
        }
    }

    public function updatePrice(Request $request, $productSlug)
    {
        try {
            $product = Product::where('slug', $productSlug)->firstOrFail();

            $user = auth()->user();
            $canSeeBuying = $user && !$user->isEmployee();

            $rules = ['selling_price' => 'required|numeric|min:0'];
            if ($canSeeBuying) {
                $rules['buying_price'] = 'nullable|numeric|min:0';
            }

            $validated = $request->validate($rules);

            $oldData = [
                'buying_price' => $product->buying_price !== null ? (float) $product->buying_price : null,
                'selling_price' => (float) $product->selling_price,
            ];

            $updateData = ['selling_price' => (float) $validated['selling_price']];
            if ($canSeeBuying) {
                $updateData['buying_price'] = isset($validated['buying_price']) ? (float) $validated['buying_price'] : null;
            }

            $newData = array_merge($oldData, $updateData);

            if ($oldData['buying_price'] === $newData['buying_price'] && $oldData['selling_price'] === $newData['selling_price']) {
                return response()->json([
                    'success' => true,
                    'message' => 'No price changes detected.',
                    'product' => [
                        'slug' => $product->slug,
                        'name' => $product->name,
                        'buying_price' => $oldData['buying_price'],
                        'selling_price' => $oldData['selling_price'],
                    ],
                ]);
            }

            $product->update($updateData);

            AuditLog::log(
                'update',
                'Product',
                $product->id,
                "Quick price update for product {$product->name} ({$product->code})",
                $oldData,
                $newData
            );

            // Notify managers and shop owners when a non-manager (employee) updates a price
            if ($user && $user->isEmployee()) {
                $shopId = $product->shop_id ?? $user->shop_id;
                $notification = new PriceUpdateNotification(
                    productName:      $product->name,
                    productCode:      $product->code,
                    productSlug:      $product->slug,
                    updatedByName:    $user->name,
                    oldSellingPrice:  $oldData['selling_price'],
                    newSellingPrice:  $newData['selling_price'],
                    oldBuyingPrice:   $oldData['buying_price'],
                    newBuyingPrice:   $newData['buying_price'],
                );
                $recipients = User::where('shop_id', $shopId)
                    ->whereIn('role', [User::ROLE_SHOP_OWNER, User::ROLE_MANAGER])
                    ->get();
                foreach ($recipients as $recipient) {
                    $recipient->notify($notification);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product prices updated successfully.',
                'product' => [
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'buying_price' => $newData['buying_price'],
                    'selling_price' => $newData['selling_price'],
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Quick price update failed', [
                'product_slug' => $productSlug,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update prices. Please try again.',
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        /**
         * Delete photo if exists.
                if (!auth()->user()->hasShopPermission('delete_product')) {
                    abort(403, 'You do not have permission to delete products.');
                }

         */
        if ($product->product_image) {
            \Storage::disk('public')->delete('products/' . $product->product_image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been deleted!');
    }
}
