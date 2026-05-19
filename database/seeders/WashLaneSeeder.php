<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WashLane;

class WashLaneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lanes = [
            [
                'name' => 'Jalur A',
                'type' => 'general',
                'is_active' => true,
                'max_queue' => 5,
                'description' => 'Jalur umum untuk semua jenis kendaraan'
            ],
            [
                'name' => 'Jalur B',
                'type' => 'motor',
                'is_active' => true,
                'max_queue' => 8,
                'description' => 'Jalur khusus untuk motor dan sepeda motor'
            ],
            [
                'name' => 'Jalur C',
                'type' => 'mobil',
                'is_active' => true,
                'max_queue' => 3,
                'description' => 'Jalur khusus untuk mobil dan kendaraan besar'
            ]
        ];

        foreach ($lanes as $lane) {
            WashLane::create($lane);
        }
    }
}