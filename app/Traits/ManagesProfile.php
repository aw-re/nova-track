<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait ManagesProfile
{
    /**
     * Show the user's profile edit form.
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view($this->getProfileViewPath('edit'), compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return redirect()->back()
            ->with('success', __('messages.success.profile_updated'));
    }

    /**
     * Show the change password form.
     */
    public function showChangePasswordForm()
    {
        return view($this->getProfileViewPath('change-password'));
    }

    /**
     * Update the user's password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()
                ->with('error', __('messages.error.password_mismatch'));
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()
            ->with('success', __('messages.success.password_changed'));
    }

    /**
     * Get the profile view path based on role.
     */
    protected function getProfileViewPath(string $view): string
    {
        $role = $this->getRolePrefix();
        return "{$role}.profile.{$view}";
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
