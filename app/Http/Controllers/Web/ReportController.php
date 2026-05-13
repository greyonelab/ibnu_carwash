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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Summary Statistics
        $orders = WashOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total_price');
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $avgDaily = $totalRevenue / max(1, $startDate->diffInDays($endDate) + 1);
        
        // Revenue Chart Data (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayRevenue = WashOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_price');
            $chartData[] = [
                'date' => $date->format('M d'),
                'revenue' => $dayRevenue
            ];
        }
        
        // Service Distribution
        $serviceStats = Service::withCount(['washOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])->get();
        
        $totalServiceOrders = $serviceStats->sum('wash_orders_count');
        $serviceDistribution = $serviceStats->map(function($service) use ($totalServiceOrders) {
            return [
                'name' => $service->name,
                'count' => $service->wash_orders_count,
                'percentage' => $totalServiceOrders > 0 ? round(($service->wash_orders_count / $totalServiceOrders) * 100, 1) : 0
            ];
        });
        
        // Top Staff Performance
        $topStaff = Staff::with(['washOrders' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed')
                  ->where('payment_status', 'paid');
        }])->get()->map(function($staff) {
            $totalSales = $staff->washOrders->sum('total_price');
            $commission = $totalSales * ($staff->commission_rate / 100);
            return [
                'name' => $staff->name,
                'orders' => $staff->washOrders->count(),
                'sales' => $totalSales,
                'commission' => $commission
            ];
        })->sortByDesc('sales')->take(5);
        
        // Payment Method Distribution
        $paymentMethods = WashOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_price) as total')
            ->groupBy('payment_method')
            ->get();
        
        return view('reports.index', compact(
            'startDate', 'endDate', 'totalRevenue', 'totalOrders', 
            'completedOrders', 'avgDaily', 'chartData', 'serviceDistribution',
            'topStaff', 'paymentMethods'
        ));
    }
    
    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        $filename = 'laporan-carwash-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new ReportsExport($startDate, $endDate), $filename);
    }
    
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        $orders = WashOrder::with(['vehicle', 'service', 'staff'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total_price');
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        
        $pdf = Pdf::loadView('reports.pdf', compact('orders', 'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'completedOrders'));
        
        $filename = 'laporan-carwash-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function receipt($id)
    {
        $order = WashOrder::with(['vehicle', 'service', 'staff', 'user'])->findOrFail($id);
        
        $pdf = Pdf::loadView('orders.receipt', compact('order'))
            ->setPaper([0, 0, 226.77, 566.93], 'portrait'); // 80mm x 200mm thermal paper
        
        $filename = 'struk-' . $order->order_number . '.pdf';
        
        return $pdf->download($filename);
    }
}
