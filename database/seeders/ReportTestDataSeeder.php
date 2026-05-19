<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WashOrder;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Models\WashLane;
use Carbon\Carbon;

class ReportTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $services = Service::all();
        $staff = Staff::all();
        $user = User::first();
        $washLanes = WashLane::all();

        if ($services->isEmpty() || $staff->isEmpty() || !$user) {
            $this->command->error('Please ensure you have services, staff, and users in the database first.');
            return;
        }

        // Create sample vehicles
        $vehicles = [
            ['license_plate' => 'B1234ABC', 'type' => 'Mobil', 'model' => 'Toyota Avanza', 'color' => 'Putih'],
            ['license_plate' => 'D5678EFG', 'type' => 'Motor', 'model' => 'Honda Beat', 'color' => 'Merah'],
            ['license_plate' => 'F9012HIJ', 'type' => 'Mobil', 'model' => 'Honda Jazz', 'color' => 'Hitam'],
            ['license_plate' => 'B3456KLM', 'type' => 'Motor', 'model' => 'Yamaha Mio', 'color' => 'Biru'],
            ['license_plate' => 'D7890NOP', 'type' => 'Mobil', 'model' => 'Suzuki Ertiga', 'color' => 'Silver'],
            ['license_plate' => 'F2345QRS', 'type' => 'Motor', 'model' => 'Honda Vario', 'color' => 'Putih'],
            ['license_plate' => 'B6789TUV', 'type' => 'Mobil', 'model' => 'Toyota Innova', 'color' => 'Hitam'],
            ['license_plate' => 'D1234WXY', 'type' => 'Motor', 'model' => 'Suzuki Nex', 'color' => 'Kuning'],
        ];

        $createdVehicles = [];
        foreach ($vehicles as $vehicleData) {
            $createdVehicles[] = Vehicle::firstOrCreate(
                ['license_plate' => $vehicleData['license_plate']],
                $vehicleData
            );
        }

        // Create sample orders for the last 30 days
        $statuses = ['completed', 'completed', 'completed', 'in_progress', 'pending'];
        $paymentMethods = ['cash', 'qris', 'transfer', null];
        $paymentStatuses = ['paid', 'paid', 'paid', 'unpaid'];

        for ($i = 0; $i < 50; $i++) {
            $randomDate = Carbon::now()->subDays(rand(0, 30));
            $randomHour = rand(8, 18); // Business hours 8 AM to 6 PM
            $randomDate->setHour($randomHour)->setMinute(rand(0, 59));

            $vehicle = $createdVehicles[array_rand($createdVehicles)];
            $service = $services->random();
            $selectedStaff = $staff->random();
            $status = $statuses[array_rand($statuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            
            // If status is completed, ensure payment is paid
            if ($status === 'completed') {
                $paymentStatus = 'paid';
                if (!$paymentMethod) {
                    $paymentMethod = ['cash', 'qris', 'transfer'][array_rand(['cash', 'qris', 'transfer'])];
                }
            }

            $additionalFee = rand(0, 10) > 7 ? rand(5000, 15000) : 0;
            $totalPrice = $service->price + $additionalFee;

            // Auto-assign wash lane
            $washLane = null;
            if ($washLanes->isNotEmpty()) {
                $availableLanes = $washLanes->filter(function($lane) use ($vehicle) {
                    return $lane->type === 'general' || 
                           strtolower($lane->type) === strtolower($vehicle->type);
                });
                $washLane = $availableLanes->isNotEmpty() ? $availableLanes->random() : null;
            }

            $orderData = [
                'vehicle_id' => $vehicle->id,
                'service_id' => $service->id,
                'staff_id' => $selectedStaff->id,
                'user_id' => $user->id,
                'wash_lane_id' => $washLane?->id,
                'queue_position' => $washLane ? rand(1, 3) : null,
                'order_number' => 'WO' . $randomDate->format('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'base_price' => $service->price,
                'additional_fee' => $additionalFee,
                'total_price' => $totalPrice,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];

            // Set timestamps based on status
            if ($status === 'in_progress' || $status === 'completed') {
                $orderData['started_at'] = $randomDate->copy()->addMinutes(rand(5, 30));
                $orderData['queued_at'] = $randomDate;
                $orderData['lane_started_at'] = $orderData['started_at'];
            }

            if ($status === 'completed') {
                $orderData['completed_at'] = $orderData['started_at']->copy()->addMinutes($service->duration_minutes + rand(-10, 20));
            }

            WashOrder::create($orderData);
        }

        $this->command->info('Created 50 sample wash orders for testing reports.');
    }
}