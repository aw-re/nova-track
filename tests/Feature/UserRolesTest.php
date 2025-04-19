<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin login and dashboard access.
     */
    public function test_admin_login_and_dashboard_access(): void
    {
        // Create admin role
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'System Administrator'
        ]);
        
        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        
        // Attach admin role
        $admin->roles()->attach($adminRole->id);
        
        // Test login with CSRF token
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);
        
        // Should redirect to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));
        
        // Test dashboard access
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);
    }
    
    /**
     * Test project owner login and dashboard access.
     */
    public function test_owner_login_and_dashboard_access(): void
    {
        // Create project owner role
        $ownerRole = Role::create([
            'name' => 'project_owner',
            'display_name' => 'Project Owner',
            'description' => 'Project Owner'
        ]);
        
        // Create project owner user
        $owner = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);
        
        // Attach project owner role
        $owner->roles()->attach($ownerRole->id);
        
        // Test login with CSRF token
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'owner@example.com',
                'password' => 'password',
            ]);
        
        // Should redirect to owner dashboard
        $response->assertRedirect(route('owner.dashboard'));
        
        // Test dashboard access
        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertStatus(200);
    }
    
    /**
     * Test engineer login and dashboard access.
     */
    public function test_engineer_login_and_dashboard_access(): void
    {
        // Create engineer role
        $engineerRole = Role::create([
            'name' => 'engineer',
            'display_name' => 'Engineer',
            'description' => 'Project Engineer'
        ]);
        
        // Create engineer user
        $engineer = User::factory()->create([
            'email' => 'engineer@example.com',
            'password' => bcrypt('password'),
            'role' => 'engineer'
        ]);
        
        // Attach engineer role
        $engineer->roles()->attach($engineerRole->id);
        
        // Test login with CSRF token
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'engineer@example.com',
                'password' => 'password',
            ]);
        
        // Should redirect to engineer dashboard
        $response->assertRedirect(route('engineer.dashboard'));
        
        // Test dashboard access
        $this->actingAs($engineer)
            ->get(route('engineer.dashboard'))
            ->assertStatus(200);
    }
    
    /**
     * Test contractor login and dashboard access.
     */
    public function test_contractor_login_and_dashboard_access(): void
    {
        // Create contractor role
        $contractorRole = Role::create([
            'name' => 'contractor',
            'display_name' => 'Contractor',
            'description' => 'Project Contractor'
        ]);
        
        // Create contractor user
        $contractor = User::factory()->create([
            'email' => 'contractor@example.com',
            'password' => bcrypt('password'),
            'role' => 'contractor'
        ]);
        
        // Attach contractor role
        $contractor->roles()->attach($contractorRole->id);
        
        // Test login with CSRF token
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/login', [
                'email' => 'contractor@example.com',
                'password' => 'password',
            ]);
        
        // Should redirect to contractor dashboard
        $response->assertRedirect(route('contractor.dashboard'));
        
        // Test dashboard access
        $this->actingAs($contractor)
            ->get(route('contractor.dashboard'))
            ->assertStatus(200);
    }
    
    /**
     * Test registration and role assignment.
     */
    public function test_user_registration_and_role_assignment(): void
    {
        // Create roles
        $ownerRole = Role::create([
            'name' => 'project_owner',
            'display_name' => 'Project Owner',
            'description' => 'Project Owner'
        ]);
        
        $engineerRole = Role::create([
            'name' => 'engineer',
            'display_name' => 'Engineer',
            'description' => 'Project Engineer'
        ]);
        
        $contractorRole = Role::create([
            'name' => 'contractor',
            'display_name' => 'Contractor',
            'description' => 'Project Contractor'
        ]);
        
        // Test registration as project owner with CSRF token
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/register', [
                'name' => 'Test Owner',
                'email' => 'test.owner@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'phone' => '1234567890',
                'address' => '123 Test St',
                'role_id' => $ownerRole->id,
            ]);
        
        // Should redirect to owner dashboard
        $response->assertRedirect(route('owner.dashboard'));
        
        // Verify user was created with correct role
        $this->assertDatabaseHas('users', [
            'email' => 'test.owner@example.com',
            'role' => 'owner',
        ]);
        
        // Verify role relationship was created
        $user = User::where('email', 'test.owner@example.com')->first();
        $this->assertTrue($user->roles()->where('name', 'project_owner')->exists());
    }
}
