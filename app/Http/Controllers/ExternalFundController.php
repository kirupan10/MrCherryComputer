<?php

namespace App\Http\Controllers;

use App\Models\ExternalFund;
use App\Models\FundRepayment;
use App\Models\User;
use App\Enums\FundType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExternalFundController extends Controller
{
    private function resolveView(string $page): string
    {
        return "external-funds.{$page}";
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Display a listing of the external funds
     */
    public function index(Request $request)
    {
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $query = ExternalFund::where('shop_id', $activeShop->id)
            ->with(['creator', 'repayments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by fund type
        if ($request->filled('fund_type')) {
            $query->where('fund_type', $request->fund_type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('source_name', 'like', '%' . $request->search . '%');
        }

        // Order by status (active first) then by start_date
        $funds = $query->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'defaulted' THEN 1 ELSE 2 END")
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        $funds->appends($request->only(['status', 'fund_type', 'search']));

        // Calculate summary statistics
        $allFunds = ExternalFund::where('shop_id', $activeShop->id)
            ->with(['repayments'])
            ->get();

        $totalFundsReceived = $allFunds->sum('amount');
        $activeFunds = $allFunds->where('status', 'active');
        $totalOutstanding = $activeFunds->sum('outstanding_balance');
        $totalRepaid = $allFunds->sum('total_repaid');
        $totalInterestPaid = $allFunds->sum('total_interest_paid');

        return view($this->resolveView('index'), compact(
            'funds',
            'totalFundsReceived',
            'totalOutstanding',
            'totalRepaid',
            'totalInterestPaid'
        ));
    }

    /**
     * Show the form for creating a new external fund
     */
    public function create()
    {
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $fundTypes = FundType::values();

        return view($this->resolveView('create'), compact('fundTypes'));
    }

    /**
     * Store a newly created external fund in storage
     */
    public function store(Request $request)
    {
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $validated = $request->validate([
            'source_name' => 'required|string|max:255',
            'fund_type' => 'required|string|in:' . implode(',', FundType::values()),
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'repayment_terms' => 'nullable|string',
            'start_date' => 'required|date',
            'maturity_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,defaulted',
        ]);

        $validated['shop_id'] = $activeShop->id;
        $validated['created_by'] = auth()->id();

        ExternalFund::create($validated);

        return redirect()->route('reports.external-funds.index')
            ->with('success', 'External fund record created successfully.');
    }

    /**
     * Display the specified external fund
     */
    public function show(ExternalFund $externalFund)
    {
        $this->authorize('view', $externalFund);

        $externalFund->load(['creator', 'repayments.recorder']);

        $repayments = $externalFund->repayments()
            ->orderBy('payment_date', 'desc')
            ->get();

        return view($this->resolveView('show'), compact('externalFund', 'repayments'));
    }

    /**
     * Show the form for editing the specified external fund
     */
    public function edit(ExternalFund $externalFund)
    {
        $this->authorize('update', $externalFund);

        $fundTypes = FundType::values();

        return view($this->resolveView('edit'), compact('externalFund', 'fundTypes'));
    }

    /**
     * Update the specified external fund in storage
     */
    public function update(Request $request, ExternalFund $externalFund)
    {
        $this->authorize('update', $externalFund);

        $validated = $request->validate([
            'source_name' => 'required|string|max:255',
            'fund_type' => 'required|string|in:' . implode(',', FundType::values()),
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'repayment_terms' => 'nullable|string',
            'start_date' => 'required|date',
            'maturity_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,defaulted',
        ]);

        $externalFund->update($validated);

        return redirect()->route('reports.external-funds.show', $externalFund)
            ->with('success', 'External fund updated successfully.');
    }

    /**
     * Remove the specified external fund from storage
     */
    public function destroy(ExternalFund $externalFund)
    {
        $this->authorize('delete', $externalFund);

        $externalFund->delete();

        return redirect()->route('reports.external-funds.index')
            ->with('success', 'External fund deleted successfully.');
    }

    /**
     * Add a repayment to an external fund
     */
    public function addRepayment(Request $request, ExternalFund $externalFund)
    {
        $this->authorize('update', $externalFund);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'principal_amount' => 'required|numeric|min:0',
            'interest_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_amount'] = $validated['principal_amount'] + $validated['interest_amount'];
        $validated['external_fund_id'] = $externalFund->id;
        $validated['recorded_by'] = auth()->id();

        FundRepayment::create($validated);

        // Check if fund is fully repaid and update status
        $externalFund->refresh();
        if ($externalFund->outstanding_balance <= 0 && $externalFund->status === 'active') {
            $externalFund->update(['status' => 'completed']);
        }

        return redirect()->route('reports.external-funds.show', $externalFund)
            ->with('success', 'Repayment recorded successfully.');
    }

    /**
     * Delete a repayment
     */
    public function deleteRepayment(FundRepayment $repayment)
    {
        $fund = $repayment->externalFund;
        $this->authorize('update', $fund);

        $repayment->delete();

        // Recheck fund status
        $fund->refresh();
        if ($fund->outstanding_balance > 0 && $fund->status === 'completed') {
            $fund->update(['status' => 'active']);
        }

        return redirect()->route('reports.external-funds.show', $fund)
            ->with('success', 'Repayment deleted successfully.');
    }

    /**
     * Show external funds report
     */
    public function report(Request $request)
    {
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $funds = ExternalFund::where('shop_id', $activeShop->id)
            ->with(['repayments'])
            ->get();

        // Summary statistics
        $totalFundsReceived = $funds->sum('amount');
        $activeFunds = $funds->where('status', 'active');
        $totalOutstanding = $activeFunds->sum('outstanding_balance');
        $totalRepaid = $funds->sum('total_repaid');
        $totalInterestPaid = $funds->sum('total_interest_paid');

        // Fund breakdown by type
        $fundsByType = $funds->groupBy('fund_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'outstanding' => $group->where('status', 'active')->sum('outstanding_balance'),
            ];
        });

        // Monthly repayment obligations (next 12 months)
        $monthlyObligations = [];
        // This would require more complex calculation based on repayment terms

        return view($this->resolveView('report'), compact(
            'funds',
            'totalFundsReceived',
            'totalOutstanding',
            'totalRepaid',
            'totalInterestPaid',
            'fundsByType'
        ));
    }
}
