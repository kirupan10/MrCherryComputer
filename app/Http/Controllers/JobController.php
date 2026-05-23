<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;
use Illuminate\Support\Facades\File;

class JobController extends Controller
{
    protected function showRoute(Job $job): string
    {
        return route('jobs.show', $job);
    }

    protected function indexRoute(): string
    {
        return 'jobs.index';
    }

    protected function listRoute(): string
    {
        return 'jobs.list';
    }

    /** Display a listing of the jobs. */
    public function index()
    {
        // Get active shop
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopId = $activeShop ? $activeShop->id : null;

        $jobs = Job::with(['customer', 'jobType'])->latest()->paginate(20);

        // Calculate stats for active shop only
        $stats = [
            'total' => Job::count(),
            'pending' => Job::where('status', 'pending')->count(),
            'this_month' => Job::whereMonth('created_at', now()->month)->count(),
            'open' => Job::whereIn('status', ['pending','in_progress'])->count(),
        ];

        return view('jobs.index', compact('jobs', 'stats'));
    }

    /** Display all jobs with filters. */
    public function list(Request $request)
    {
        // Get active shop
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopId = $activeShop ? $activeShop->id : null;

        $query = Job::with(['customer', 'jobType']);

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Apply date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Calculate shop-specific stats
        $stats = [
            'total' => Job::count(),
            'pending' => Job::where('status', 'pending')->count(),
            'in_progress' => Job::where('status', 'in_progress')->count(),
            'completed' => Job::where('status', 'completed')->count(),
        ];

        $jobs = $query->latest()->paginate(20);

        return view('jobs.list', compact('jobs', 'stats'));
    }

    /** Show the form for creating a new job. */
    public function create()
    {
        // The index page now contains the create form inline. Redirect to index so users see the all-in-one page.
        return redirect()->route($this->indexRoute());
    }

    /** Store a newly created job in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'new_customer_name' => 'required_without:customer_id|string|max:150',
            'new_customer_phone' => 'required_without:customer_id|string|max:50',
            'new_customer_address' => 'required_without:customer_id|string|max:500',
            'type' => 'nullable|string|max:150',
            'job_type_id' => 'nullable|exists:job_types,id',
            'description' => 'nullable|string',
            'estimated_duration' => 'nullable|integer|min:0',
            'status' => 'nullable|in:' . implode(',', Job::statuses()),
        ]);

        // Prevent Walk-In Customer from being used for jobs
        if (!empty($data['customer_id'])) {
            $customer = \App\Models\Customer::find($data['customer_id']);
            if ($customer && $customer->name === 'Walk-In Customer') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Walk-In Customer cannot be used for jobs. Please select a specific customer or create a new one.');
            }
        }

        // If no existing customer chosen, create a new customer from provided details
        // Customer info is required for jobs (enforced by validation)
        if (empty($data['customer_id'])) {
            $customer = \App\Models\Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'] ?? null,
                'address' => $data['new_customer_address'] ?? null,
                'created_by' => auth()->id(),
                'shop_id' => auth()->user()->shop_id ?? null,
            ]);
            $data['customer_id'] = $customer->id;
        }

        // If estimated_duration not provided, and a job_type_id is selected, use job type default_days
        if (empty($data['estimated_duration']) && !empty($data['job_type_id'])) {
            $jt = \App\Models\JobType::find($data['job_type_id']);
            if ($jt && $jt->default_days !== null) {
                $data['estimated_duration'] = $jt->default_days;
            }
        }

        // If estimated_duration not provided, and a job_type_id is selected, use job type default_days
        if (empty($data['estimated_duration']) && !empty($data['job_type_id'])) {
            $jt = \App\Models\JobType::find($data['job_type_id']);
            if ($jt && $jt->default_days !== null) {
                $data['estimated_duration'] = $jt->default_days;
            }
        }

        // Generate reference number with shop-specific counting
        // Use shop scope to get last job for this shop only (global scope will filter)
        $lastJob = Job::latest('id')->first();
        $nextNumber = $lastJob ? $lastJob->id + 1 : 1;

        // Get shop prefix for reference number if available
        $shopPrefix = 'APFJS';
        if (auth()->user()->shop && isset(auth()->user()->shop->prefix)) {
            $shopPrefix = strtoupper(auth()->user()->shop->prefix);
        }

        $data['reference_number'] = $shopPrefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        $data['status'] = $data['status'] ?? Job::STATUS_PENDING;

        // Set shop_id from authenticated user
        $data['shop_id'] = auth()->user()->shop_id;

        // Only keep job fields
        $jobData = array_filter($data, function ($k) {
            return in_array($k, ['reference_number', 'type', 'description', 'estimated_duration', 'status', 'shop_id', 'job_type_id', 'customer_id']);
        }, ARRAY_FILTER_USE_KEY);

        $job = Job::create($jobData);

        return redirect()->to($this->showRoute($job))->with('success', 'Job created successfully');
    }

    /** Display the specified job. */
    public function show(Job $job)
    {
        $job->load(['customer', 'jobType', 'statusHistories.changedBy']);
        return view('jobs.show', compact('job'));
    }

    /**
     * Return JSON payload for a job receipt (used by the client-side modal/print flow)
     */
    public function showReceipt(Job $job)
    {
        $job->load(['customer', 'jobType']);
        $shop = auth()->user()->shop ?? \App\Models\Shop::first();

        // Return a simple JSON structure the frontend can use to build the printable receipt
        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'reference_number' => $job->reference_number,
                'type' => $job->type,
                'description' => $job->description,
                'estimated_duration' => $job->estimated_duration,
                'status' => $job->status,
                'created_at' => $job->created_at->toIso8601String(),
                'updated_at' => $job->updated_at->toIso8601String(),
                'customer' => $job->customer ? [
                    'name' => $job->customer->name,
                    'phone' => $job->customer->phone,
                    'address' => $job->customer->address,
                ] : null,
                'job_type' => $job->jobType ? [
                    'name' => $job->jobType->name ?? $job->jobType->type ?? null,
                    'default_days' => $job->jobType->default_days ?? null,
                ] : null,
                'shop' => $shop ? [
                    'name' => $shop->name,
                    'address' => $shop->address,
                    'phone' => $shop->phone,
                    'email' => $shop->email,
                ] : null,
            ],
        ]);
    }

    /** Show the form for editing the specified job. */
    public function edit(Job $job)
    {
        $statuses = Job::statuses();
        $customers = \App\Models\Customer::orderBy('name')->get();
        return view('jobs.edit', compact('job', 'statuses', 'customers'));
    }

    /** Update the specified job in storage. */
    public function update(Request $request, Job $job)
    {
        // Quick status update (from dropdown in list)
        // Get all input except Laravel's internal fields
        $actualInput = $request->except(['_token', '_method']);

        // If ONLY status is present, it's a quick status update
        if (count($actualInput) === 1 && isset($actualInput['status'])) {
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', Job::statuses()),
            ]);

            $oldStatus = $job->status;
            $job->update(['status' => $validated['status']]);

            // Save status history
            \App\Models\JobStatusHistory::create([
                'job_id' => $job->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'changed_by' => auth()->id(),
            ]);

            return redirect()->route($this->listRoute())->with('success', 'Job status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])));
        }

        // Full job update
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'new_customer_name' => 'required_without:customer_id|string|max:150',
            'new_customer_phone' => 'required_without:customer_id|string|max:50',
            'new_customer_address' => 'required_without:customer_id|string|max:500',
            'type' => 'nullable|string|max:150',
            'job_type_id' => 'nullable|exists:job_types,id',
            'description' => 'nullable|string',
            'estimated_duration' => 'nullable|integer|min:0',
            'status' => 'nullable|in:' . implode(',', Job::statuses()),
        ]);

        // Prevent Walk-In Customer from being used for jobs
        if (!empty($data['customer_id'])) {
            $customer = \App\Models\Customer::find($data['customer_id']);
            if ($customer && $customer->name === 'Walk-In Customer') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Walk-In Customer cannot be used for jobs. Please select a specific customer or create a new one.');
            }
        }

        if (empty($data['customer_id'])) {
            $customer = \App\Models\Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'] ?? null,
                'address' => $data['new_customer_address'] ?? null,
                'created_by' => auth()->id(),
                'shop_id' => auth()->user()->shop_id ?? null,
            ]);
            $data['customer_id'] = $customer->id;
        }

        $jobData = array_filter($data, function ($k) {
            return in_array($k, ['type', 'description', 'estimated_duration', 'status', 'job_type_id', 'customer_id']);
        }, ARRAY_FILTER_USE_KEY);

        $job->update($jobData);

        return redirect()->to($this->showRoute($job))->with('success', 'Job updated successfully');
    }

    /** Remove the specified job from storage. */
    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route($this->indexRoute())->with('success', 'Job removed');
    }

    /**
     * Download PDF job sheet (always without letterhead - clean design)
     */
    public function downloadPdfJobSheet($jobId)
    {
        try {
            $job = Job::with(['customer', 'jobType', 'items'])->findOrFail($jobId);
            $shop = auth()->user()->getActiveShop() ?: \App\Models\Shop::first();

            // Generate PDF using DomPDF (no letterhead - always clean design)
            $pdf = PDF::loadView('jobs.pdf-job-sheet', [
                'job' => $job,
                'shop' => $shop,
                'letterheadConfig' => [], // Empty config - no letterhead
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => false,
            ]);

            // Generate filename
            $filename = "JobSheet_{$job->reference_number}_" . ($job->created_at ? $job->created_at->format('Y-m-d') : 'unknown') . ".pdf";

            // Clear any output buffers
            if (ob_get_length() > 0) {
                ob_end_clean();
            }

            // Get PDF content
            $pdfContent = $pdf->output();

            // Return PDF with proper headers and redirect back after download
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ])->header('Refresh', '1; url=' . $this->showRoute(Job::findOrFail($jobId)));

        } catch (\Throwable $e) {
            \Log::error('Job sheet PDF generation failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate job sheet PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate job PDF with PDF letterhead (FPDI merge)
     */
    /**
     * Get letterhead configuration (same as OrderController)
     */
    private function getLetterheadConfig()
    {
        $user = auth()->user();
        $activeShop = $user->getActiveShop();

        if (!$activeShop) {
            return $this->getDefaultLetterheadConfig();
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

            return array_merge($this->getDefaultLetterheadConfig(), $config);
        }
        return $this->getDefaultLetterheadConfig();
    }

    /**
     * Get default letterhead configuration
     */
    private function getDefaultLetterheadConfig()
    {
        return [
            'letterhead_type' => 'image',
            'letterhead_file' => null,
            'letterhead_image' => null,
            'preview_image' => null,
        ];
    }
}
