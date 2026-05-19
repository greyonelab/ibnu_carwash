<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WashOrder;
use App\Models\Vehicle;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([]);
        }
        
        $results = [];
        
        // Search Orders
        $orders = WashOrder::with(['vehicle', 'service', 'staff'])
            ->where('order_number', 'like', "%{$query}%")
            ->orWhereHas('vehicle', function($q) use ($query) {
                $q->where('license_plate', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();
            
        foreach ($orders as $order) {
            $results[] = [
                'type' => 'order',
                'title' => $order->order_number,
                'subtitle' => $order->vehicle->license_plate . ' - ' . $order->service->name,
                'url' => route('orders.show', $order->id),
                'icon' => 'receipt_long'
            ];
        }
        
        // Search Vehicles
        $vehicles = Vehicle::where('license_plate', 'like', "%{$query}%")
            ->orWhere('model', 'like', "%{$query}%")
            ->limit(3)
            ->get();
            
        foreach ($vehicles as $vehicle) {
            $results[] = [
                'type' => 'vehicle',
                'title' => $vehicle->license_plate,
                'subtitle' => $vehicle->type . ' ' . $vehicle->model,
                'url' => '#',
                'icon' => 'directions_car'
            ];
        }
        
        // Search Staff
        $staff = Staff::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get();
            
        foreach ($staff as $member) {
            $results[] = [
                'type' => 'staff',
                'title' => $member->name,
                'subtitle' => $member->position,
                'url' => '#',
                'icon' => 'person'
            ];
        }
        
        return response()->json($results);
    }
}
