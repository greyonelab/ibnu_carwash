<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WashOrder;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        // Total penjualan hari ini
        $todayRevenue = WashOrder::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_price');
            
        // Total penjualan kemarin untuk perbandingan
        $yesterdayRevenue = WashOrder::whereDate('created_at', $today->copy()->subDay())
            ->where('payment_status', 'paid')
            ->sum('total_price');
            
        // Persentase perubahan
        $revenueChange = $yesterdayRevenue > 0 
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 
            : 0;
        
        // Mobil terlayani hari ini
        $carsServed = WashOrder::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();
            
        // Mobil dalam antrian
        $carsInQueue = WashOrder::whereIn('status', ['pending', 'in_progress'])
            ->count();
        
        // Total komisi karyawan hari ini
        $todayCommission = WashOrder::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->with('staff')
            ->get()
            ->sum(function ($order) {
                return $order->total_price * ($order->staff->commission_rate / 100);
            });
        
        // Aktivitas terkini (5 terakhir)
        $recentActivities = WashOrder::with(['vehicle', 'service', 'staff'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Status lajur (simulasi 3 lajur)
        $bayStatus = [
            [
                'id' => 1,
                'name' => 'Lajur 01',
                'status' => 'occupied',
                'remaining_minutes' => 12,
                'current_order' => WashOrder::where('status', 'in_progress')->first()
            ],
            [
                'id' => 2,
                'name' => 'Lajur 02',
                'status' => 'available',
                'remaining_minutes' => 0,
                'current_order' => null
            ],
            [
                'id' => 3,
                'name' => 'Lajur 03',
                'status' => 'available',
                'remaining_minutes' => 0,
                'current_order' => null
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'revenue' => [
                    'today' => $todayRevenue,
                    'yesterday' => $yesterdayRevenue,
                    'change_percentage' => round($revenueChange, 1),
                    'cash_indicator' => 420.00 // Simulasi kas
                ],
                'cars' => [
                    'served_today' => $carsServed,
                    'in_queue' => $carsInQueue
                ],
                'commission' => [
                    'today_total' => $todayCommission,
                    'target_percentage' => 65 // Simulasi persentase target
                ],
                'recent_activities' => $recentActivities,
                'bay_status' => $bayStatus,
                'shift_progress' => [
                    'current_hours' => 6,
                    'total_hours' => 8,
                    'percentage' => 75
                ]
            ]
        ]);
    }
}
