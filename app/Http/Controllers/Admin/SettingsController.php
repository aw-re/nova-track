<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the system settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all settings from database or cache
        $settings = $this->getAllSettings();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'session_timeout' => 'required|integer|min:1|max:1440',
            'max_login_attempts' => 'required|integer|min:1|max:10',
            'max_file_size' => 'required|integer|min:1|max:100',
            'allowed_file_types' => 'required|string',
        ]);

        // Update or create settings
        foreach ($request->except('_token', '_method') as $key => $value) {
            // For checkboxes, convert to boolean
            if (in_array($key, ['enable_email_notifications', 'enable_welcome_email', 'enable_two_factor', 'force_password_change'])) {
                $value = $request->has($key) ? true : false;
            }
            
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear settings cache
        Cache::forget('settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Get all settings from database or cache.
     *
     * @return array
     */
    private function getAllSettings()
    {
        // Try to get settings from cache
        if (Cache::has('settings')) {
            return Cache::get('settings');
        }

        // Get settings from database
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Store in cache for future requests
        Cache::put('settings', $settings, now()->addDay());

        return $settings;
    }
}
