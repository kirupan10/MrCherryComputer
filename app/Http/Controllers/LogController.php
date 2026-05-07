<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    private function resolveView(string $page): string
    {
        $shopType = active_shop_type() ?? 'tech';
        $shopTypeView = "shop-types.{$shopType}.logs.{$page}";

        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        $techView = "shop-types.tech.logs.{$page}";
        if (view()->exists($techView)) {
            return $techView;
        }

        return "logs.{$page}";
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Display a listing of audit logs (only for shop owners and managers)
     */
    public function index(Request $request)
    {
        // Check authorization - staff cannot access audit logs
        if (!$this->currentUser()->canAccessAuditLogs()) {
            abort(403, 'You do not have permission to view Audit Logs.');
        }

        $user = $this->currentUser();
        $activeShop = $user->getActiveShop();
        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $shopId = $activeShop->id;
        $action = $request->get('action');
        $modelType = $request->get('model_type');
        $userId = $request->get('user_id');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $search = $request->get('search');

        $query = AuditLog::where('shop_id', $shopId)
            ->with('user');

        // Filter by action
        if ($action) {
            $query->where('action', $action);
        }

        // Filter by model type
        if ($modelType) {
            $query->where('model_type', $modelType);
        }

        // Filter by user
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filter by date range
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Search in description
        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());

        // Get distinct model types for filter
        $modelTypes = AuditLog::where('shop_id', $shopId)
            ->distinct()
            ->pluck('model_type');

        // Get users for filter
        $users = User::where('shop_id', $shopId)
            ->orderBy('name')
            ->get();

        return view($this->resolveView('index'), compact('logs', 'modelTypes', 'users', 'action', 'modelType', 'userId', 'fromDate', 'toDate', 'search'));
    }

    /**
     * Display the specified audit log
     */
    public function show(int $id)
    {
        // Check authorization - staff cannot access audit logs
        if (!$this->currentUser()->canAccessAuditLogs()) {
            abort(403, 'You do not have permission to view Audit Logs.');
        }

        $user = $this->currentUser();
        $activeShop = $user->getActiveShop();
        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $log = AuditLog::where('shop_id', $activeShop->id)
            ->with('user')
            ->findOrFail($id);

        return view($this->resolveView('show'), compact('log'));
    }
}
