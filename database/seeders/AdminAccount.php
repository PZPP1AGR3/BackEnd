<?php

namespace Database\Seeders;

use App\Enum\Role;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;
use Str;

class AdminAccount extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Str::random(8);

        $user = User::firstOrCreate(
            [
                'username' => 'admin',
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'role' => Role::Admin,
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->command->info("Admin account created with username 'admin' and password '$password'");
        } else {
            $this->command->info('Admin account already exists.');
        }
    }
}
