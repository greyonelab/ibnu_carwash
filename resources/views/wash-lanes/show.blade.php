@extends('layouts.app')

@section('title', 'Detail Jalur Cuci - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('wash-lanes.index') }}" 
                class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $washLane->name }}</h1>
                <p class="text-slate-600">Detail jalur cuci dan antrian</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('wash-lanes.edit', $washLane) }}" 
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined">edit</span>
                Edit Jalur
            </a>
        </div>
    </div>

    <!-- Lane Info Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white font-bold text-2xl">{{ substr($washLane->name, -1) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ $washLane->name }}</h2>
                        <p class="text-slate-600 capitalize">{{ $washLane->type }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-2 {{ $washLane->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $washLane->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-slate-50 p-4 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600">queue</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">{{ $washLane->washOrders->count() }}</p>
                            <p class="text-sm text-slate-600">Antrian Saat Ini</p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-600">speed</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">{{ $washLane->max_queue }}</p>
                            <p class="text-sm text-slate-600">Kapasitas Maksimal</p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-600">trending_up</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">{{ $washLane->max_queue > 0 ? round(($washLane->washOrders->count() / $washLane->max_queue) * 100) : 0 }}%</p>
                            <p class="text-sm text-slate-600">Tingkat Okupansi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue Progress -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-700">Status Antrian</span>
                    <span class="text-sm text-slate-600">{{ $washLane->washOrders->count() }}/{{ $washLane->max_queue }}</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $washLane->max_queue > 0 ? ($washLane->washOrders->count() / $washLane->max_queue) * 100 : 0 }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-1">
                    <span>{{ $washLane->washOrders->count() > 0 ? 'Ada antrian' : 'Kosong' }}</span>
                    <span>{{ $washLane->washOrders->count() >= $washLane->max_queue ? 'Penuh' : 'Tersedia' }}</span>
                </div>
            </div>

            @if($washLane->description)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-slate-700 mb-2">Deskripsi</h3>
                <p class="text-slate-600">{{ $washLane->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Current Queue -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Antrian Saat Ini</h3>
                <span class="text-sm text-slate-600">{{ $washLane->washOrders->count() }} kendaraan</span>
            </div>

            @if($washLane->washOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($washLane->washOrders as $order)
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 font-bold">{{ $order->queue_position }}</span>
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-medium text-slate-900">{{ $order->vehicle->license_plate }}</h4>
                                <span class="text-xs px-2 py-1 rounded-full {{ $order->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $order->status === 'in_progress' ? 'Sedang Dicuci' : 'Menunggu' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-slate-600">
                                <span>{{ $order->vehicle->type }} - {{ $order->vehicle->brand }} {{ $order->vehicle->model }}</span>
                                <span>{{ $order->service->name }}</span>
                                @if($order->staff)
                                    <span>Karyawan: {{ $order->staff->name }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-medium text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-500">{{ $order->queued_at ? $order->queued_at->format('H:i') : '-' }}</p>
                        </div>
                        
                        <div class="flex gap-1">
                            <a href="{{ route('orders.show', $order->id) }}" 
                                class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">queue</span>
                    <h4 class="text-lg font-medium text-slate-900 mb-2">Tidak ada antrian</h4>
                    <p class="text-slate-600">Jalur ini sedang kosong</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection