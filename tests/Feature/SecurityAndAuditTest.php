<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use App\Services\PasswordPolicy;
use App\Services\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_security_headers_middleware_is_applied()
    {
        $response = $this->get('/');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    public function test_account_locked_middleware_denies_inactive_users()
    {
        $user = User::factory()->create([
            'status'   => 'inactive',
            'role'     => 'pi',
            'password' => Hash::make('Secret@12345'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_password_policy_service_validates_strength()
    {
        $weak = PasswordPolicy::validateStrength('simple');
        $this->assertFalse($weak['valid']);
        $this->assertNotEmpty($weak['errors']);

        $strong = PasswordPolicy::validateStrength('StrongPass!2026');
        $this->assertTrue($strong['valid']);
        $this->assertEmpty($strong['errors']);
    }

    public function test_totp_service_generates_and_verifies_codes()
    {
        $secret = TotpService::generateSecret();
        $this->assertEquals(16, strlen($secret));

        $code = TotpService::getCurrentCode($secret);
        $this->assertEquals(6, strlen($code));
        $this->assertTrue(TotpService::verifyCode($secret, $code));
        $this->assertFalse(TotpService::verifyCode($secret, '000000'));
    }

    public function test_totp_recovery_codes_generation()
    {
        $recoveryCodes = TotpService::generateRecoveryCodes(8);
        $this->assertCount(8, $recoveryCodes);
        foreach ($recoveryCodes as $code) {
            $this->assertMatchesRegularExpression('/^[A-Z0-9]{5}-[A-Z0-9]{5}$/', $code);
        }
    }

    public function test_audit_service_logs_metadata_and_diff()
    {
        $user = User::where('role', 'admin')->first();
        $this->actingAs($user);

        $log = AuditService::log('SECURITY_TEST', 'User', $user->id, 'Testing audit log creation');

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertEquals('SECURITY_TEST', $log->action);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertNotNull($log->ip_address);

        // Test diff snapshot
        $diffLog = AuditService::diff('UPDATE_USER', $user, ['name' => 'Old Name'], ['name' => 'New Name']);
        $this->assertNotNull($diffLog->diff_json);
        $this->assertArrayHasKey('changes', $diffLog->diff_json);
        $this->assertEquals('Old Name', $diffLog->diff_json['changes']['name']['before']);
        $this->assertEquals('New Name', $diffLog->diff_json['changes']['name']['after']);
    }

    public function test_admin_can_export_audit_logs_pdf()
    {
        $admin = User::where('role', 'admin')->first();

        // Seed an audit log
        AuditService::log('EXPORT_TEST', 'User', $admin->id, 'Test export event');

        $response = $this->actingAs($admin)->get('/admin/audit-logs/pdf');
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type', ''), 'application/pdf') ||
            str_contains($response->headers->get('content-disposition', ''), 'UNI_Audit_Trail')
        );
    }
}
