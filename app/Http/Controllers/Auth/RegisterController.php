<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $roles = Role::whereIn('name', ['project_owner', 'engineer', 'contractor'])->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Get the role name to set in the role column
        $role = Role::find($request->role_id);
        $roleName = null;
        
        if ($role->name === 'project_owner') {
            $roleName = 'owner';
        } elseif ($role->name === 'engineer' || $role->name === 'contractor') {
            $roleName = $role->name;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $roleName, // Set the role column directly
        ]);

        // Attach role to user through the relationship as well
        $user->roles()->attach($request->role_id);

        // Log the user in
        Auth::login($user);

        // Redirect based on user role
        if ($role->name === 'project_owner') {
            return redirect()->route('owner.dashboard');
        } elseif ($role->name === 'engineer') {
            return redirect()->route('engineer.dashboard');
        } elseif ($role->name === 'contractor') {
            return redirect()->route('contractor.dashboard');
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
