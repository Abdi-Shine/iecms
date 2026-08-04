<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function page(Request $request)
    {
        $base = auth()->user()->notifications();

        $stats = [
            'total'  => (clone $base)->count(),
            'unread' => (clone $base)->whereNull('read_at')->count(),
            'read'   => (clone $base)->whereNotNull('read_at')->count(),
        ];

        $query = auth()->user()->notifications()->latest();

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $perPage       = $this->resolvePerPage($request);
        $notifications = $query->paginate($perPage)->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
            'stats'          => $stats,
        ]);
    }

    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn($n) => [
                'id'        => $n->id,
                'data'      => $n->data,
                'read_at'   => $n->read_at,
                'created_at'=> $n->created_at->diffForHumans(),
            ]);

        $unread = auth()->user()->unreadNotifications()->count();

        return response()->json(compact('notifications', 'unread'));
    }

    public function markRead(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
