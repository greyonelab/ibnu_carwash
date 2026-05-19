<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            [
                'name' => 'Ahmad Subarjo',
                'phone' => '081234567890',
                'position' => 'Senior Washer',
                'commission_rate' => 15.00
            ],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567891',
                'position' => 'Washer',
                'commission_rate' => 12.00
            ],
            [
                'name' => 'Dedi Kurniawan',
                'phone' => '081234567892',
                'position' => 'Washer',
                'commission_rate' => 10.00
            ]
        ];

        foreach ($staff as $member) {
            Staff::create($member);
        }
    }
}
