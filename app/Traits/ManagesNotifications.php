<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ManagesNotifications
{
    /**
     * Display the user's notifications.
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        return view($this->getNotificationsViewPath('index'), compact('notifications', 'unreadCount'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()
            ->with('success', __('messages.success.updated', ['model' => __('app.notifications')]));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markNotificationAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()
            ->with('success', __('messages.success.updated', ['model' => __('app.notification')]));
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()
            ->with('success', __('messages.success.deleted', ['model' => __('app.notification')]));
    }

    /**
     * Get the notifications view path based on role.
     */
    protected function getNotificationsViewPath(string $view): string
    {
        $role = $this->getRolePrefix();
        return "{$role}.notifications.{$view}";
    }

    /**
     * Get the role prefix for views and routes.
     * This should be overridden by the using controller.
     */
    protected function getRolePrefix(): string
    {
        return 'shared';
    }
}
