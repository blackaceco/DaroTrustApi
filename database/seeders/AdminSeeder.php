<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // creating a super admin
        $admin = Admin::create([
            'name' => "Super Admin Name",
            'email' => env('DEFAULT_SEEDER_ADMIN_EMAIL', 'super@email.com'),
            'password' => \Illuminate\Support\Facades\Hash::make(env('DEFAULT_SEEDER_ADMIN_PASSWORD', 'password')),
            'superAdmin' => true,
            'status' => "active",
        ]);

        // syncing roles
        $admin->roles()->sync(Role::pluck('id')->toArray());
    }
}
