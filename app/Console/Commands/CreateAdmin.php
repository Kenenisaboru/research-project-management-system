<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'rpms:create-admin';

    protected $description = 'Create a production system administrator account';

    public function handle(): int
    {
        $staffId = $this->ask('Staff ID');
        $name = $this->ask('Full name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');
        $confirmation = $this->secret('Confirm password');

        $validator = Validator::make([
            'staff_id' => $staffId,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'staff_id' => 'required|string|max:100|unique:users,staff_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $admin = User::create([
            'staff_id' => $staffId,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        \App\Services\RbacService::syncUserRole($admin);
        $this->info("Administrator account created for {$admin->email}.");

        return self::SUCCESS;
    }
}
