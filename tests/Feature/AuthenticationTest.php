<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration functionality.
     */
    public function test_user_can_register(): void
    {
        // Create project owner role
        $role = Role::create([
            'name' => 'project_owner',
            'display_name' => 'Project Owner',
            'description' => 'Project Owner'
        ]);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role_id' => $role->id,
            ]);

        $response->assertRedirect(route('owner.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'owner',
        ]);
    }

    /**
     * Test user login functionality for project owner role.
     */
    public function test_owner_can_login(): void
    {
        // Create project owner role
        $role = Role::create([
            'name' => 'project_owner',
            'display_name' => 'Project Owner',
            'description' => 'Project Owner'
        ]);

        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        $user->roles()->attach($role->id);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'owner@example.com',
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
    }

    /**
     * Test user login functionality for admin role.
     */
    public function test_admin_can_login(): void
    {
        // Create admin role
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'System Administrator'
        ]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $user->roles()->attach($role->id);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
    }

    /**
     * Test user login functionality for engineer role.
     */
    public function test_engineer_can_login(): void
    {
        // Create engineer role
        $role = Role::create([
            'name' => 'engineer',
            'display_name' => 'Engineer',
            'description' => 'Project Engineer'
        ]);

        $user = User::factory()->create([
            'email' => 'engineer@example.com',
            'password' => bcrypt('password'),
            'role' => 'engineer',
        ]);

        $user->roles()->attach($role->id);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'engineer@example.com',
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
    }

    /**
     * Test user login functionality for contractor role.
     */
    public function test_contractor_can_login(): void
    {
        // Create contractor role
        $role = Role::create([
            'name' => 'contractor',
            'display_name' => 'Contractor',
            'description' => 'Project Contractor'
        ]);

        $user = User::factory()->create([
            'email' => 'contractor@example.com',
            'password' => bcrypt('password'),
            'role' => 'contractor',
        ]);

        $user->roles()->attach($role->id);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'contractor@example.com',
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
    }

    /**
     * Test user cannot login with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

        $this->assertGuest();
    }
}
