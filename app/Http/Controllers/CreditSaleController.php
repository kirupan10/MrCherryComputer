<?php

namespace App\Http\Controllers;

use App\Models\CreditSale;
use App\Models\CreditPayment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;

class CreditSaleController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'unpaid'); // Default to pending + partial
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = CreditSale::with(['customer', 'order']);

        // Filter by status - if 'all' is selected, show all statuses
        if ($status === 'unpaid') {
            $query->whereIn('status', ['pending', 'partial']);
        } elseif ($status && in_array($status, ['pending', 'partial', 'paid'])) {
            $query->where('status', $status);
        }

        // Search by customer name or invoice number
        if ($search) {
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('order', function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $creditSales = $query->orderByRaw("CASE
                WHEN status = 'pending' THEN 1
                WHEN status = 'partial' THEN 2
                WHEN status = 'paid' THEN 3
                ELSE 4 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // Consolidate aggregate queries into a single DB query to reduce round-trips
        $aggregates = CreditSale::selectRaw(
            'COALESCE(SUM(total_amount),0) as total_credit, '
            . 'COALESCE(SUM(paid_amount),0) as total_paid, '
            . 'COALESCE(SUM(due_amount),0) as total_due'
        )->first();

        $stats = [
            'total_credit' => $aggregates->total_credit ?? 0,
            'total_paid' => $aggregates->total_paid ?? 0,
            'total_due' => $aggregates->total_due ?? 0,
            'pending_payment_count' => CreditSale::whereIn('status', ['pending', 'partial'])->count(),
        ];

        return view('credit-sales.index', compact('creditSales', 'stats', 'status', 'search', 'fromDate', 'toDate'));
    }

    public function show(CreditSale $creditSale)
    {
        $creditSale->load(['customer', 'order', 'payments' => function($query) {
            $query->orderBy('payment_date', 'desc');
        }]);

        return view('credit-sales.show', compact('creditSale'));
    }

    public function makePayment(Request $request, CreditSale $creditSale)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $creditSale->due_amount,
            'payment_method' => 'required|in:Cash,Card,Bank Transfer',
            'notes' => 'nullable|string|max:255'
        ]);

        $payment = $creditSale->makePayment(
            $request->payment_amount,
            $request->payment_method,
            $request->notes
        );

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }

    public function overdueReport()
    {
        $overdueSales = CreditSale::overdue()
            ->with(['customer', 'order'])
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        return view('credit-sales.overdue', compact('overdueSales'));
    }

    public function customerCreditHistory(Customer $customer)
    {
        $creditSales = $customer->creditSales()
            ->with(['order', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $customerStats = [
            'total_credit' => $customer->creditSales()->sum('total_amount'),
            'total_paid' => $customer->creditSales()->sum('paid_amount'),
            'total_due' => $customer->creditSales()->sum('due_amount'),
            'overdue_count' => $customer->creditSales()->overdue()->count(),
        ];

        return view('credit-sales.customer-history', compact('customer', 'creditSales', 'customerStats'));
    }

    /**
     * Download credit sale receipt as PDF
     */
    public function downloadPdf(CreditSale $creditSale)
    {
        $creditSale->load(['customer', 'order', 'payments']);

        $letterheadConfig = $this->getLetterheadConfig(auth()->user()->shop_id ?? null);
        $letterheadFile = $letterheadConfig['letterhead_file'] ?? null;
        $letterheadType = $letterheadConfig['letterhead_type'] ?? null;

        try {
            $this->ensureDiskSpaceOrFail();

            if ($letterheadFile && $letterheadType === 'pdf') {
                $pdfContent = $this->generatePdfWithLetterhead($creditSale, $letterheadConfig, $letterheadFile);
            } else {
                $pdfContent = $this->generateStandardSalesPdf($creditSale, $letterheadConfig);
            }

            $fileName = 'Credit-Sale-' . ($creditSale->order->invoice_no ?? $creditSale->id) . '.pdf';

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            \Log::error('Failed to generate credit sale PDF', ['id' => $creditSale->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }



    /**
     * Generate standard PDF (image letterhead or no letterhead)
     */
    private function generateStandardSalesPdf(CreditSale $creditSale, array $letterheadConfig)
    {
        // Embed preview image as data URI for PDF rendering
        try {
            if (!empty($letterheadConfig['preview_image'])) {
                $previewPath = public_path('letterheads/' . $letterheadConfig['preview_image']);
                if (File::exists($previewPath)) {
                    $contents = File::get($previewPath);
                    $mime = @mime_content_type($previewPath) ?: 'image/png';
                    $letterheadConfig['preview_image_data'] = 'data:' . $mime . ';base64,' . base64_encode($contents);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to embed preview image for PDF generation', ['error' => $e->getMessage()]);
        }

        $pdf = PDF::loadView('credit-sales.pdf-receipt', [
            'creditSale' => $creditSale,
            'order' => $creditSale->order,
            'letterheadConfig' => $letterheadConfig,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'isFontSubsettingEnabled' => false,
        ]);

        return $pdf->output();
    }

    /**
     * Generate PDF with PDF letterhead using FPDI merge
     */
    private function generatePdfWithLetterhead(CreditSale $creditSale, array $letterheadConfig, $letterheadFile)
    {
        // Create overlay PDF using HTML view with proper configuration
        $contentPdf = PDF::loadView('credit-sales.pdf-receipt-overlay', [
            'creditSale' => $creditSale,
            'order' => $creditSale->order,
            'letterheadConfig' => $letterheadConfig,
        ]);

        $contentPdf->setPaper('A4', 'portrait');
        $contentPdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'isFontSubsettingEnabled' => false,
        ]);

        // Use unique temp file path to avoid conflicts
        $tempContentPath = storage_path('app/temp_credit_' . $creditSale->id . '_' . uniqid() . '.pdf');
        File::put($tempContentPath, $contentPdf->output());

        try {
            $letterheadPath = public_path('letterheads/' . $letterheadFile);
            if (!File::exists($letterheadPath)) {
                throw new \Exception('Letterhead file not found: ' . $letterheadPath);
            }

            // Merge using FPDI with configured offset
            $mergeOffset = $letterheadConfig['merge_offset'] ?? ['x' => 0, 'y' => 0];
            $mergedPdf = $this->mergePdfsWithFpdi($letterheadPath, $tempContentPath, $mergeOffset);

            return $mergedPdf;
        } finally {
            // Clean up temp file
            if (File::exists($tempContentPath)) {
                @File::delete($tempContentPath);
            }
        }
    }

    /**
     * Merge PDFs using FPDI (same logic as OrderController)
     */
    private function mergePdfsWithFpdi($letterheadPath, $contentPath, $mergeOffset = ['x' => 0, 'y' => 0])
    {
        // Try to use Node.js pdf-lib merger if available for better quality
        try {
            $nodeScript = base_path('scripts/pdf_merge.js');
            if (File::exists($nodeScript)) {
                $outPath = storage_path('app/merged_credit_' . uniqid() . '.pdf');
                $x = isset($mergeOffset['x']) ? $mergeOffset['x'] : 0;
                $y = isset($mergeOffset['y']) ? $mergeOffset['y'] : 0;
                $unit = isset($mergeOffset['unit']) ? $mergeOffset['unit'] : 'mm';
                $cmd = 'node ' . escapeshellarg($nodeScript) . ' ' . escapeshellarg($letterheadPath) . ' ' . escapeshellarg($contentPath) . ' ' . escapeshellarg($outPath) . ' ' . escapeshellarg($x) . ' ' . escapeshellarg($y) . ' ' . escapeshellarg($unit);

                exec($cmd . ' 2>&1', $cmdOut, $ret);
                if ($ret === 0 && File::exists($outPath)) {
                    $result = File::get($outPath);
                    @File::delete($outPath);
                    return $result;
                }

                \Log::warning('Node pdf_merge.js failed; falling back to FPDI', ['cmd' => $cmd, 'ret' => $ret]);
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to run node pdf_merge.js, falling back to FPDI', ['error' => $e->getMessage()]);
        }

        // Fall back to FPDI for stable PDF merging
        $fpdi = new Fpdi();

        try {
            // Import letterhead PDF (background)
            $fpdi->setSourceFile($letterheadPath);
            $letterheadTemplate = $fpdi->importPage(1);

            // Import content PDF (overlay)
            $contentPageCount = $fpdi->setSourceFile($contentPath);
            $contentPageToImport = max(1, $contentPageCount);
            $contentTemplate = $fpdi->importPage($contentPageToImport);

            // Get letterhead size for proper canvas dimensions
            $lhSize = null;
            if (method_exists($fpdi, 'getTemplateSize')) {
                $lhSize = $fpdi->getTemplateSize($letterheadTemplate);
            }

            // A4 size in PDF points (595.28 x 841.89)
            $canvasW = 595.28;
            $canvasH = 841.89;
            if (!empty($lhSize) && !empty($lhSize['width']) && !empty($lhSize['height'])) {
                $canvasW = (float)$lhSize['width'];
                $canvasH = (float)$lhSize['height'];
            }

            // Create new page with proper dimensions
            $fpdi->AddPage('P', [$canvasW, $canvasH]);

            // Place letterhead at full size (background)
            if (!empty($lhSize) && !empty($lhSize['width']) && !empty($lhSize['height'])) {
                $lhW = (float)$lhSize['width'];
                $lhH = (float)$lhSize['height'];
                if (abs($lhW - $canvasW) < 0.0001 && abs($lhH - $canvasH) < 0.0001) {
                    $fpdi->useTemplate($letterheadTemplate, 0, 0, $lhW, $lhH);
                } else {
                    // Scale letterhead to fit canvas
                    $scaleX = $canvasW / $lhW;
                    $scaleY = $canvasH / $lhH;
                    $scale = min($scaleX, $scaleY);
                    $drawW = $lhW * $scale;
                    $drawH = $lhH * $scale;
                    $lhX = ($canvasW - $drawW) / 2.0;
                    $lhY = ($canvasH - $drawH) / 2.0;
                    $fpdi->useTemplate($letterheadTemplate, $lhX, $lhY, $drawW, $drawH);
                }
            } else {
                $fpdi->useTemplate($letterheadTemplate, 0, 0, $canvasW, $canvasH);
            }

            // Overlay content with merge offset
            $contentSize = null;
            if (method_exists($fpdi, 'getTemplateSize')) {
                $contentSize = $fpdi->getTemplateSize($contentTemplate);
            }

            if (!empty($contentSize) && !empty($contentSize['width']) && !empty($contentSize['height'])) {
                $cW = (float)$contentSize['width'];
                $cH = (float)$contentSize['height'];
            } else {
                $cW = $canvasW;
                $cH = $canvasH;
            }

            // Normalize merge offset to PDF points
            $offset = $this->normalizeMergeOffset($mergeOffset, $lhSize);
            $contentX = (float)($offset['x'] ?? 0);
            $contentY = (float)($offset['y'] ?? 0);

            // Scale content to match canvas if needed
            if (abs($cW - $canvasW) > 0.0001 || abs($cH - $canvasH) > 0.0001) {
                $scaleX = $canvasW / $cW;
                $scaleY = $canvasH / $cH;
                $scale = min($scaleX, $scaleY);
                $contentDrawW = $cW * $scale;
                $contentDrawH = $cH * $scale;
                $fpdi->useTemplate($contentTemplate, $contentX, $contentY, $contentDrawW, $contentDrawH);
            } else {
                $fpdi->useTemplate($contentTemplate, $contentX, $contentY, $cW, $cH);
            }

            return $fpdi->Output('S'); // Return as string
        } catch (\Exception $e) {
            \Log::error('FPDI merge failed', [
                'error' => $e->getMessage(),
                'letterhead' => $letterheadPath,
                'content' => $contentPath,
            ]);
            throw new \Exception('Failed to merge PDFs: ' . $e->getMessage());
        }
    }

    /**
     * Get letterhead config for a shop (same as OrderController)
     */
    private function getLetterheadConfig($shopId = null)
    {
        $user = auth()->user();
        $activeShop = $shopId ? \App\Models\Shop::find($shopId) : ($user ? $user->getActiveShop() : null);

        if (!$activeShop) {
            return [];
        }

        $configPath = storage_path('app/letterhead_config_shop_' . $activeShop->id . '.json');
        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true);

            // SECURITY: Validate that the letterhead file belongs to this shop
            if (!empty($config['letterhead_file'])) {
                $expectedPrefix = 'letterhead_shop_' . $activeShop->id . '.';
                if (strpos($config['letterhead_file'], $expectedPrefix) !== 0) {
                    \Log::warning('Letterhead file mismatch - file does not belong to current shop', [
                        'shop_id' => $activeShop->id,
                        'letterhead_file' => $config['letterhead_file'],
                        'expected_prefix' => $expectedPrefix
                    ]);
                    // Remove the invalid letterhead file from config
                    unset($config['letterhead_file']);
                    unset($config['letterhead_type']);
                    unset($config['preview_image']);
                }
            }

            return $config;
        }
        return [];
    }

    /**
     * Normalize merge offset into PDF user units
     */
    private function normalizeMergeOffset(array $mergeOffset, $lhSize = null)
    {
        $unit = isset($mergeOffset['unit']) ? strtolower($mergeOffset['unit']) : 'pt';
        $x = isset($mergeOffset['x']) ? (float)$mergeOffset['x'] : 0.0;
        $y = isset($mergeOffset['y']) ? (float)$mergeOffset['y'] : 0.0;

        if ($unit === 'mm') {
            // Convert millimeters to PDF points (1 mm = 2.834645669 pt)
            $x = $x * 2.834645669;
            $y = $y * 2.834645669;
        } elseif ($unit === 'percent') {
            // Percentage relative to letterhead dimensions
            if (!empty($lhSize) && !empty($lhSize['width']) && !empty($lhSize['height'])) {
                $lhW = (float)$lhSize['width'];
                $lhH = (float)$lhSize['height'];
                $x = ($x / 100.0) * $lhW;
                $y = ($y / 100.0) * $lhH;
            } else {
                $x = 0.0;
                $y = 0.0;
            }
        }

        return ['x' => $x, 'y' => $y];
    }

    /**
     * Ensure sufficient disk space before PDF generation
     */
    private function ensureDiskSpaceOrFail($minMbFree = 100)
    {
        $free = disk_free_space(storage_path());
        if ($free && $free < ($minMbFree * 1024 * 1024)) {
            throw new \Exception('Insufficient disk space for PDF generation');
        }
    }
}
