<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function auditLogs(Request $request)
    {
        $user = $request->user();
        $activeShop = method_exists($user, 'getActiveShop') ? $user->getActiveShop() : null;
        $shopId = $activeShop?->id ?? $user->shop_id;

        if (!$shopId) {
            return response()->json(['logs' => []]);
        }

        $logs = AuditLog::with('user:id,name')
            ->where('shop_id', $shopId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id'          => $log->id,
                    'action'      => ucfirst($log->action ?? 'updated'),
                    'model_type'  => $log->model_type ?? 'Record',
                    'description' => $log->description ?? '',
                    'user'        => $log->user?->name ?? 'System',
                    'created_at'  => $log->created_at->format('d M Y, h:i A'),
                    'relative'    => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json(['logs' => $logs]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()->latest()->take(25)->get()->map(function ($notification) {
            $data = $notification->data ?? [];
            $oldSelling = array_key_exists('old_selling_price', $data) && $data['old_selling_price'] !== null
                ? 'LKR ' . number_format((float) $data['old_selling_price'], 2)
                : '-';
            $newSelling = array_key_exists('new_selling_price', $data) && $data['new_selling_price'] !== null
                ? 'LKR ' . number_format((float) $data['new_selling_price'], 2)
                : '-';

            return [
                'id' => $notification->id,
                'title' => 'Product Edit Alert',
                'message' => ($data['product_name'] ?? 'Product') . ' selling price changed from ' . $oldSelling . ' to ' . $newSelling,
                'meta' => trim(($data['product_code'] ?? '') . ' · by ' . ($data['updated_by'] ?? 'System'), ' ·'),
                'label' => 'Price Update',
                'tone' => 'primary',
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
                'sort_at' => $notification->created_at->timestamp,
            ];
        });

        $activeShop = method_exists($user, 'getActiveShop') ? $user->getActiveShop() : null;
        $shopId = $activeShop?->id ?? $user->shop_id;

        $auditAlerts = collect();
        if ($shopId) {
            $auditAlerts = AuditLog::with('user:id,name')
                ->where('shop_id', $shopId)
                ->where(function ($query) {
                    $query->where(function ($productQuery) {
                        $productQuery->where('model_type', 'Product')
                            ->where(function ($innerQuery) {
                                $innerQuery->where('description', 'like', '%Stock updated%')
                                    ->orWhere('description', 'like', '%product updated%')
                                    ->orWhere('description', 'like', '%Quick price update%');
                            });
                    })->orWhere(function ($orderQuery) {
                        $orderQuery->where('model_type', 'Order')
                            ->where('action', 'update');
                    });
                })
                ->latest()
                ->take(25)
                ->get()
                ->map(function ($log) {
                    $description = $log->description ?? 'Activity updated';
                    $title = 'Activity Alert';
                    $label = 'Activity';
                    $tone = 'secondary';

                    if (str_contains(strtolower($description), 'stock updated')) {
                        $title = 'Product Stock Update Alert';
                        $label = 'Stock Update';
                        $tone = 'warning';
                    } elseif ($log->model_type === 'Product') {
                        $title = 'Product Edit Alert';
                        $label = 'Product Edit';
                        $tone = 'primary';
                    } elseif ($log->model_type === 'Order') {
                        $title = 'Sales Edit Alert';
                        $label = 'Sales Edit';
                        $tone = 'success';
                    }

                    return [
                        'id' => 'audit-' . $log->id,
                        'title' => $title,
                        'message' => $description,
                        'meta' => trim(($log->model_type ?? 'Record') . ' · by ' . ($log->user?->name ?? 'System'), ' ·'),
                        'label' => $label,
                        'tone' => $tone,
                        'read_at' => $log->created_at,
                        'created_at' => $log->created_at->diffForHumans(),
                        'sort_at' => $log->created_at->timestamp,
                    ];
                });
        }

        $items = $notifications->concat($auditAlerts)
            ->sortByDesc('sort_at')
            ->take(50)
            ->values()
            ->map(function ($item) {
                unset($item['sort_at']);
                return $item;
            });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $items,
        ]);
    }
}
