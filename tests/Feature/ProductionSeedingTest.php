<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seed_does_not_create_demo_accounts_by_default(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production', 'rpms.seed_demo_data' => false]);

        $this->app->make(\Database\Seeders\DatabaseSeeder::class)->run();

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseMissing('users', ['email' => 'admin@rpms.local']);
    }

    public function test_production_administrator_can_be_provisioned_with_a_unique_password(): void
    {
        $this->seed(\Database\Seeders\RbacSeeder::class);

        $this->artisan('rpms:create-admin')
            ->expectsQuestion('Staff ID', 'PROD-ADMIN-01')
            ->expectsQuestion('Full name', 'Production Administrator')
            ->expectsQuestion('Email address', 'admin@research.example')
            ->expectsQuestion('Password', 'SecureAdmin123!')
            ->expectsQuestion('Confirm password', 'SecureAdmin123!')
            ->expectsOutput('Administrator account created for admin@research.example.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@research.example',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
