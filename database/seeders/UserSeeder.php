<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'RAI Admin',
            'email'    => 'admin@raimotorcycleparts.ph',
            'phone'    => '09171234567',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Staff
        User::create([
            'name'     => 'Juan Catalog',
            'email'    => 'catalog@raimotorcycleparts.ph',
            'phone'    => '09179876543',
            'password' => Hash::make('password'),
            'role'     => 'catalog_manager',
        ]);

        User::create([
            'name'     => 'Pedro Packer',
            'email'    => 'packer@raimotorcycleparts.ph',
            'phone'    => '09185551234',
            'password' => Hash::make('password'),
            'role'     => 'packer',
        ]);

        // Sample customers
        $customers = [
            ['name' => 'Carlo Reyes',       'email' => 'carlo@example.com',   'phone' => '09201234567'],
            ['name' => 'Maria Santos',      'email' => 'maria@example.com',   'phone' => '09157654321'],
            ['name' => 'Kevin Dela Cruz',   'email' => 'kevin@example.com',   'phone' => '09178889999'],
            ['name' => 'Jasmine Villanueva','email' => 'jasmine@example.com', 'phone' => '09269998888'],
            ['name' => 'Mark Aquino',       'email' => 'mark@example.com',    'phone' => '09191112222'],
        ];

        foreach ($customers as $c) {
            User::create(array_merge($c, [
                'password'       => Hash::make('password'),
                'role'           => 'customer',
                'loyalty_points' => rand(0, 500),
            ]));
        }
    }
}
