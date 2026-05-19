<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cuci Standar',
                'description' => 'Cuci luar & vakum dasar',
                'price' => 50000,
                'duration_minutes' => 30,
                'type' => 'standard'
            ],
            [
                'name' => 'Cuci Premium',
                'description' => 'Wax, semir ban & interior',
                'price' => 85000,
                'duration_minutes' => 45,
                'type' => 'premium'
            ],
            [
                'name' => 'Detail Lengkap',
                'description' => 'Pembersihan mesin & kerak',
                'price' => 150000,
                'duration_minutes' => 90,
                'type' => 'detail'
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
