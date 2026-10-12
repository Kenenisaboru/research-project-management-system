<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/multi-role-gate', function () {
            return 'ok';
        })->middleware('role:coordinator,admin');
    }

    public function test_multi_role_routes_allow_any_allowed_role(): void
    {
        $coordinator = User::factory()->create(['role' => 'coordinator']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($coordinator)
            ->get('/multi-role-gate')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/multi-role-gate')
            ->assertOk();
    }

    public function test_multi_role_routes_reject_disallowed_roles(): void
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);

        $this->actingAs($reviewer)
            ->get('/multi-role-gate')
            ->assertForbidden();
    }
}
