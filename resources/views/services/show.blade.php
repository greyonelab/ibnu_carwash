@extends('layouts.app')

@section('title', 'Detail Layanan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('services.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $service->name }}</h1>
                <p class="text-slate-600">{{ ucfirst($service->category) }} Service</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('services.edit', $service) }}" 
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined">edit</span>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Service Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Informasi Layanan</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        @php
                            $icons = [
                                'standard' => 'water_drop',
                                'premium' => 'auto_awesome',
                                'detail' => 'cleaning_services'
                            ];
                            $colors = [
                                'standard' => 'bg-blue-100 text-blue-600',
                                'premium' => 'bg-purple-100 text-purple-600',
                                'detail' => 'bg-green-100 text-green-600'
                            ];
                        @endphp
                        <div class="w-12 h-12 rounded-full {{ $colors[$service->category] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center">
                            <span class="material-symbols-outlined">{{ $icons[$service->category] ?? 'local_car_wash' }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">{{ $service->name }}</p>
                            <p class="text-sm text-slate-600">{{ ucfirst($service->category) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 text-sm">
                        <span class="material-symbols-outlined text-slate-400">payments</span>
                        <span class="text-slate-600 font-medium">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-3 text-sm">
                        <span class="material-symbols-outlined text-slate-400">schedule</span>
                        <span class="text-slate-600">{{ $service->duration_minutes }} menit</span>
                    </div>
                    
                    <div class="flex items-center gap-3 text-sm">
                        <span class="material-symbols-outlined text-slate-400">
                            {{ $service->is_active ? 'check_circle' : 'cancel' }}
                        </span>
                        <span class="text-slate-600">
                            {{ $service->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    
                    @if($service->description)
                    <div class="pt-4 border-t border-slate-200">
                        <p class="text-sm text-slate-600 font-medium mb-2">Deskripsi:</p>
                        <p class="text-sm text-slate-600">{{ $service->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Pesanan Terbaru</h2>
                
                @if($service->washOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($service->washOrders as $order)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600">local_car_wash</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-slate-600">
                                        {{ $order->vehicle->license_plate }} - {{ $order->staff->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <p class="text-sm font-medium text-slate-900 mt-1">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($service->washOrders->count() >= 10)
                    <div class="mt-4 text-center">
                        <a href="{{ route('orders.index', ['service_id' => $service->id]) }}" 
                            class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Lihat Semua Pesanan
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-slate-400 text-4xl">assignment</span>
                        <p class="text-slate-600 mt-2">Belum ada pesanan untuk layanan ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection