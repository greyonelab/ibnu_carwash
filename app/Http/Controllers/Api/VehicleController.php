<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\WashOrder;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $vehicles = Vehicle::where('license_plate', 'like', "%{$query}%")
            ->with(['washOrders' => function($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }])
            ->orderBy('license_plate')
            ->limit(10)
            ->get();

        $result = $vehicles->map(function($vehicle) {
            $totalWashes = $vehicle->washOrders()->count();
            $lastWash = $vehicle->washOrders()->orderBy('created_at', 'desc')->first();
            
            return [
                'id' => $vehicle->id,
                'license_plate' => $vehicle->license_plate,
                'type' => $vehicle->type,
                'model' => $vehicle->model,
                'color' => $vehicle->color,
                'total_washes' => $totalWashes,
                'last_wash_date' => $lastWash ? $lastWash->created_at->format('Y-m-d') : null,
                'last_wash_service' => $lastWash ? $lastWash->service->name : null,
                'created_at' => $vehicle->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function getVehicleHistory($id)
    {
        $vehicle = Vehicle::with(['washOrders.service', 'washOrders.staff'])
            ->findOrFail($id);

        $orders = $vehicle->washOrders()
            ->with(['service', 'staff'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalWashes = $orders->count();
        $totalSpent = $orders->where('payment_status', 'paid')->sum('total_price');
        $lastWash = $orders->first();

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle' => [
                    'id' => $vehicle->id,
                    'license_plate' => $vehicle->license_plate,
                    'type' => $vehicle->type,
                    'model' => $vehicle->model,
                    'color' => $vehicle->color,
                    'created_at' => $vehicle->created_at->format('Y-m-d H:i:s')
                ],
                'statistics' => [
                    'total_washes' => $totalWashes,
                    'total_spent' => $totalSpent,
                    'last_wash_date' => $lastWash ? $lastWash->created_at->format('Y-m-d') : null,
                    'last_wash_service' => $lastWash ? $lastWash->service->name : null,
                    'customer_since' => $vehicle->created_at->format('Y-m-d')
                ],
                'recent_orders' => $orders->take(10)->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'service_name' => $order->service->name,
                        'staff_name' => $order->staff->name ?? 'N/A',
                        'total_price' => $order->total_price,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'completed_at' => $order->completed_at ? $order->completed_at->format('Y-m-d H:i:s') : null
                    ];
                })
            ]
        ]);
    }
}