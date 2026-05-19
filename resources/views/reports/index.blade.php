@extends('layouts.app')

@section('title', 'Laporan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Laporan</h1>
            <p class="text-slate-600">Analisis performa dan statistik bisnis</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.export-orders', request()->query()) }}" class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                <span class="material-symbols-outlined">download</span>
                Export Orders
            </a>
            <a href="{{ route('reports.export-reports', request()->query()) }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <span class="material-symbols-outlined">table_view</span>
                Export Reports
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('reports.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition-colors">
                Reset
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">payments</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-sm text-slate-600">Total Pendapatan</p>
            <p class="text-xs text-slate-500 mt-1">Rata-rata: Rp {{ number_format($avgRevenue, 0, ',', '.') }}/hari</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">local_car_wash</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalOrders) }}</h3>
            <p class="text-sm text-slate-600">Pesanan Selesai</p>
            <p class="text-xs text-slate-500 mt-1">Rata-rata: {{ number_format($avgOrdersPerDay, 1) }}/hari</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600">pending</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">{{ number_format($pendingOrders) }}</h3>
            <p class="text-sm text-slate-600">Menunggu</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">autorenew</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">{{ number_format($inProgressOrders) }}</h3>
            <p class="text-sm text-slate-600">Sedang Proses</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600">group</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
            <p class="text-sm text-slate-600">Komisi Staff</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">account_balance_wallet</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalOwnerCommission, 0, ',', '.') }}</h3>
            <p class="text-sm text-slate-600">Komisi Owner</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Pendapatan Harian</h2>
                <span class="text-sm text-slate-500">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="h-64 flex items-end justify-between gap-1">
                @php
                    $maxRevenue = collect($revenueChart)->max('revenue');
                @endphp
                @foreach($revenueChart as $data)
                <div class="flex flex-col items-center gap-2 flex-1">
                    <div class="w-full bg-gradient-to-t from-blue-600 to-blue-400 rounded-t" 
                         style="height: {{ $maxRevenue > 0 ? ($data['revenue'] / $maxRevenue) * 100 : 0 }}%"
                         title="Rp {{ number_format($data['revenue'], 0, ',', '.') }}"></div>
                    <span class="text-xs text-slate-500 transform -rotate-45 origin-center">{{ $data['date'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Pesanan Harian</h2>
                <span class="text-sm text-slate-500">{{ number_format($totalOrders) }} total</span>
            </div>
            <div class="h-64 flex items-end justify-between gap-1">
                @php
                    $maxOrders = collect($ordersChart)->max('orders');
                @endphp
                @foreach($ordersChart as $data)
                <div class="flex flex-col items-center gap-2 flex-1">
                    <div class="w-full bg-gradient-to-t from-green-600 to-green-400 rounded-t" 
                         style="height: {{ $maxOrders > 0 ? ($data['orders'] / $maxOrders) * 100 : 0 }}%"
                         title="{{ $data['orders'] }} pesanan"></div>
                    <span class="text-xs text-slate-500 transform -rotate-45 origin-center">{{ $data['date'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Service Distribution -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-6">Distribusi Layanan</h2>
            <div class="space-y-4">
                @php
                    $totalServiceOrders = $serviceStats->sum('count');
                    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500'];
                @endphp
                @foreach($serviceStats->take(5) as $index => $service)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 {{ $colors[$index % count($colors)] }} rounded"></div>
                        <span class="text-sm text-slate-700">{{ $service['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $colors[$index % count($colors)] }}" style="width: {{ $totalServiceOrders > 0 ? ($service['count'] / $totalServiceOrders) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-slate-900">{{ $totalServiceOrders > 0 ? round(($service['count'] / $totalServiceOrders) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Vehicle & Hourly Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Vehicle Type Distribution -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-6">Jenis Kendaraan</h2>
            <div class="space-y-4">
                @php
                    $totalVehicleRevenue = $vehicleStats->sum('revenue');
                    $vehicleColors = [
                        'Motor' => 'bg-orange-500',
                        'Mobil' => 'bg-blue-500',
                        'default' => 'bg-slate-500'
                    ];
                @endphp
                @foreach($vehicleStats as $vehicle)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 {{ $vehicleColors[$vehicle->type] ?? $vehicleColors['default'] }} bg-opacity-20 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined {{ str_replace('bg-', 'text-', $vehicleColors[$vehicle->type] ?? $vehicleColors['default']) }}">
                                {{ $vehicle->type === 'Motor' ? 'motorcycle' : 'directions_car' }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">{{ $vehicle->type }}</p>
                            <p class="text-sm text-slate-500">{{ $vehicle->count }} kendaraan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-slate-900">{{ $totalVehicleRevenue > 0 ? round(($vehicle->revenue / $totalVehicleRevenue) * 100, 1) : 0 }}%</p>
                        <p class="text-sm text-slate-500">Rp {{ number_format($vehicle->revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Hourly Distribution -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-6">Distribusi Jam Sibuk</h2>
            <div class="h-48 flex items-end justify-between gap-1">
                @php
                    $maxHourlyOrders = $hourlyStats->max('count');
                    $hourlyData = [];
                    for($i = 0; $i < 24; $i++) {
                        $hourlyData[$i] = $hourlyStats->where('hour', $i)->first()->count ?? 0;
                    }
                @endphp
                @for($hour = 0; $hour < 24; $hour++)
                <div class="flex flex-col items-center gap-1 flex-1">
                    <div class="w-full bg-gradient-to-t from-purple-600 to-purple-400 rounded-t" 
                         style="height: {{ $maxHourlyOrders > 0 ? ($hourlyData[$hour] / $maxHourlyOrders) * 100 : 0 }}%"
                         title="{{ $hourlyData[$hour] }} pesanan pada jam {{ $hour }}:00"></div>
                    @if($hour % 3 === 0)
                    <span class="text-xs text-slate-500">{{ $hour }}</span>
                    @endif
                </div>
                @endfor
            </div>
            <div class="flex justify-between text-xs text-slate-500 mt-2">
                <span>00:00</span>
                <span>12:00</span>
                <span>23:00</span>
            </div>
        </div>
    </div>

    <!-- Staff Performance & Payment Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Staff -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Karyawan Terbaik</h2>
                    <a href="{{ route('staff.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topStaff->take(5) as $index => $staff)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-slate-100 text-slate-600' : 'bg-orange-100 text-orange-600') }} flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($staff['name'], 0, 1)) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">{{ $staff['name'] }}</p>
                            <p class="text-sm text-slate-500">{{ $staff['orders'] }} pesanan • {{ $staff['position'] ?? 'Staff' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900">Rp {{ number_format($staff['commission'], 0, ',', '.') }}</p>
                            <p class="text-sm text-slate-500">Komisi</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Metode Pembayaran</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                        $totalPaymentRevenue = $paymentMethods->sum('revenue');
                        $icons = [
                            'cash' => 'payments',
                            'qris' => 'qr_code',
                            'transfer' => 'account_balance'
                        ];
                        $colors = [
                            'cash' => 'bg-green-100 text-green-600',
                            'qris' => 'bg-blue-100 text-blue-600',
                            'transfer' => 'bg-purple-100 text-purple-600'
                        ];
                    @endphp
                    @foreach($paymentMethods as $method)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 {{ $colors[$method->payment_method] ?? 'bg-slate-100 text-slate-600' }} rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined">{{ $icons[$method->payment_method] ?? 'payment' }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ strtoupper($method->payment_method ?? 'Unknown') }}</p>
                                <p class="text-sm text-slate-500">{{ $method->count }} transaksi</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900">{{ $totalPaymentRevenue > 0 ? round(($method->revenue / $totalPaymentRevenue) * 100, 1) : 0 }}%</p>
                            <p class="text-sm text-slate-500">Rp {{ number_format($method->revenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Wash Lane Performance & Recent Orders -->
    @if($laneStats->isNotEmpty())
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Wash Lane Performance -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Performa Jalur Cuci</h2>
                    <a href="{{ route('wash-lanes.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Kelola Jalur
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($laneStats as $lane)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($lane['name'], -1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ $lane['name'] }}</p>
                                <p class="text-sm text-slate-500">{{ ucfirst($lane['type']) }} • {{ $lane['orders'] }} pesanan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900">Rp {{ number_format($lane['revenue'], 0, ',', '.') }}</p>
                            <p class="text-sm text-slate-500">Pendapatan</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Pesanan Terbaru</h2>
                    <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-slate-600">{{ $order->vehicle->type === 'Motor' ? 'motorcycle' : 'directions_car' }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ $order->vehicle->license_plate }}</p>
                                <p class="text-sm text-slate-500">{{ $order->service->name }} • {{ $order->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $order->status === 'completed' ? 'Selesai' : 
                                   ($order->status === 'in_progress' ? 'Proses' : 
                                   ($order->status === 'pending' ? 'Menunggu' : 'Dibatalkan')) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Recent Orders Full Width -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Pesanan Terbaru</h2>
                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Lihat Semua
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kendaraan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-600 text-sm">{{ $order->vehicle->type === 'Motor' ? 'motorcycle' : 'directions_car' }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-slate-900">{{ $order->vehicle->license_plate }}</div>
                                    <div class="text-sm text-slate-500">{{ $order->vehicle->type }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $order->service->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $order->staff->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $order->status === 'completed' ? 'Selesai' : 
                                   ($order->status === 'in_progress' ? 'Proses' : 
                                   ($order->status === 'pending' ? 'Menunggu' : 'Dibatalkan')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $order->created_at->format('H:i') }}</div>
                            <div class="text-sm text-slate-500">{{ $order->created_at->format('d/m/Y') }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Detailed Staff Performance Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Laporan Performa Karyawan Detail</h2>
                <div class="text-sm text-slate-600">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Posisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pesanan Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total Pendapatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Komisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($topStaff as $staff)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($staff['name'], 0, 1)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $staff['name'] }}</div>
                                    <div class="text-sm text-slate-500">{{ $staff['phone'] ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-slate-900">{{ $staff['position'] ?? 'Staff' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-slate-900">{{ $staff['orders'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-slate-900">{{ $staff['completed_orders'] ?? $staff['orders'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-900">Rp {{ number_format($staff['revenue'] ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-green-600">Rp {{ number_format($staff['commission'], 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $staff['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $staff['is_active'] ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection