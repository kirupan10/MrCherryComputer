<?php

namespace App\Http\Controllers;

use App\Models\BusinessTransaction;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessTransactionController extends Controller
{
    private function resolveView(string $page): string
    {
        $shopType = active_shop_type() ?? 'tech';
        $shopTypeView = "shop-types.{$shopType}.business-transactions.{$page}";
        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        $techView = "shop-types.tech.business-transactions.{$page}";
        if (view()->exists($techView)) {
            return $techView;
        }

        return "business-transactions.{$page}";
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Authorization check - staff cannot access business transactions
        if (!$this->currentUser()->canAccessTransactions()) {
            abort(403, 'You do not have permission to access Business Transactions.');
        }

        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $query = BusinessTransaction::where('shop_id', $activeShop->id)
            ->with(['paidByUser']);

        // Filter by transaction type
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')->paginate(20);

        // Get summary statistics
        $stats = [
            'total_transactions' => BusinessTransaction::where('shop_id', $activeShop->id)->count(),
            'total_amount' => BusinessTransaction::where('shop_id', $activeShop->id)
                ->where('status', 'completed')
                ->sum('net_amount'),
            'pending_count' => BusinessTransaction::where('shop_id', $activeShop->id)
                ->where('status', 'pending')
                ->count(),
            'this_month' => BusinessTransaction::where('shop_id', $activeShop->id)
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('net_amount'),
        ];

        return view($this->resolveView('index'), compact('transactions', 'stats', 'activeShop'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Authorization check - staff cannot create business transactions
        if (!$this->currentUser()->canEditFinanceRecords()) {
            abort(403, 'You do not have permission to create Business Transactions.');
        }

        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        // Get all users in the shop
        $users = User::where('shop_id', $activeShop->id)->get();

        return view($this->resolveView('create'), compact('activeShop', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Authorization check - staff cannot store business transactions
        if (!$this->currentUser()->canEditFinanceRecords()) {
            abort(403, 'You do not have permission to create Business Transactions.');
        }

        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|string|max:50',
            'vendor_name' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'paid_by' => 'nullable|string|max:50',
            'paid_by_user_id' => 'nullable|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            // 'status' => 'required|in:completed,pending,cancelled', // Removed for Pay action
            'items' => 'nullable|array',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Calculate net amount
        $validated['net_amount'] = $validated['total_amount']
            - ($validated['discount_amount'] ?? 0);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('transaction-attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        // Add shop_id and created_by
        $validated['shop_id'] = $activeShop->id;
        $validated['created_by'] = auth()->id();

        $transaction = BusinessTransaction::create($validated);

        // Create corresponding Expense record only for specific transaction types
        $expenseTypes = ['expense', 'owner_personal', 'commission', 'other'];
        if (in_array($validated['transaction_type'], $expenseTypes)) {
            \App\Models\Expense::create([
                'type' => ucfirst(str_replace('_', ' ', $validated['transaction_type'])),
                'amount' => $validated['net_amount'], // Amount in normal format (model accessor will handle display)
                'expense_date' => $validated['transaction_date'],
                'notes' => $validated['description'] ?? "Transaction #{$transaction->id} - {$validated['vendor_name']}",
                'details' => [
                    'transaction_id' => $transaction->id,
                    'vendor_name' => $validated['vendor_name'] ?? null,
                    'receipt_number' => $validated['receipt_number'] ?? null,
                    'category' => $validated['category'] ?? null,
                ],
                'shop_id' => $activeShop->id,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('business-transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessTransaction $transaction)
    {
        // Authorization check - staff cannot view business transactions
        if (!$this->currentUser()->canAccessTransactions()) {
            abort(403, 'You do not have permission to view Business Transactions.');
        }

        // Check if user has access to this transaction's shop
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop || $transaction->shop_id !== $activeShop->id) {
            abort(403, 'Unauthorized access to this transaction.');
        }

        $transaction->load(['creator', 'shop', 'paidByUser']);

        return view($this->resolveView('show'), [
            'businessTransaction' => $transaction,
            'activeShop' => $activeShop
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessTransaction $transaction)
    {
        // Authorization check - staff cannot edit business transactions
        if (!$this->currentUser()->canEditFinanceRecords()) {
            abort(403, 'You do not have permission to edit Business Transactions.');
        }

        // Check if user has access to this transaction's shop
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop || $transaction->shop_id !== $activeShop->id) {
            abort(403, 'Unauthorized access to this transaction.');
        }

        // Get all users in the shop
        $users = User::where('shop_id', $activeShop->id)->get();

        return view($this->resolveView('edit'), [
            'businessTransaction' => $transaction,
            'activeShop' => $activeShop,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessTransaction $transaction)
    {
        // Authorization check - staff cannot update business transactions
        if (!$this->currentUser()->canEditFinanceRecords()) {
            abort(403, 'You do not have permission to update Business Transactions.');
        }

        // Check if user has access to this transaction's shop
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop || $transaction->shop_id !== $activeShop->id) {
            abort(403, 'Unauthorized access to this transaction.');
        }

        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|string|max:50',
            'vendor_name' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'paid_by' => 'nullable|string|max:50',
            'paid_by_user_id' => 'nullable|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:completed,pending,cancelled',
            'items' => 'nullable|array',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Calculate net amount
        $validated['net_amount'] = $validated['total_amount']
            - ($validated['discount_amount'] ?? 0);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }

            $path = $request->file('attachment')->store('transaction-attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        $transaction->update($validated);

        return redirect()->route('business-transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessTransaction $transaction)
    {
        // Authorization check - staff cannot delete business transactions
        if (!$this->currentUser()->canEditFinanceRecords()) {
            abort(403, 'You do not have permission to delete Business Transactions.');
        }

        // Check if user has access to this transaction's shop
        $activeShop = $this->currentUser()->getActiveShop();

        if (!$activeShop || $transaction->shop_id !== $activeShop->id) {
            abort(403, 'Unauthorized access to this transaction.');
        }

        // Only shop owners can delete transactions
        if (!$this->currentUser()->isShopOwner() && !$this->currentUser()->isAdmin()) {
            return redirect()->route('business-transactions.show', $transaction)
                ->with('error', 'Only shop owners can delete transactions.');
        }

        // Delete attachment if exists
        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }

        $transaction->delete();

        return redirect()->route('business-transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}
