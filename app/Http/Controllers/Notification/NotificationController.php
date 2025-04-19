<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $user = Auth::user();
        
        $notifications = $user->userNotifications()
            ->latest()
            ->paginate(20);
            
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     * This is typically not used directly as notifications are system-generated.
     */
    public function create()
    {
        // Only admin can manually create notifications
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You do not have permission to create notifications.');
        }
        
        $users = \App\Models\User::all();
        
        return view('notifications.create', compact('users'));
    }

    /**
     * Store a newly created notification in storage.
     * This is typically not used directly as notifications are system-generated.
     */
    public function store(Request $request)
    {
        // Only admin can manually create notifications
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You do not have permission to create notifications.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:100',
            'related_id' => 'nullable|integer',
            'related_type' => 'nullable|string|max:100',
        ]);
        
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'related_id' => $request->related_id,
            'related_type' => $request->related_type,
            'is_read' => false,
        ]);
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        $user = Auth::user();
        
        // Users can only view their own notifications
        if ($notification->user_id !== $user->id && !$user->isAdmin()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You do not have permission to view this notification.');
        }
        
        // Mark as read if not already
        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        
        return view('notifications.show', compact('notification'));
    }

    /**
     * Mark the specified notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        // Users can only mark their own notifications
        if ($notification->user_id !== $user->id && !$user->isAdmin()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You do not have permission to update this notification.');
        }
        
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->userNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            
        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        
        // Users can only delete their own notifications
        if ($notification->user_id !== $user->id && !$user->isAdmin()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You do not have permission to delete this notification.');
        }
        
        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Get unread notifications count for the current user.
     * This is used for AJAX requests to update the notification badge.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $count = $user->userNotifications()
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * Create a system notification.
     * This is a static method that can be called from other controllers.
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param string|null $type
     * @param int|null $relatedId
     * @param string|null $relatedType
     * @return Notification
     */
    public static function createSystemNotification($userId, $title, $message, $type = null, $relatedId = null, $relatedType = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'is_read' => false,
        ]);
    }
}
