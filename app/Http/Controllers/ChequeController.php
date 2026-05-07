<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\CreditPurchase;
use App\Models\BusinessTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChequeController extends Controller
{
    private function resolveView(string $page): string
    {
        $shopType = active_shop_type() ?? 'tech';
        $shopTypeView = "shop-types.{$shopType}.cheques.{$page}";
        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        $techView = "shop-types.tech.cheques.{$page}";
        if (view()->exists($techView)) {
            return $techView;
        }

        return "cheques.{$page}";
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Update only the status of a cheque (from index page inline form)
     */
    public function updateStatus(Request $request, Cheque $cheque)
    {
        // Staff members cannot update cheque status
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update cheque status.');
        }

        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }
        $validated = $request->validate([
            'status' => 'required|in:pending,deposited,cleared,bounced,cancelled',
        ]);
        $cheque->update(['status' => $validated['status']]);
        return back()->with('success', 'Cheque status updated successfully.');
    }
    /**
     * Display a listing of cheques
     */
    public function index(Request $request)
    {
        $shopId = $this->currentUser()->shop_id;
        $status = $request->get('status');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Cheque::where('shop_id', $shopId)->with('createdBy');

        // Filter by status
        if ($status && in_array($status, ['pending', 'deposited', 'cleared', 'bounced', 'cancelled'])) {
            $query->where('status', $status);
        }

        // Search by cheque number, bank name, or payee name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cheque_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('payee_name', 'like', "%{$search}%")
                  ->orWhere('drawer_name', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($dateFrom) {
            $query->where('cheque_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('cheque_date', '<=', $dateTo);
        }

        $cheques = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10);

        // Calculate statistics
        $rawStats = Cheque::where('shop_id', $shopId)
            ->select(
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN status = "cleared" THEN 1 ELSE 0 END) as cleared_count'),
                DB::raw('SUM(CASE WHEN status = "bounced" THEN 1 ELSE 0 END) as bounced_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN status = "cleared" THEN amount ELSE 0 END) as cleared_amount'),
                DB::raw('SUM(CASE WHEN status = "bounced" THEN amount ELSE 0 END) as bounced_amount')
            )
            ->first();

        // Format stats into nested array structure
        $stats = [
            'total_count' => $rawStats->total_count ?? 0,
            'total_amount' => $rawStats->total_amount ?? 0,
            'pending' => [
                'count' => $rawStats->pending_count ?? 0,
                'amount' => $rawStats->pending_amount ?? 0,
            ],
            'cleared' => [
                'count' => $rawStats->cleared_count ?? 0,
                'amount' => $rawStats->cleared_amount ?? 0,
            ],
            'bounced' => [
                'count' => $rawStats->bounced_count ?? 0,
                'amount' => $rawStats->bounced_amount ?? 0,
            ],
        ];

        return view($this->resolveView('index'), compact('cheques', 'stats', 'status', 'search', 'dateFrom', 'dateTo'));
    }

    /**
     * Show the form for creating a new cheque
     */
    public function create()
    {
        $vendors = CreditPurchase::where('shop_id', $this->currentUser()->shop_id)
            ->select('id', 'vendor_name')
            ->distinct()
            ->get();

        return view($this->resolveView('create'), compact('vendors'));
    }

    /**
     * Store a newly created cheque
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cheque_number' => 'required|string|unique:cheques',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'cheque_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'related_to' => 'required|in:vendor_payment,customer_payment,other',
            'related_id' => 'nullable|integer',
            'drawer_name' => 'nullable|string|max:255',
            'payee_name' => 'required|string|max:255',
            'payee_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $cheque = Cheque::create([
            'shop_id' => $this->currentUser()->shop_id,
            'created_by' => auth()->id(),
            'cheque_number' => $validated['cheque_number'],
            'bank_name' => $validated['bank_name'],
            'branch_name' => $validated['branch_name'] ?? null,
            'cheque_date' => $validated['cheque_date'],
            'amount' => $validated['amount'],
            'related_to' => $validated['related_to'],
            'related_id' => $validated['related_id'] ?? null,
            'drawer_name' => $validated['drawer_name'] ?? null,
            'payee_name' => $validated['payee_name'],
            'payee_address' => $validated['payee_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('cheques.show', $cheque->id)
                        ->with('success', 'Cheque recorded successfully');
    }

    /**
     * Display the specified cheque
     */
    public function show(Cheque $cheque)
    {
        // Check authorization
        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $cheque->load('createdBy');
        return view($this->resolveView('show'), compact('cheque'));
    }

    /**
     * Show the form for editing the specified cheque
     */
    public function edit(Cheque $cheque)
    {
        // Staff members cannot edit cheques
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to edit cheques.');
        }

        // Check authorization
        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $vendors = CreditPurchase::where('shop_id', $this->currentUser()->shop_id)
            ->select('id', 'vendor_name')
            ->distinct()
            ->get();

        return view($this->resolveView('edit'), compact('cheque', 'vendors'));
    }

    /**
     * Update the specified cheque
     */
    public function update(Request $request, Cheque $cheque)
    {
        // Staff members cannot update cheques
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update cheques.');
        }

        // Check authorization
        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'cheque_number' => 'required|string|unique:cheques,cheque_number,' . $cheque->id,
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'cheque_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'related_to' => 'required|in:vendor_payment,customer_payment,other',
            'related_id' => 'nullable|integer',
            'drawer_name' => 'nullable|string|max:255',
            'payee_name' => 'required|string|max:255',
            'payee_address' => 'nullable|string',
            'status' => 'required|in:pending,deposited,cleared,bounced,cancelled',
            'deposit_date' => 'nullable|date|required_if:status,deposited,cleared',
            'clearance_date' => 'nullable|date|required_if:status,cleared',
            'bounce_reason' => 'nullable|string|required_if:status,bounced',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $cheque->update($validated);

        return redirect()->route('cheques.show', $cheque->id)
                        ->with('success', 'Cheque updated successfully');
    }

    /**
     * Delete the specified cheque
     */
    public function destroy(Cheque $cheque)
    {
        // Staff members cannot delete cheques
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to delete cheques.');
        }

        // Check authorization
        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $cheque->delete();

        return redirect()->route('cheques.index')
                        ->with('success', 'Cheque deleted successfully');
    }

    /**
     * Mark cheque as deposited
     */
    public function markDeposited(Request $request, Cheque $cheque)
    {
        // Staff members cannot mark cheques as deposited
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update cheque status.');
        }

        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'deposit_date' => 'required|date',
        ]);

        $cheque->update([
            'status' => 'deposited',
            'deposit_date' => $validated['deposit_date'],
        ]);

        return back()->with('success', 'Cheque marked as deposited');
    }

    /**
     * Mark cheque as cleared
     */
    public function markCleared(Request $request, Cheque $cheque)
    {
        // Staff members cannot mark cheques as cleared
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update cheque status.');
        }

        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'clearance_date' => 'required|date',
        ]);

        $cheque->update([
            'status' => 'cleared',
            'clearance_date' => $validated['clearance_date'],
        ]);

        // Record in business transactions when cheque clears
        BusinessTransaction::create([
            'shop_id' => $cheque->shop_id,
            'created_by' => auth()->id(),
            'transaction_date' => $validated['clearance_date'],
            'transaction_type' => 'cheque_cleared',
            'vendor_name' => $cheque->drawer_name ?? $cheque->payee_name,
            'receipt_number' => $cheque->cheque_number,
            'reference_number' => $cheque->reference_number,
            'paid_by' => 'cheque',
            'paid_by_user_id' => auth()->id(),
            'total_amount' => $cheque->amount,
            'net_amount' => $cheque->amount,
            'description' => 'Cheque cleared - ' . $cheque->cheque_number . ' from ' . $cheque->bank_name . ($cheque->notes ? ' - ' . $cheque->notes : ''),
            'category' => $cheque->related_to === 'vendor_payment' ? 'vendor_payment' : 'other',
            'status' => 'completed',
        ]);

        return back()->with('success', 'Cheque marked as cleared');
    }

    /**
     * Mark cheque as bounced
     */
    public function markBounced(Request $request, Cheque $cheque)
    {
        // Staff members cannot mark cheques as bounced
        if ($this->currentUser()->isEmployee()) {
            abort(403, 'Staff members do not have permission to update cheque status.');
        }

        if ($cheque->shop_id !== $this->currentUser()->shop_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'bounce_reason' => 'required|string',
        ]);

        $cheque->update([
            'status' => 'bounced',
            'bounce_reason' => $validated['bounce_reason'],
        ]);

        // Record in business transactions when cheque bounces (failed transaction)
        BusinessTransaction::create([
            'shop_id' => $cheque->shop_id,
            'created_by' => auth()->id(),
            'transaction_date' => now(),
            'transaction_type' => 'cheque_bounced',
            'vendor_name' => $cheque->drawer_name ?? $cheque->payee_name,
            'receipt_number' => $cheque->cheque_number,
            'reference_number' => $cheque->reference_number,
            'paid_by' => 'cheque',
            'paid_by_user_id' => auth()->id(),
            'total_amount' => $cheque->amount,
            'net_amount' => 0, // No actual money received
            'description' => 'Cheque bounced - ' . $cheque->cheque_number . ' - Reason: ' . $validated['bounce_reason'],
            'category' => 'cheque_bounced',
            'status' => 'failed',
        ]);

        return back()->with('success', 'Cheque marked as bounced');
    }

    /**
     * Get cheque statistics
     */
    public function getStats(Request $request)
    {
        $shopId = $this->currentUser()->shop_id;
        $month = $request->get('month', now()->format('Y-m'));

        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            $selectedDate = now();
        }

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();

        $stats = Cheque::where('shop_id', $shopId)
            ->whereBetween('cheque_date', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN status = "cleared" THEN amount ELSE 0 END) as cleared_amount'),
                DB::raw('SUM(CASE WHEN status = "bounced" THEN amount ELSE 0 END) as bounced_amount')
            )
            ->first();

        return compact('stats');
    }
}
