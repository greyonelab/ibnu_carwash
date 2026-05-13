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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">payments</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-sm text-slate-600">Total Pendapatan</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">local_car_wash</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalOrders) }}</h3>
            <p class="text-sm text-slate-600">Total Kendaraan</p>
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Pendapatan Harian</h2>
            </div>
            <div class="h-64 flex items-end justify-between gap-2">
                @php
                    $maxRevenue = collect($revenueChart)->max('revenue');
                @endphp
                @foreach($revenueChart as $data)
                <div class="flex flex-col items-center gap-2 flex-1">
                    <div class="w-full bg-blue-600 rounded-t" style="height: {{ $maxRevenue > 0 ? ($data['revenue'] / $maxRevenue) * 100 : 0 }}%"></div>
                    <span class="text-xs text-slate-500">{{ $data['date'] }}</span>
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
                @endphp
                @foreach($serviceStats as $service)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <span class="text-sm text-slate-700">{{ $service['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: {{ $totalServiceOrders > 0 ? ($service['count'] / $totalServiceOrders) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-slate-900">{{ $totalServiceOrders > 0 ? round(($service['count'] / $totalServiceOrders) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Staff -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Karyawan Terbaik</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topStaff->take(3) as $index => $staff)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-slate-100 text-slate-600' : 'bg-orange-100 text-orange-600') }} flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-600">person</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">{{ $staff['name'] }}</p>
                            <p class="text-sm text-slate-500">{{ $staff['orders'] }} pesanan</p>
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
</div>
@endsection