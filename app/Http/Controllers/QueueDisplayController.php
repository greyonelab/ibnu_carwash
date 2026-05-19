<?php

namespace App\Http\Controllers;

use App\Models\WashLane;
use App\Models\WashOrder;
use Illuminate\Http\Request;

class QueueDisplayController extends Controller
{
    public function index()
    {
        $lanes = WashLane::where('is_active', true)
            ->with(['washOrders' => function($query) {
                $query->whereIn('status', ['pending', 'in_progress'])
                      ->with(['vehicle', 'service'])
                      ->orderBy('queue_position');
            }])
            ->orderBy('name')
            ->get();

        return view('queue-display.index', compact('lanes'));
    }

    public function api()
    {
        $lanes = WashLane::where('is_active', true)
            ->with(['washOrders' => function($query) {
                $query->whereIn('status', ['pending', 'in_progress'])
                      ->with(['vehicle', 'service'])
                      ->orderBy('queue_position');
            }])
            ->orderBy('name')
            ->get();

        $data = $lanes->map(function($lane) {
            return [
                'id' => $lane->id,
                'name' => $lane->name,
                'type' => $lane->type,
                'current_queue' => $lane->washOrders->count(),
                'max_queue' => $lane->max_queue,
                'queue' => $lane->washOrders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'license_plate' => $order->vehicle->license_plate,
                        'service_name' => $order->service->name,
                        'status' => $order->status,
                        'queue_position' => $order->queue_position,
                        'estimated_time' => $this->calculateEstimatedTime($order),
                        'queued_at' => $order->queued_at?->format('H:i'),
                        'lane_started_at' => $order->lane_started_at?->format('H:i')
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'last_updated' => now()->format('Y-m-d H:i:s')
        ]);
    }

    private function calculateEstimatedTime($order)
    {
        if ($order->status === 'in_progress') {
            return 'Sedang Dicuci';
        }

        // Hitung estimasi berdasarkan posisi antrian dan durasi layanan
        $position = $order->queue_position;
        $serviceDuration = $order->service->duration_minutes ?? 30;
        
        // Estimasi waktu = (posisi - 1) * durasi layanan
        $estimatedMinutes = ($position - 1) * $serviceDuration;
        
        if ($estimatedMinutes <= 0) {
            return 'Selanjutnya';
        } elseif ($estimatedMinutes < 60) {
            return $estimatedMinutes . ' menit';
        } else {
            $hours = floor($estimatedMinutes / 60);
            $minutes = $estimatedMinutes % 60;
            return $hours . 'j ' . $minutes . 'm';
        }
    }
}