<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_filament_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_filament_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@nodus.com',
            'password' => 'password',
            'role' => 'admin',
            'trust_score' => 5.0,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_regular_user_cannot_access_filament_dashboard(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@nodus.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }
}
