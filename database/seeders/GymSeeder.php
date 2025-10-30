<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('gyms')->insert([
            [
                'name' => 'Gym Principal',
                'address' => 'Av. Principal #123',
                'phone' => '7771234567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sucursal Norte',
                'address' => 'Av. Norte #456',
                'phone' => '7777654321',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}