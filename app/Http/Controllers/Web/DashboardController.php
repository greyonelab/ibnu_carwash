<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WashOrder;
use App\Models\WashLane;
use App\Models\Staff;
use App\Models\Service;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        // ── Statistik Hari Ini ──────────────────────────────────────────
        $todayRevenue = WashOrder::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $yesterdayRevenue = WashOrder::whereDate('created_at', $today->copy()->subDay())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $revenueChange = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : ($todayRevenue > 0 ? 100 : 0);

        $todayOrders = WashOrder::whereDate('created_at', $today)->count();
        $yesterdayOrders = WashOrder::whereDate('created_at', $today->copy()->subDay())->count();
        $ordersChange = $yesterdayOrders > 0
            ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100
            : ($todayOrders > 0 ? 100 : 0);

        $carsServed = WashOrder::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $carsInQueue = WashOrder::whereIn('status', ['pending', 'in_progress'])->count();
        $carsInProgress = WashOrder::where('status', 'in_progress')->count();
        $carsPending = WashOrder::where('status', 'pending')->count();

        // ── Komisi Hari Ini ─────────────────────────────────────────────
        $staffCommissionRate = CommissionSetting::getStaffCommission();
        $todayCommission = WashOrder::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total_price') * ($staffCommissionRate / 100);

        // ── Statistik Minggu & Bulan Ini ────────────────────────────────
        $weekRevenue = WashOrder::where('created_at', '>=', $thisWeekStart)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $monthRevenue = WashOrder::where('created_at', '>=', $thisMonthStart)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $monthOrders = WashOrder::where('created_at', '>=', $thisMonthStart)->count();

        // ── Rata-rata nilai order hari ini ──────────────────────────────
        $avgOrderValue = $todayOrders > 0
            ? WashOrder::whereDate('created_at', $today)->avg('total_price')
            : 0;

        // ── Chart 7 Hari Terakhir ───────────────────────────────────────
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('D, d M');
            $chartRevenue[] = (float) WashOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_price');
            $chartOrders[] = WashOrder::whereDate('created_at', $date)->count();
        }

        // ── Distribusi Layanan Bulan Ini ────────────────────────────────
        $serviceStats = WashOrder::where('created_at', '>=', $thisMonthStart)
            ->where('status', 'completed')
            ->with('service')
            ->get()
            ->groupBy('service_id')
            ->map(function ($orders) {
                return [
                    'name' => $orders->first()->service->name ?? 'Unknown',
                    'count' => $orders->count(),
                    'revenue' => $orders->sum('total_price'),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->take(5);

        // ── Top Staff Bulan Ini ─────────────────────────────────────────
        $topStaff = Staff::where('is_active', true)
            ->withCount(['washOrdersAsTeamMember as orders_count' => function ($q) use ($thisMonthStart) {
                $q->where('wash_orders.created_at', '>=', $thisMonthStart)
                  ->where('wash_orders.status', 'completed');
            }])
            ->withSum(['washOrdersAsTeamMember as total_commission' => function ($q) use ($thisMonthStart) {
                $q->where('wash_orders.created_at', '>=', $thisMonthStart)
                  ->where('wash_orders.status', 'completed')
                  ->where('wash_orders.payment_status', 'paid');
            }], 'wash_order_staff.commission_amount')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        // ── Status Jalur Cuci Real ──────────────────────────────────────
        $washLanes = WashLane::where('is_active', true)
            ->with(['washOrders' => function ($q) {
                $q->whereIn('status', ['pending', 'in_progress'])
                  ->with(['vehicle', 'service'])
                  ->orderBy('queue_position');
            }])
            ->withCount(['washOrders as active_queue' => function ($q) {
                $q->whereIn('status', ['pending', 'in_progress']);
            }])
            ->orderBy('name')
            ->get();

        // ── Aktivitas Terkini ───────────────────────────────────────────
        $recentActivities = WashOrder::with(['vehicle', 'service', 'staffMembers'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // ── Pesanan Pending Terlama ─────────────────────────────────────
        $pendingOrders = WashOrder::with(['vehicle', 'service', 'washLane'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // ── Metode Pembayaran Hari Ini ──────────────────────────────────
        $paymentMethods = WashOrder::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_price) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        // ── Data untuk Quick Order (staff & services) ───────────────────
        $activeStaff = Staff::where('is_active', true)->get();
        $activeServices = Service::where('is_active', true)->get();

        return view('dashboard.index', compact(
            'todayRevenue', 'yesterdayRevenue', 'revenueChange',
            'todayOrders', 'ordersChange',
            'carsServed', 'carsInQueue', 'carsInProgress', 'carsPending',
            'todayCommission', 'staffCommissionRate',
            'weekRevenue', 'monthRevenue', 'monthOrders',
            'avgOrderValue',
            'chartLabels', 'chartRevenue', 'chartOrders',
            'serviceStats',
            'topStaff',
            'washLanes',
            'recentActivities',
            'pendingOrders',
            'paymentMethods',
            'activeStaff', 'activeServices'
        ));
    }
}
