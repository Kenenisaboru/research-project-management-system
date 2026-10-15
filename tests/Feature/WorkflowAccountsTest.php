<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkflowAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_workflow_accounts_have_separate_role_dashboards(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $accounts = [
            'pi@rpms.local' => 'pi',
            'tm@rpms.local' => 'tm',
            'dh@rpms.local' => 'dh',
            'coordinator@rpms.local' => 'coordinator',
            'reviewer@rpms.local' => 'reviewer',
            'reviewer2@rpms.local' => 'reviewer',
            'dean@rpms.local' => 'dean',
            'irerc@rpms.local' => 'irerc',
            'vp@rpms.local' => 'vparttcs',
            'rcsc@rpms.local' => 'rcsc',
            'finance@rpms.local' => 'finance',
            'admin@rpms.local' => 'admin',
        ];

        foreach ($accounts as $email => $role) {
            $user = User::where('email', $email)->firstOrFail();

            $this->assertSame($role, $user->role);
            $this->assertTrue(Hash::check('UNI@Demo1', $user->password));
            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertOk();
        }
    }

    public function test_role_switcher_route_is_removed(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('switch-role'));
        $this->get('/switch-role/admin')->assertNotFound();
    }

    public function test_workflow_credentials_are_shown_only_in_local_environment(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Workflow test accounts');

        $this->app['env'] = 'local';
        config(['app.env' => 'local']);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Workflow test accounts')
            ->assertSee('pi@rpms.local')
            ->assertSee('reviewer2@rpms.local')
            ->assertSee('UNI@Demo1')
            ->assertDontSee('Sign in as demo administrator');
    }

    public function test_public_home_and_login_use_distinct_guest_layouts(): void
    {
        $home = $this->get(route('home'))->assertOk();
        $this->assertMatchesRegularExpression('/<body class="[^"]*guest-screen[^"]*">/', $home->getContent());
        $home->assertSee('guest-login-link')
            ->assertSee('site-footer mt-auto');

        $login = $this->get(route('login'))->assertOk();
        $this->assertMatchesRegularExpression('/<body class="[^"]*auth-screen[^"]*">/', $login->getContent());
        $this->assertDoesNotMatchRegularExpression('/<body class="[^"]*guest-screen[^"]*">/', $login->getContent());
    }
}
