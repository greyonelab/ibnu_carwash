<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WashOrder;
use App\Models\Staff;
use App\Models\Service;
use App\Exports\OrdersExport;
use App\Exports\ReportsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Summary Statistics
        $totalRevenue = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_price');
            
        $totalOrders = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
            
        $pendingOrders = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('status', 'pending')
            ->count();
            
        $inProgressOrders = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('status', 'in_progress')
            ->count();
            
        $cancelledOrders = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();
            
        $totalCommission = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->get()
            ->sum(function ($order) {
                $commissions = $order->calculateCommissions();
                return $commissions['total_staff_commission'];
            });
            
        $totalOwnerCommission = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->get()
            ->sum(function ($order) {
                $commissions = $order->calculateCommissions();
                return $commissions['owner_commission'];
            });
            
        $avgPerDay = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $avgRevenue = $avgPerDay > 0 ? $totalRevenue / $avgPerDay : 0;
        $avgOrdersPerDay = $avgPerDay > 0 ? $totalOrders / $avgPerDay : 0;
        
        // Chart Data - Revenue per day
        $revenueChart = [];
        $ordersChart = [];
        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);
        
        while ($currentDate <= $endDateCarbon) {
            $dayRevenue = WashOrder::whereDate('wash_orders.created_at', $currentDate)
                ->where('payment_status', 'paid')
                ->sum('total_price');
                
            $dayOrders = WashOrder::whereDate('wash_orders.created_at', $currentDate)
                ->where('status', 'completed')
                ->count();
                
            $revenueChart[] = [
                'date' => $currentDate->format('M d'),
                'revenue' => $dayRevenue
            ];
            
            $ordersChart[] = [
                'date' => $currentDate->format('M d'),
                'orders' => $dayOrders
            ];
            
            $currentDate->addDay();
        }
        
        // Service Distribution
        $serviceStats = Service::with(['washOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('wash_orders.created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
        }])->get()->map(function($service) {
            return [
                'name' => $service->name,
                'count' => $service->washOrders->count(),
                'revenue' => $service->washOrders->sum('total_price')
            ];
        })->sortByDesc('revenue');
        
        // Vehicle Type Distribution
        $vehicleStats = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->join('vehicles', 'wash_orders.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicles.type, COUNT(*) as count, SUM(wash_orders.total_price) as revenue')
            ->groupBy('vehicles.type')
            ->get();
        
        // Top Staff Performance
        $topStaff = Staff::with(['washOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('wash_orders.created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
        }, 'washOrdersAsTeamMember' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('wash_orders.created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
        }])->get()->map(function($staff) use ($startDate, $endDate) {
            $singleOrders = $staff->washOrders;
            $teamOrders = $staff->washOrdersAsTeamMember;
            $allOrders = $singleOrders->merge($teamOrders)->unique('id');
            
            $completedOrders = $allOrders->where('status', 'completed')->count();
            $revenue = $allOrders->sum('total_price');
            
            // Calculate commission from single orders
            $singleOrdersCommission = $singleOrders->sum(function($order) use ($staff) {
                $commissions = $order->calculateCommissions();
                return $commissions['total_staff_commission'] / $order->getAllStaff()->count();
            });
            
            // Calculate commission from team orders
            $teamOrdersCommission = $staff->washOrdersAsTeamMember()
                ->whereBetween('wash_orders.created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->sum('wash_order_staff.commission_amount');
            
            $totalCommission = $singleOrdersCommission + $teamOrdersCommission;
            
            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'position' => $staff->position,
                'phone' => $staff->phone,
                'is_active' => $staff->is_active,
                'orders' => $allOrders->count(),
                'completed_orders' => $completedOrders,
                'revenue' => $revenue,
                'commission' => $totalCommission,
                'commission_rate' => $staff->commission_rate ?? 15
            ];
        })->sortByDesc('commission');
        
        // Payment Method Distribution
        $paymentMethods = WashOrder::whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_price) as revenue')
            ->groupBy('payment_method')
            ->get();
        
        // Hourly Distribution
        $hourlyStats = WashOrder::whereBetween('wash_orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->selectRaw('HOUR(wash_orders.created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Recent Orders
        $recentOrders = WashOrder::with(['vehicle', 'service', 'staff'])
            ->whereBetween('wash_orders.created_at', [$startDate, $endDate])
            ->orderBy('wash_orders.created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Wash Lane Performance (if exists)
        $laneStats = [];
        if (class_exists('\App\Models\WashLane')) {
            $laneStats = \App\Models\WashLane::with(['washOrders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('wash_orders.created_at', [$startDate, $endDate])
                      ->where('payment_status', 'paid');
            }])->get()->map(function($lane) {
                return [
                    'name' => $lane->name,
                    'type' => $lane->type,
                    'orders' => $lane->washOrders->count(),
                    'revenue' => $lane->washOrders->sum('total_price'),
                    'avg_queue_time' => $lane->washOrders->avg('queue_position') ?? 0
                ];
            })->sortByDesc('revenue');
        }
        
        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'inProgressOrders',
            'cancelledOrders',
            'totalCommission',
            'totalOwnerCommission',
            'avgRevenue',
            'avgOrdersPerDay',
            'revenueChart',
            'ordersChart',
            'serviceStats',
            'vehicleStats',
            'topStaff',
            'paymentMethods',
            'hourlyStats',
            'recentOrders',
            'laneStats'
        ));
    }
    
    public function exportOrders(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $filename = 'orders_' . ($startDate ?? 'all') . '_to_' . ($endDate ?? 'now') . '.xlsx';
        
        return Excel::download(new OrdersExport($startDate, $endDate), $filename);
    }
    
    public function exportReports(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $filename = 'reports_' . ($startDate ?? 'all') . '_to_' . ($endDate ?? 'now') . '.xlsx';
        
        return Excel::download(new ReportsExport($startDate, $endDate), $filename);
    }
}
