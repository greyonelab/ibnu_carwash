@extends('layouts.app')

@section('title', 'Dashboard - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">RINGKASAN HARI INI</p>
            <h2 class="text-2xl font-bold text-slate-900">Selamat {{ date('H') < 12 ? 'Pagi' : (date('H') < 18 ? 'Siang' : 'Malam') }}, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-base">add</span> Pesanan Baru
            </a>
            <button onclick="openQuickOrderModal()" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-base">flash_on</span> Cuci Express
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

        {{-- Revenue Card --}}
        <div class="col-span-2 bg-gradient-to-br from-blue-600 to-blue-700 p-5 rounded-xl text-white">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-wider">Pendapatan Hari Ini</p>
                <div class="bg-white/20 p-1.5 rounded-lg"><span class="material-symbols-outlined text-lg">payments</span></div>
            </div>
            <p class="text-3xl font-bold mb-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            <div class="flex items-center gap-2">
                @if($revenueChange >= 0)
                <span class="text-xs font-bold bg-white/20 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">trending_up</span>+{{ number_format($revenueChange, 1) }}%
                </span>
                @else
                <span class="text-xs font-bold bg-white/20 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">trending_down</span>{{ number_format($revenueChange, 1) }}%
                </span>
                @endif
                <span class="text-xs text-blue-100">vs kemarin</span>
            </div>
        </div>

        {{-- Orders Today --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pesanan Hari Ini</p>
                <div class="bg-indigo-100 p-1.5 rounded-lg"><span class="material-symbols-outlined text-indigo-600 text-lg">receipt_long</span></div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-2">{{ $todayOrders }}</p>
            <div class="flex items-center gap-1">
                @if($ordersChange >= 0)
                <span class="text-xs font-bold text-green-600">+{{ number_format($ordersChange, 1) }}%</span>
                @else
                <span class="text-xs font-bold text-red-600">{{ number_format($ordersChange, 1) }}%</span>
                @endif
                <span class="text-xs text-slate-400 ml-1">vs kemarin</span>
            </div>
        </div>

        {{-- Cars Served --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Hari Ini</p>
                <div class="bg-green-100 p-1.5 rounded-lg"><span class="material-symbols-outlined text-green-600 text-lg">check_circle</span></div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-2">{{ $carsServed }}</p>
            <span class="text-xs text-slate-400">kendaraan selesai</span>
        </div>

        {{-- Queue --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Antrian Aktif</p>
                <div class="bg-yellow-100 p-1.5 rounded-lg"><span class="material-symbols-outlined text-yellow-600 text-lg">queue</span></div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-2">{{ $carsInQueue }}</p>
            <div class="flex gap-2 text-xs">
                <span class="text-blue-600 font-medium">{{ $carsInProgress }} proses</span>
                <span class="text-slate-300">|</span>
                <span class="text-yellow-600 font-medium">{{ $carsPending }} tunggu</span>
            </div>
        </div>

        {{-- Commission --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Komisi Staff</p>
                <div class="bg-purple-100 p-1.5 rounded-lg"><span class="material-symbols-outlined text-purple-600 text-lg">group</span></div>
            </div>
            <p class="text-2xl font-bold text-slate-900 mb-2">Rp {{ number_format($todayCommission, 0, ',', '.') }}</p>
            <span class="text-xs text-slate-400">{{ $staffCommissionRate }}% dari pendapatan</span>
        </div>

        {{-- Month Revenue --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan Ini</p>
                <div class="bg-orange-100 p-1.5 rounded-lg"><span class="material-symbols-outlined text-orange-600 text-lg">calendar_month</span></div>
            </div>
            <p class="text-2xl font-bold text-slate-900 mb-2">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
            <span class="text-xs text-slate-400">{{ $monthOrders }} pesanan</span>
        </div>

    </div>

    {{-- Chart & Wash Lanes --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Pendapatan 7 Hari Terakhir</h3>
                    <p class="text-sm text-slate-500">Minggu ini: Rp {{ number_format($weekRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="switchChart('revenue')" id="btn-revenue" class="chart-btn text-xs px-3 py-1.5 rounded-lg bg-blue-600 text-white font-medium">Pendapatan</button>
                    <button onclick="switchChart('orders')" id="btn-orders" class="chart-btn text-xs px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-medium">Pesanan</button>
                </div>
            </div>
            <div class="relative h-56">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Wash Lanes --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Status Jalur Cuci</h3>
                <a href="{{ route('wash-lanes.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Kelola</a>
            </div>
            @if($washLanes->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <span class="material-symbols-outlined text-4xl block mb-2">route</span>
                <p class="text-sm">Belum ada jalur cuci</p>
                <a href="{{ route('wash-lanes.create') }}" class="text-xs text-blue-600 mt-2 inline-block">+ Tambah Jalur</a>
            </div>
            @else
            <div class="space-y-3">
                @foreach($washLanes as $lane)
                @php
                    $pct = $lane->max_queue > 0 ? min(100, round(($lane->active_queue / $lane->max_queue) * 100)) : 0;
                    $isFull = $lane->active_queue >= $lane->max_queue;
                    $barColor = $pct >= 100 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                    $cardClass = $isFull ? 'border-red-200 bg-red-50' : ($lane->active_queue > 0 ? 'border-yellow-200 bg-yellow-50' : 'border-green-200 bg-green-50');
                    $dotClass = $isFull ? 'bg-red-500' : ($lane->active_queue > 0 ? 'bg-yellow-500 animate-pulse' : 'bg-green-500');
                    $statusText = $isFull ? 'PENUH' : ($lane->active_queue > 0 ? 'AKTIF' : 'KOSONG');
                    $statusColor = $isFull ? 'text-red-600' : ($lane->active_queue > 0 ? 'text-yellow-700' : 'text-green-700');
                @endphp
                <div class="p-3 rounded-lg border {{ $cardClass }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $dotClass }}"></div>
                            <span class="font-semibold text-sm text-slate-900">{{ $lane->name }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded bg-slate-200 text-slate-600">{{ strtoupper($lane->type) }}</span>
                        </div>
                        <span class="text-xs font-bold {{ $statusColor }}">{{ $statusText }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-white/70 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $barColor }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">{{ $lane->active_queue }}/{{ $lane->max_queue }}</span>
                    </div>
                    @if($lane->washOrders->isNotEmpty())
                    <div class="mt-2 space-y-1">
                        @foreach($lane->washOrders->take(2) as $qOrder)
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span class="font-mono font-medium">{{ $qOrder->vehicle->license_plate ?? '-' }}</span>
                            <span class="{{ $qOrder->status === 'in_progress' ? 'text-blue-600' : 'text-yellow-600' }}">
                                {{ $qOrder->status === 'in_progress' ? 'Proses' : 'Tunggu' }}
                            </span>
                        </div>
                        @endforeach
                        @if($lane->washOrders->count() > 2)
                        <p class="text-xs text-slate-400">+{{ $lane->washOrders->count() - 2 }} lainnya</p>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Recent Activity & Pending Orders --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Recent Activity --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Aktivitas Terkini</h3>
                <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kendaraan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Layanan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Bayar</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentActivities as $activity)
                        @php
                            $icon = ($activity->vehicle->type ?? '') === 'Motor' ? 'two_wheeler' : 'directions_car';
                            $sc = ['pending' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800'];
                            $sl = ['pending' => 'Menunggu', 'in_progress' => 'Proses', 'completed' => 'Selesai', 'cancelled' => 'Batal'];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-slate-500 text-lg">{{ $icon }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 font-mono">{{ $activity->vehicle->license_plate }}</p>
                                        <p class="text-xs text-slate-400">{{ $activity->vehicle->model ?? $activity->vehicle->type }} &bull; {{ $activity->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-700">{{ $activity->service->name }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc[$activity->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $sl[$activity->status] ?? $activity->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @if($activity->payment_status === 'paid')
                                <span class="inline-flex items-center gap-1 text-xs text-green-700 font-medium">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>{{ ucfirst($activity->payment_method ?? 'paid') }}
                                </span>
                                @else
                                <span class="text-xs text-slate-400">Belum bayar</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm font-bold text-slate-900 text-right">Rp {{ number_format($activity->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2">inbox</span>
                                <p class="text-sm">Belum ada aktivitas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Active Queue --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Antrian Aktif</h3>
                <span class="text-xs bg-yellow-100 text-yellow-800 font-bold px-2 py-0.5 rounded-full">{{ $carsInQueue }}</span>
            </div>
            @if($pendingOrders->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <span class="material-symbols-outlined text-4xl block mb-2">done_all</span>
                <p class="text-sm">Tidak ada antrian</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($pendingOrders as $po)
                @php
                    $poCardClass = $po->status === 'in_progress' ? 'bg-blue-50 border-blue-200' : 'bg-yellow-50 border-yellow-200';
                    $poIconBg = $po->status === 'in_progress' ? 'bg-blue-600' : 'bg-yellow-500';
                    $poIcon = $po->status === 'in_progress' ? 'autorenew' : 'schedule';
                    $poLabel = $po->status === 'in_progress' ? 'Proses' : 'Tunggu';
                    $poLabelColor = $po->status === 'in_progress' ? 'text-blue-700' : 'text-yellow-700';
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-lg border {{ $poCardClass }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $poIconBg }}">
                        <span class="material-symbols-outlined text-white text-sm">{{ $poIcon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 font-mono">{{ $po->vehicle->license_plate ?? '-' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $po->service->name ?? '-' }}{{ $po->washLane ? ' &bull; '.$po->washLane->name : '' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold {{ $poLabelColor }}">{{ $poLabel }}</p>
                        <p class="text-xs text-slate-400">{{ $po->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @if($carsInQueue > 5)
            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="mt-3 block text-center text-xs text-blue-600 hover:text-blue-700 font-medium">
                Lihat {{ $carsInQueue - 5 }} antrian lainnya
            </a>
            @endif
            @endif
        </div>
    </div>

    {{-- Bottom Row: Top Staff, Service Stats, Payment Methods --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Top Staff --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-900">Top Staff Bulan Ini</h3>
                <a href="{{ route('staff.index') }}" class="text-xs text-blue-600 font-medium">Semua</a>
            </div>
            @if($topStaff->isEmpty())
            <p class="text-sm text-slate-400 text-center py-6">Belum ada data</p>
            @else
            <div class="space-y-3">
                @foreach($topStaff as $i => $member)
                @php
                    $rankClass = $i === 0 ? 'bg-yellow-400 text-yellow-900' : ($i === 1 ? 'bg-slate-300 text-slate-700' : ($i === 2 ? 'bg-orange-300 text-orange-900' : 'bg-slate-100 text-slate-500'));
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $rankClass }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $member->name }}</p>
                        <p class="text-xs text-slate-400">{{ $member->orders_count }} pesanan</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-green-600">Rp {{ number_format($member->total_commission ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Service Stats --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-900">Layanan Populer</h3>
                <span class="text-xs text-slate-400">Bulan ini</span>
            </div>
            @if($serviceStats->isEmpty())
            <p class="text-sm text-slate-400 text-center py-6">Belum ada data</p>
            @else
            @php $maxCount = $serviceStats->max('count'); @endphp
            <div class="space-y-3">
                @foreach($serviceStats as $svc)
                @php $svPct = $maxCount > 0 ? round(($svc['count'] / $maxCount) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-700 truncate">{{ $svc['name'] }}</span>
                        <span class="text-slate-500 font-semibold ml-2">{{ $svc['count'] }}x</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $svPct }}%"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Rp {{ number_format($svc['revenue'], 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Payment Methods --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-900">Metode Pembayaran</h3>
                <span class="text-xs text-slate-400">Hari ini</span>
            </div>
            @php
                $pmIcons = ['cash' => 'payments', 'qris' => 'qr_code', 'transfer' => 'account_balance'];
                $pmColors = ['cash' => 'text-green-600 bg-green-100', 'qris' => 'text-blue-600 bg-blue-100', 'transfer' => 'text-purple-600 bg-purple-100'];
                $pmLabels = ['cash' => 'Cash', 'qris' => 'QRIS', 'transfer' => 'Transfer'];
                $pmBarColors = ['cash' => 'bg-green-500', 'qris' => 'bg-blue-500', 'transfer' => 'bg-purple-500'];
                $totalPaid = $paymentMethods->sum('count');
            @endphp
            <div class="space-y-3">
                @foreach(['cash','qris','transfer'] as $method)
                @php
                    $pm = $paymentMethods->get($method);
                    $pmPct = $totalPaid > 0 && $pm ? round(($pm->count / $totalPaid) * 100) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $pmColors[$method] }}">
                        <span class="material-symbols-outlined text-lg">{{ $pmIcons[$method] }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-slate-700">{{ $pmLabels[$method] }}</span>
                            <span class="font-bold text-slate-900">{{ $pm ? $pm->count : 0 }}x</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $pmBarColors[$method] }}" style="width: {{ $pmPct }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400 w-8 text-right">{{ $pmPct }}%</span>
                </div>
                @endforeach
                @if($totalPaid > 0)
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Total terbayar</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($paymentMethods->sum('total'), 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Order Modal --}}
    <div id="quickOrderModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600">flash_on</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Cuci Express</h3>
                        <p class="text-xs text-slate-500">Pesanan langsung selesai & bayar</p>
                    </div>
                </div>
                <button onclick="closeQuickOrderModal()" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1.5 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="auto_complete" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Plat Nomor *</label>
                        <input type="text" name="license_plate" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono uppercase tracking-widest"
                            placeholder="B 1234 ABC">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Kendaraan *</label>
                        <select name="vehicle_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Pilih...</option>
                            <option value="Motor">Motor</option>
                            <option value="Sedan">Sedan</option>
                            <option value="Hatchback">Hatchback</option>
                            <option value="SUV">SUV</option>
                            <option value="MPV">MPV</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Layanan *</label>
                    <div class="space-y-2">
                        @foreach($activeServices as $svc)
                        <label class="qo-service-label flex items-center gap-3 p-3 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-green-300 transition-colors">
                            <input type="radio" name="service_id" value="{{ $svc->id }}" class="sr-only qo-service-radio" required>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900 text-sm">{{ $svc->name }}</p>
                                <p class="text-xs text-slate-500">{{ $svc->duration_minutes }} mnt</p>
                            </div>
                            <span class="font-bold text-green-600 text-sm">Rp {{ number_format($svc->price, 0, ',', '.') }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Staff *</label>
                    <div class="max-h-32 overflow-y-auto border border-slate-200 rounded-lg p-2 space-y-1">
                        @foreach($activeStaff as $member)
                        <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                            <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}" class="h-4 w-4 text-green-600 rounded">
                            <span class="text-sm text-slate-700">{{ $member->name }}</span>
                            <span class="text-xs text-slate-400 ml-auto">{{ $member->position }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Metode Pembayaran *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="qo-pay-label flex flex-col items-center p-3 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-green-300 transition-colors">
                            <input type="radio" name="payment_method" value="cash" class="sr-only qo-pay-radio" required>
                            <span class="material-symbols-outlined text-green-600 mb-1">payments</span>
                            <span class="text-xs font-medium">Cash</span>
                        </label>
                        <label class="qo-pay-label flex flex-col items-center p-3 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                            <input type="radio" name="payment_method" value="qris" class="sr-only qo-pay-radio" required>
                            <span class="material-symbols-outlined text-blue-600 mb-1">qr_code</span>
                            <span class="text-xs font-medium">QRIS</span>
                        </label>
                        <label class="qo-pay-label flex flex-col items-center p-3 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                            <input type="radio" name="payment_method" value="transfer" class="sr-only qo-pay-radio" required>
                            <span class="material-symbols-outlined text-purple-600 mb-1">account_balance</span>
                            <span class="text-xs font-medium">Transfer</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeQuickOrderModal()" class="flex-1 bg-slate-100 text-slate-700 py-2.5 px-4 rounded-lg hover:bg-slate-200 transition-colors font-medium">Batal</button>
                    <button type="submit" class="flex-[2] bg-green-600 text-white py-2.5 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">flash_on</span> Buat & Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartLabels = @json($chartLabels);
const chartRevenue = @json($chartRevenue);
const chartOrders = @json($chartOrders);
let revenueChart;
let currentMode = 'revenue';

function initChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pendapatan',
                data: chartRevenue,
                backgroundColor: 'rgba(59,130,246,0.15)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (currentMode === 'revenue') {
                                return 'Rp ' + ctx.raw.toLocaleString('id-ID');
                            }
                            return ctx.raw + ' pesanan';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(v) {
                            if (currentMode === 'revenue') {
                                return v >= 1000000 ? 'Rp ' + (v/1000000).toFixed(1) + 'jt' : 'Rp ' + (v/1000).toFixed(0) + 'rb';
                            }
                            return v;
                        },
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

function switchChart(mode) {
    currentMode = mode;
    const data = mode === 'revenue' ? chartRevenue : chartOrders;
    const label = mode === 'revenue' ? 'Pendapatan' : 'Pesanan';
    const color = mode === 'revenue' ? 'rgba(59,130,246,' : 'rgba(16,185,129,';
    revenueChart.data.datasets[0].data = data;
    revenueChart.data.datasets[0].label = label;
    revenueChart.data.datasets[0].backgroundColor = color + '0.15)';
    revenueChart.data.datasets[0].borderColor = color + '1)';
    revenueChart.update();
    document.querySelectorAll('.chart-btn').forEach(b => {
        b.classList.remove('bg-blue-600', 'text-white', 'bg-emerald-600');
        b.classList.add('bg-slate-100', 'text-slate-600');
    });
    const activeBtn = document.getElementById('btn-' + mode);
    activeBtn.classList.remove('bg-slate-100', 'text-slate-600');
    activeBtn.classList.add(mode === 'revenue' ? 'bg-blue-600' : 'bg-emerald-600', 'text-white');
}

function openQuickOrderModal() {
    document.getElementById('quickOrderModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeQuickOrderModal() {
    document.getElementById('quickOrderModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    initChart();

    document.querySelectorAll('.qo-service-radio').forEach(r => {
        r.addEventListener('change', function() {
            document.querySelectorAll('.qo-service-label').forEach(l => {
                l.classList.remove('border-green-500', 'bg-green-50');
                l.classList.add('border-slate-200');
            });
            this.closest('.qo-service-label').classList.remove('border-slate-200');
            this.closest('.qo-service-label').classList.add('border-green-500', 'bg-green-50');
        });
    });

    document.querySelectorAll('.qo-pay-radio').forEach(r => {
        r.addEventListener('change', function() {
            document.querySelectorAll('.qo-pay-label').forEach(l => {
                l.classList.remove('border-green-500','bg-green-50','border-blue-500','bg-blue-50','border-purple-500','bg-purple-50');
                l.classList.add('border-slate-200');
            });
            const label = this.closest('.qo-pay-label');
            label.classList.remove('border-slate-200');
            if (this.value === 'cash') label.classList.add('border-green-500', 'bg-green-50');
            else if (this.value === 'qris') label.classList.add('border-blue-500', 'bg-blue-50');
            else label.classList.add('border-purple-500', 'bg-purple-50');
        });
    });

    document.getElementById('quickOrderModal').addEventListener('click', function(e) {
        if (e.target === this) closeQuickOrderModal();
    });
});
</script>
@endpush
