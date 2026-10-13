<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_login_returns_token_and_user_resource()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@rpms.local',
            'password' => 'UNI@Demo1',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                ],
                'errors',
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'type' => 'Bearer',
                    'user' => [
                        'email' => 'admin@rpms.local',
                        'role'  => 'admin',
                    ],
                ],
            ]);
    }

    public function test_api_login_rejects_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@rpms.local',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'data'    => null,
            ]);
    }

    public function test_authenticated_user_can_access_me_and_logout()
    {
        $user = User::where('email', 'admin@rpms.local')->first();
        $token = $user->createToken('test-suite')->plainTextToken;

        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'email' => 'admin@rpms.local',
                ],
            ]);

        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_authenticated_user_can_list_projects()
    {
        $user = User::where('email', 'admin@rpms.local')->first();
        $token = $user->createToken('test-suite')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/projects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'items',
                    'pagination' => [
                        'current_page',
                        'total',
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_fetch_dashboard_stats()
    {
        $user = User::where('email', 'admin@rpms.local')->first();
        $token = $user->createToken('test-suite')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'role',
                    'stats',
                    'alerts',
                ],
            ]);
    }

    public function test_authenticated_user_can_fetch_unread_notifications_count()
    {
        $user = User::where('email', 'admin@rpms.local')->first();
        $token = $user->createToken('test-suite')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'unread_count',
                ],
            ]);
    }

    public function test_authenticated_user_can_fetch_analytics()
    {
        $user = User::where('email', 'admin@rpms.local')->first();
        $token = $user->createToken('test-suite')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/analytics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overview',
                    'financials',
                    'status_distribution',
                    'thematic_distribution',
                ],
            ]);
    }

    public function test_unauthenticated_request_is_rejected_on_protected_endpoints()
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(401);
    }
}
