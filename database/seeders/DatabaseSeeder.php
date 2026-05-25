<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'test',
            'full_name' => 'Test User',
            'role' => 'Customer',
        ]);

        User::factory()->create([
            'username' => 'warehouse',
            'full_name' => 'Nhân viên quản lý kho',
            'role' => 'StaffWarehouse',
        ]);

        User::factory()->create([
            'username' => 'tech',
            'full_name' => 'Nhân viên lắp đặt',
            'role' => 'StaffTech',
        ]);
    }
}
