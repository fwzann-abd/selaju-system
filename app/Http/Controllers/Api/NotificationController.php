<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List notifications for the authenticated account (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $accountId = $request->user()->uuid;
        $perPage = min((int) $request->query('per_page', 15), 50);

        $notifications = Notification::where('account_id', $accountId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'total'        => $notifications->total(),
                'unread_count' => Notification::where('account_id', $accountId)
                    ->whereNull('read_at')
                    ->count(),
            ],
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('account_id', $request->user()->uuid)
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notifikasi ditandai dibaca.']);
    }

    /**
     * Mark ALL notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        Notification::where('account_id', $request->user()->uuid)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Semua notifikasi ditandai dibaca.']);
    }
}
