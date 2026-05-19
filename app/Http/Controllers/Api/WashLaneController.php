<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WashLane;
use Illuminate\Http\Request;

class WashLaneController extends Controller
{
    public function index()
    {
        $lanes = WashLane::where('is_active', true)
            ->withCount(['washOrders as current_queue' => function($query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lanes->map(function($lane) {
                return [
                    'id' => $lane->id,
                    'name' => $lane->name,
                    'type' => $lane->type,
                    'max_queue' => $lane->max_queue,
                    'current_queue' => $lane->current_queue,
                    'is_available' => $lane->current_queue < $lane->max_queue,
                    'description' => $lane->description
                ];
            })
        ]);
    }

    public function show($id)
    {
        $lane = WashLane::with(['washOrders' => function($query) {
            $query->whereIn('status', ['pending', 'in_progress'])
                  ->with(['vehicle', 'service'])
                  ->orderBy('queue_position');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lane->id,
                'name' => $lane->name,
                'type' => $lane->type,
                'max_queue' => $lane->max_queue,
                'current_queue' => $lane->washOrders->count(),
                'description' => $lane->description,
                'queue' => $lane->washOrders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'license_plate' => $order->vehicle->license_plate,
                        'service_name' => $order->service->name,
                        'status' => $order->status,
                        'queue_position' => $order->queue_position,
                        'queued_at' => $order->queued_at?->format('Y-m-d H:i:s')
                    ];
                })
            ]
        ]);
    }

    public function getAvailable(Request $request)
    {
        $vehicleType = $request->get('vehicle_type');
        
        $availableLane = WashLane::getAvailableLane($vehicleType);
        
        if (!$availableLane) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jalur yang tersedia saat ini'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $availableLane->id,
                'name' => $availableLane->name,
                'type' => $availableLane->type,
                'current_queue' => $availableLane->queue_count,
                'max_queue' => $availableLane->max_queue,
                'next_position' => $availableLane->next_queue_position
            ]
        ]);
    }
}