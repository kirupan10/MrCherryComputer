<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Http\Controllers\Controller;
use App\ShopTypes\Tech\Models\TechRepairJob;
use App\ShopTypes\Tech\Models\TechSerialNumber;
use App\ShopTypes\Tech\Models\TechProduct;
use App\ShopTypes\Tech\Models\TechRepairPart;
use App\ShopTypes\Tech\Models\TechDiagnostic;
use App\Models\Customer;
use App\Models\User;
use App\Traits\HasShopFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechRepairJobController extends Controller
{
    use HasShopFeatures;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('shop.tenant');
        $this->requireShopFeature('repairs');
    }

    /**
     * Display a listing of repair jobs.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TechRepairJob::class);

        $query = TechRepairJob::with(['product', 'customer', 'assignedTechnician'])
            ->forCurrentShop();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by technician
        if ($request->filled('technician_id')) {
            $query->where('assigned_technician_id', $request->technician_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('received_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('received_date', '<=', $request->to_date);
        }

        $repairJobs = $query->latest('received_date')->paginate(20);

        // Get technicians for filter
        $technicians = User::where('shop_id', auth()->user()->currentShop->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('repairs.index', compact('repairJobs', 'technicians'));
    }

    /**
     * Show the form for creating a new repair job.
     */
    public function create(Request $request)
    {
        $this->authorize('create', TechRepairJob::class);

        $customers = Customer::forCurrentShop()->orderBy('name')->get();
        $products = TechProduct::forCurrentShop()->orderBy('name')->get();
        $technicians = User::where('shop_id', auth()->user()->currentShop->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedSerial = null;
        if ($request->filled('serial_number_id')) {
            $selectedSerial = TechSerialNumber::forCurrentShop()
                ->with('product')
                ->findOrFail($request->serial_number_id);
        }

        return view('repairs.create', compact('customers', 'products', 'technicians', 'selectedSerial'));
    }

    /**
     * Store a newly created repair job.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TechRepairJob::class);
        $productTable = (new TechProduct())->getTable();
        $serialNumberTable = (new TechSerialNumber())->getTable();

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tech_product_id' => 'required|exists:' . $productTable . ',id',
            'tech_serial_number_id' => 'nullable|exists:' . $serialNumberTable . ',id',
            'received_date' => 'required|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:received_date',
            'issue_description' => 'required|string',
            'customer_notes' => 'nullable|string',
            'assigned_technician_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        // Generate job number
        $lastJob = TechRepairJob::forCurrentShop()
            ->latest('id')
            ->first();

        $jobNumber = 'RJ-' . str_pad(($lastJob ? $lastJob->id + 1 : 1), 6, '0', STR_PAD_LEFT);

        $validated['shop_id'] = auth()->user()->currentShop->id;
        $validated['job_number'] = $jobNumber;
        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        $repairJob = TechRepairJob::create($validated);

        return redirect()
            ->route('tech.repairs.show', $repairJob)
            ->with('success', 'Repair job created successfully.');
    }

    /**
     * Display the specified repair job.
     */
    public function show(TechRepairJob $repairJob)
    {
        $this->authorize('view', $repairJob);

        $repairJob->load([
            'product',
            'serialNumber',
            'customer',
            'assignedTechnician',
            'createdBy',
            'parts.product',
            'diagnostics'
        ]);

        return view('repairs.show', compact('repairJob'));
    }

    /**
     * Show the form for editing the repair job.
     */
    public function edit(TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        // Cannot edit completed or cancelled jobs
        if (in_array($repairJob->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->withErrors(['error' => 'Cannot edit completed, delivered, or cancelled repair jobs.']);
        }

        $customers = Customer::forCurrentShop()->orderBy('name')->get();
        $products = TechProduct::forCurrentShop()->orderBy('name')->get();
        $technicians = User::where('shop_id', auth()->user()->currentShop->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('repairs.edit', compact('repairJob', 'customers', 'products', 'technicians'));
    }

    /**
     * Update the specified repair job.
     */
    public function update(Request $request, TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);
        $productTable = (new TechProduct())->getTable();
        $serialNumberTable = (new TechSerialNumber())->getTable();

        // Cannot update completed or cancelled jobs
        if (in_array($repairJob->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->withErrors(['error' => 'Cannot update completed, delivered, or cancelled repair jobs.']);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tech_product_id' => 'required|exists:' . $productTable . ',id',
            'tech_serial_number_id' => 'nullable|exists:' . $serialNumberTable . ',id',
            'expected_completion_date' => 'nullable|date|after_or_equal:received_date',
            'issue_description' => 'required|string',
            'customer_notes' => 'nullable|string',
            'assigned_technician_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_cost' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
        ]);

        $repairJob->update($validated);

        return redirect()
            ->route('tech.repairs.show', $repairJob)
            ->with('success', 'Repair job updated successfully.');
    }

    /**
     * Assign a technician to the repair job.
     */
    public function assignTechnician(Request $request, TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        $validated = $request->validate([
            'assigned_technician_id' => 'required|exists:users,id',
        ]);

        $repairJob->update([
            'assigned_technician_id' => $validated['assigned_technician_id'],
        ]);

        return back()->with('success', 'Technician assigned successfully.');
    }

    /**
     * Start diagnosis phase.
     */
    public function startDiagnosis(TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        if ($repairJob->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending jobs can start diagnosis.']);
        }

        $repairJob->update([
            'status' => 'diagnosis',
            'diagnosis_started_at' => now(),
        ]);

        return back()->with('success', 'Diagnosis started successfully.');
    }

    /**
     * Start repair phase.
     */
    public function startRepair(TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        if (!in_array($repairJob->status, ['pending', 'diagnosis'])) {
            return back()->withErrors(['error' => 'Cannot start repair from current status.']);
        }

        $repairJob->update([
            'status' => 'in_progress',
            'repair_started_at' => now(),
        ]);

        return back()->with('success', 'Repair started successfully.');
    }

    /**
     * Complete the repair job.
     */
    public function complete(Request $request, TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        if ($repairJob->status !== 'in_progress') {
            return back()->withErrors(['error' => 'Only in-progress jobs can be completed.']);
        }

        $validated = $request->validate([
            'repair_notes' => 'required|string',
            'final_cost' => 'required|numeric|min:0',
        ]);

        $repairJob->update([
            'status' => 'completed',
            'completed_at' => now(),
            'repair_notes' => $validated['repair_notes'],
            'final_cost' => $validated['final_cost'],
        ]);

        return back()->with('success', 'Repair job completed successfully.');
    }

    /**
     * Mark job as delivered to customer.
     */
    public function deliver(TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        if ($repairJob->status !== 'completed') {
            return back()->withErrors(['error' => 'Only completed jobs can be delivered.']);
        }

        $repairJob->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return back()->with('success', 'Job marked as delivered successfully.');
    }

    /**
     * Add a part to the repair job.
     */
    public function addPart(Request $request, TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);
        $productTable = (new TechProduct())->getTable();

        $validated = $request->validate([
            'tech_product_id' => 'required|exists:' . $productTable . ',id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['tech_repair_job_id'] = $repairJob->id;
        $validated['total_cost'] = $validated['quantity'] * $validated['unit_cost'];

        TechRepairPart::create($validated);

        return back()->with('success', 'Part added successfully.');
    }

    /**
     * Remove a part from the repair job.
     */
    public function removePart(TechRepairJob $repairJob, TechRepairPart $part)
    {
        $this->authorize('update', $repairJob);

        if ($part->tech_repair_job_id !== $repairJob->id) {
            return back()->withErrors(['error' => 'Part does not belong to this repair job.']);
        }

        $part->delete();

        return back()->with('success', 'Part removed successfully.');
    }

    /**
     * Add a diagnostic entry.
     */
    public function addDiagnostic(Request $request, TechRepairJob $repairJob)
    {
        $this->authorize('update', $repairJob);

        $validated = $request->validate([
            'test_name' => 'required|string|max:255',
            'test_result' => 'required|in:pass,fail,warning',
            'findings' => 'required|string',
            'recommendations' => 'nullable|string',
        ]);

        $validated['tech_repair_job_id'] = $repairJob->id;
        $validated['tested_by'] = auth()->id();
        $validated['tested_at'] = now();

        TechDiagnostic::create($validated);

        return back()->with('success', 'Diagnostic added successfully.');
    }

    /**
     * Print job sheet.
     */
    public function print(TechRepairJob $repairJob)
    {
        $this->authorize('view', $repairJob);

        $repairJob->load([
            'product',
            'serialNumber',
            'customer',
            'assignedTechnician',
            'parts.product',
            'diagnostics'
        ]);

        return view('repairs.print', compact('repairJob'));
    }

    /**
     * Remove the specified repair job.
     */
    public function destroy(TechRepairJob $repairJob)
    {
        $this->authorize('delete', $repairJob);

        // Can only delete pending jobs
        if ($repairJob->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending repair jobs can be deleted.']);
        }

        // Delete associated parts and diagnostics
        $repairJob->parts()->delete();
        $repairJob->diagnostics()->delete();
        $repairJob->delete();

        return redirect()
            ->route('tech.repairs.index')
            ->with('success', 'Repair job deleted successfully.');
    }
}
