<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Requests\Products\UpdateProductStockRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'stock']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('low_stock')) {
            $query->lowStock();
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $units = Unit::active()->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'unit',
            'stock',
            'stockLogs' => function ($query) {
                $query->with('creator')->latest();
            },
        ]);

        return view('products.show', compact('product'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $validated['created_by'] = Auth::id();
            $product = Product::create($validated);

            // Create initial stock
            $initialStock = $request->input('initial_stock', 0);
            $stock = Stock::create([
                'product_id' => $product->id,
                'quantity' => $initialStock,
                'last_updated_by' => Auth::id(),
            ]);

            // Log initial stock
            if ($initialStock > 0) {
                StockLog::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $initialStock,
                    'previous_quantity' => 0,
                    'current_quantity' => $initialStock,
                    'reference_type' => 'initial_stock',
                    'notes' => 'Initial stock entry',
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $units = Unit::active()->get();
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Soft delete the product. We do not delete the image file 
        // to maintain historical data for past sales and receipts.
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function updateStock(UpdateProductStockRequest $request, Product $product)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $stock = $product->stock ?? Stock::create([
                'product_id' => $product->id,
                'quantity' => 0,
                'last_updated_by' => Auth::id(),
            ]);

            $previousQuantity = $stock->quantity;
            $quantity = $validated['quantity'];

            if ($validated['type'] === 'in') {
                $newQuantity = $previousQuantity + $quantity;
            } elseif ($validated['type'] === 'out') {
                $newQuantity = max(0, $previousQuantity - $quantity);
            } else { // adjustment
                $newQuantity = $quantity;
            }

            $stock->update([
                'quantity' => $newQuantity,
                'last_updated_by' => Auth::id(),
            ]);

            StockLog::create([
                'product_id' => $product->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'previous_quantity' => $previousQuantity,
                'current_quantity' => $newQuantity,
                'reference_type' => 'manual',
                'notes' => $validated['notes'] ?? 'Manual stock update',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update stock: ' . $e->getMessage()]);
        }
    }
}
