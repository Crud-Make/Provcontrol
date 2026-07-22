<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@provcontrol.com',
            'password' => Hash::make('password'),
            'role' => Role::Admin->value,
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
