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
        $totalRevenue = WashOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_price');
            
        $totalOrders = WashOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
            
        $totalCommission = WashOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->get()
            ->sum(function ($order) {
                $commissions = $order->calculateCommissions();
                return $commissions['total_staff_commission'];
            });
            
        $totalOwnerCommission = WashOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->get()
            ->sum(function ($order) {
                $commissions = $order->calculateCommissions();
                return $commissions['owner_commission'];
            });
            
        $avgPerDay = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $avgRevenue = $avgPerDay > 0 ? $totalRevenue / $avgPerDay : 0;
        
        // Chart Data - Revenue per day
        $revenueChart = [];
        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);
        
        while ($currentDate <= $endDateCarbon) {
            $dayRevenue = WashOrder::whereDate('created_at', $currentDate)
                ->where('payment_status', 'paid')
                ->sum('total_price');
                
            $revenueChart[] = [
                'date' => $currentDate->format('M d'),
                'revenue' => $dayRevenue
            ];
            
            $currentDate->addDay();
        }
        
        // Service Distribution
        $serviceStats = Service::with(['washOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
        }])->get()->map(function($service) {
            return [
                'name' => $service->name,
                'count' => $service->washOrders->count(),
                'revenue' => $service->washOrders->sum('total_price')
            ];
        });
        
        // Top Staff Performance
        $topStaff = Staff::with(['washOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
        }, 'washOrdersAsTeamMember' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
        }])->get()->map(function($staff) {
            $singleOrders = $staff->washOrders;
            $teamOrders = $staff->washOrdersAsTeamMember;
            $allOrders = $singleOrders->merge($teamOrders)->unique('id');
            
            $revenue = $allOrders->sum('total_price');
            
            // Calculate commission from single orders
            $singleOrdersCommission = $singleOrders->sum(function($order) use ($staff) {
                return $order->total_price * ($staff->commission_rate / 100);
            });
            
            // Calculate commission from team orders
            $teamOrdersCommission = $staff->washOrdersAsTeamMember()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->sum('wash_order_staff.commission_amount');
            
            $totalCommission = $singleOrdersCommission + $teamOrdersCommission;
            
            return [
                'name' => $staff->name,
                'orders' => $allOrders->count(),
                'revenue' => $revenue,
                'commission' => $totalCommission
            ];
        })->sortByDesc('commission')->take(5);
        
        // Payment Method Distribution
        $paymentMethods = WashOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_price) as revenue')
            ->groupBy('payment_method')
            ->get();
        
        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'totalCommission',
            'totalOwnerCommission',
            'avgRevenue',
            'revenueChart',
            'serviceStats',
            'topStaff',
            'paymentMethods'
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
