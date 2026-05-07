<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarcodeSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeController extends Controller
{
    /**
     * Display barcode configuration page
     */
    public function index()
    {
        $user = auth()->user();
        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            abort(403, 'No active shop selected.');
        }

        $settings = BarcodeSettings::getForShop($activeShop->id);

        return $user->viewForShopType('barcode.index', compact('settings'));
    }

    /**
     * Update barcode settings
     */
    public function updateSettings(Request $request)
    {
        Log::info('Barcode settings update request received', [
            'request_data' => $request->all(),
            'content_type' => $request->header('Content-Type')
        ]);

        $user = auth()->user();
        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            Log::error('No active shop found for barcode settings update');
            return response()->json(['success' => false, 'message' => 'No active shop selected.'], 403);
        }

        try {
            $validated = $request->validate([
                'font_size' => 'required|in:10,12,14,16,18',
            ]);

            // Fixed settings for EAN-13 barcode with 40x30mm size
            $validated['barcode_type'] = 'EAN13';
            $validated['barcode_width'] = 2.5;  // Optimized for better scanning
            $validated['barcode_height'] = 70; // Optimized for EAN-13 standard
            $validated['paper_size'] = '40x30';
            $validated['labels_per_row'] = 3;

            // Always show all elements
            $validated['show_barcode'] = true;
            $validated['show_title'] = true;
            $validated['show_price'] = true;

            $settings = BarcodeSettings::getForShop($activeShop->id);

            Log::info('Updating barcode settings', [
                'shop_id' => $activeShop->id,
                'old_settings' => $settings->toArray(),
                'new_settings' => $validated
            ]);

            $settings->update($validated);

            $updatedSettings = $settings->fresh();

            Log::info('Barcode settings updated successfully', [
                'shop_id' => $activeShop->id,
                'updated_settings' => $updatedSettings->toArray()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barcode settings updated successfully.',
                'settings' => $updatedSettings
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for barcode settings', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating barcode settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print barcode for a single product with quantity
     */
    public function printProduct(Product $product, Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'User not authenticated.');
        }

        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            // If no active shop, try to get user's primary shop
            $activeShop = $user->shop;

            if (!$activeShop) {
                abort(403, 'No shop associated with this user.');
            }
        }

        if ($product->shop_id !== $activeShop->id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $quantity = $request->input('quantity', $product->quantity);
        $settings = BarcodeSettings::getForShop($activeShop->id);

        $barcodes = $this->generateBarcodesForProduct($product, $quantity, $settings);

        return $user->viewForShopType('barcode.print-single', compact('product', 'barcodes', 'settings', 'quantity'));
    }

    /**
     * Print barcodes for all in-stock products (bulk)
     */
    public function printBulk()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'User not authenticated.');
        }

        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            // If no active shop, try to get user's primary shop
            $activeShop = $user->shop;

            if (!$activeShop) {
                abort(403, 'No shop associated with this user.');
            }
        }

        $settings = BarcodeSettings::getForShop($activeShop->id);

        // Get all in-stock products for the active shop
        $products = Product::where('shop_id', $activeShop->id)
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $allBarcodes = [];
        foreach ($products as $product) {
            $barcodes = $this->generateBarcodesForProduct($product, $product->quantity, $settings);
            $allBarcodes = array_merge($allBarcodes, $barcodes);
        }

        return $user->viewForShopType('barcode.print-bulk', compact('allBarcodes', 'settings'));
    }

    /**
     * Generate barcodes for a product based on quantity
     */
    private function generateBarcodesForProduct($product, int $quantity, BarcodeSettings $settings)
    {
        // Use SVG generator for high-quality, scalable barcodes that print well
        $generator = new BarcodeGeneratorSVG();
        $barcodes = [];

        // Map barcode type to Picqer constants
        $typeMap = [
            'C128' => $generator::TYPE_CODE_128,
            'C39' => $generator::TYPE_CODE_39,
            'EAN13' => $generator::TYPE_EAN_13,
            'UPCA' => $generator::TYPE_UPC_A,
        ];

        $barcodeType = $typeMap[$settings->barcode_type] ?? $generator::TYPE_CODE_128;

        // Calculate proper barcode dimensions for EAN-13
        // EAN-13 standard: 37.29mm wide x 25.93mm tall at 100% scale
        // Using width multiplier and height in pixels that will scale well
        $barcodeWidth = $settings->barcode_width ?? 2;
        $barcodeHeight = $settings->barcode_height ?? 50;

        // For EAN-13, use larger dimensions for better scanning
        if ($settings->barcode_type === 'EAN13') {
            $barcodeWidth = 2.5;  // Optimized width for better scanning
            $barcodeHeight = 70; // Optimized height for EAN-13 standard
        }

        for ($i = 0; $i < $quantity; $i++) {
            try {
                // Always regenerate barcode based on product code to ensure consistency
                $expectedBarcode = Product::generateBarcode($product->shop_id ?? 1, $product->code ?? '00001');

                // Update if barcode is missing or doesn't match the product code
                if (empty($product->barcode) || $product->barcode !== $expectedBarcode) {
                    if (isset($product->id)) {
                        $product->barcode = $expectedBarcode;
                        $product->save();
                    }
                }

                // Use the numeric barcode directly
                $numericBarcode = $product->barcode ?? $expectedBarcode;

                // Debug log to check barcode
                \Log::info("Barcode Generation Debug", [
                    'product_id' => $product->id ?? 'sample',
                    'product_code' => $product->code,
                    'barcode' => $numericBarcode,
                    'barcode_length' => strlen($numericBarcode),
                    'barcode_type' => $settings->barcode_type
                ]);

                $displayBarcode = $numericBarcode;
                $barcodeCode = $numericBarcode;

                // Prepare barcode code based on type - library handles check digits automatically
                if ($settings->barcode_type === 'EAN13') {
                    // EAN13 requires exactly 12 digits (library adds 13th check digit)
                    // Ensure we have 12 digits by padding or trimming
                    $barcodeCode = substr(str_pad($numericBarcode, 12, '0', STR_PAD_LEFT), 0, 12);
                    // For display, calculate and show the full 13-digit code
                    $displayBarcode = $barcodeCode . $this->calculateEan13CheckDigit($barcodeCode);
                } elseif ($settings->barcode_type === 'UPCA') {
                    // UPCA requires exactly 11 digits (library adds 12th check digit)
                    $barcodeCode = substr(str_pad($numericBarcode, 11, '0', STR_PAD_LEFT), 0, 11);
                } else {
                    // CODE 128 and CODE 39 - use barcode as-is
                    $barcodeCode = $numericBarcode;
                }

                // Generate SVG barcode with proper dimensions
                $barcodeSvg = $generator->getBarcode(
                    $barcodeCode,
                    $barcodeType,
                    $barcodeWidth,
                    $barcodeHeight
                );

                $barcodes[] = [
                    'product_id' => $product->id ?? 0,
                    'name' => $product->name ?? 'Sample Product',
                    'code' => $product->code ?? '00001',
                    'barcode' => $numericBarcode,  // The actual barcode from database
                    'display_barcode' => $displayBarcode,
                    'barcode_code' => $barcodeCode,  // The code encoded in barcode image
                    'barcode_numeric' => $numericBarcode, // The raw numeric barcode for debugging
                    'price' => $product->selling_price ?? 0,
                    'barcode_html' => $barcodeSvg,  // SVG for scalable, high-quality output
                ];
            } catch (\Exception $e) {
                // Log detailed error and skip invalid barcodes
                Log::error("Failed to generate barcode for product", [
                    'product_id' => $product->id ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return $barcodes;
    }

    private function calculateEan13CheckDigit(string $barcode12): int
    {
        $digits = str_split(substr(preg_replace('/\D/', '', $barcode12), 0, 12));
        $sum = 0;

        foreach ($digits as $index => $digit) {
            $sum += ((int) $digit) * (($index % 2 === 0) ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10;
    }

    /**
     * Preview barcode settings
     */
    public function preview(Request $request)
    {
        $user = auth()->user();
        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            return response()->json(['success' => false, 'message' => 'No active shop selected.'], 403);
        }

        // Get a sample product or use dummy data
        $sampleProduct = Product::where('shop_id', $activeShop->id)->first();

        if (!$sampleProduct) {
            $sampleProduct = (object)[
                'id' => 0,
                'name' => 'Sample Product',
                'code' => 'SAMPLE001',
                'selling_price' => 99.99,
            ];
        }

        // Create temporary settings from request (fixed EAN-13 values)
        $settings = new BarcodeSettings([
            'show_barcode' => true,
            'show_title' => true,
            'show_price' => true,
            'barcode_type' => 'EAN13',
            'barcode_width' => 2.5,  // Optimized for better scanning
            'barcode_height' => 70, // Optimized for EAN-13 standard
            'paper_size' => '40x30',
            'labels_per_row' => 3,
            'font_size' => $request->input('font_size', '10'),
        ]);

        $barcodes = $this->generateBarcodesForProduct($sampleProduct, 1, $settings);

        $viewName = $user->getShopTypeView('barcode.preview');
        $html = view($viewName, compact('barcodes', 'settings'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    /**
     * Test print - simple label with no headers
     */
    public function testPrint()
    {
        $user = auth()->user();
        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            abort(403, 'No active shop selected.');
        }

        $settings = BarcodeSettings::getForShop($activeShop->id);

        // Create sample product for testing
        $sampleProduct = (object)[
            'id' => 0,
            'name' => 'Sample Product',
            'code' => '00001',
            'selling_price' => 99.99,
        ];

        // Generate 1 sample barcode for testing
        $barcodes = $this->generateBarcodesForProduct($sampleProduct, 1, $settings);

        return $user->viewForShopType('barcode.print-test', compact('barcodes', 'settings'));
    }
}
