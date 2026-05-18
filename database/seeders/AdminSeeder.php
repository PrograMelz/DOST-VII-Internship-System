<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@internship.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
            ]
        );

        // You can also create additional admin accounts here
        User::firstOrCreate(
            ['email' => 'manager@internship.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('manager123'),
            ]
        );
    }
}
