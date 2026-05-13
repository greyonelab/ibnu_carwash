@extends('layouts.app')

@section('title', 'Detail Pesanan - WashManager Pro')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('orders.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-slate-600">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Detail Pesanan</h1>
                <p class="text-slate-600">{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Vehicle Info -->
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Kendaraan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Plat Nomor:</span>
                        <span class="font-medium">{{ $order->vehicle->license_plate }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Jenis:</span>
                        <span class="font-medium">{{ $order->vehicle->type }}</span>
                    </div>
                    @if($order->vehicle->model)
                    <div class="flex justify-between">
                        <span class="text-slate-600">Model:</span>
                        <span class="font-medium">{{ $order->vehicle->model }}</span>
                    </div>
                    @endif
                    @if($order->vehicle->color)
                    <div class="flex justify-between">
                        <span class="text-slate-600">Warna:</span>
                        <span class="font-medium">{{ $order->vehicle->color }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Service Info -->
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Layanan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Layanan:</span>
                        <span class="font-medium">{{ $order->service->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Petugas:</span>
                        <div class="text-right">
                            @php
                                $allStaff = $order->getAllStaff();
                            @endphp
                            @if($allStaff->count() > 1)
                                @foreach($allStaff as $staff)
                                    <div class="font-medium">{{ $staff->name }}</div>
                                @endforeach
                                <div class="text-xs text-slate-500 mt-1">{{ $allStaff->count() }} staff</div>
                            @else
                                <span class="font-medium">{{ $allStaff->first()?->name ?? 'N/A' }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Durasi:</span>
                        <span class="font-medium">{{ $order->service->duration_minutes }} menit</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Payment -->
        <div class="mt-6 pt-6 border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Status Pesanan</h3>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu',
                            'in_progress' => 'Sedang Proses',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] }}">
                        {{ $statusLabels[$order->status] }}
                    </span>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Status Pembayaran</h3>
                    @php
                        $paymentColors = [
                            'paid' => 'bg-green-100 text-green-800',
                            'unpaid' => 'bg-red-100 text-red-800'
                        ];
                        $paymentLabels = [
                            'paid' => 'Lunas',
                            'unpaid' => 'Belum Bayar'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $paymentColors[$order->payment_status] }}">
                        {{ $paymentLabels[$order->payment_status] }}
                        @if($order->payment_method && $order->payment_status === 'paid')
                            ({{ strtoupper($order->payment_method) }})
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Price Breakdown -->
        <div class="mt-6 pt-6 border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Biaya -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Rincian Biaya</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Harga Layanan:</span>
                            <span class="font-medium">Rp {{ number_format($order->base_price, 0, ',', '.') }}</span>
                        </div>
                        @if($order->additional_fee > 0)
                        <div class="flex justify-between">
                            <span class="text-slate-600">Biaya Tambahan:</span>
                            <span class="font-medium">Rp {{ number_format($order->additional_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="border-t border-slate-200 pt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-slate-900">Total:</span>
                                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Breakdown -->
                @if($order->status === 'completed' && $order->payment_status === 'paid')
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Pembagian Komisi</h3>
                    @php
                        $commissions = $order->calculateCommissions();
                        $allStaff = $order->getAllStaff();
                    @endphp
                    <div class="space-y-2">
                        @if($allStaff->count() > 0)
                            @if($allStaff->count() > 1)
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Komisi per Staff:</span>
                                    <span class="font-medium text-blue-600">Rp {{ number_format($commissions['staff_commission_per_person'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Total Staff ({{ $commissions['staff_count'] }}):</span>
                                    <span class="font-medium text-blue-600">Rp {{ number_format($commissions['total_staff_commission'], 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Komisi Staff:</span>
                                    <span class="font-medium text-blue-600">Rp {{ number_format($commissions['total_staff_commission'], 0, ',', '.') }}</span>
                                </div>
                            @endif
                        @endif
                        <div class="flex justify-between">
                            <span class="text-slate-600">Komisi Owner:</span>
                            <span class="font-medium text-green-600">Rp {{ number_format($commissions['owner_commission'], 0, ',', '.') }}</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-2">
                            Staff: {{ \App\Models\CommissionSetting::getStaffCommission() }}% • Owner: {{ \App\Models\CommissionSetting::getOwnerCommission() }}%
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($order->notes)
        <!-- Notes -->
        <div class="mt-6 pt-6 border-t border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Catatan</h3>
            <p class="text-slate-600">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Timeline -->
        <div class="mt-6 pt-6 border-t border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Timeline</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-slate-600">Dibuat: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($order->started_at)
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                    <span class="text-sm text-slate-600">Mulai: {{ $order->started_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                @if($order->completed_at)
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-slate-600">Selesai: {{ $order->completed_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('orders.receipt', $order->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Print Struk
        </a>
        <a href="{{ route('orders.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition-colors">
            Kembali
        </a>
    </div>
</div>
@endsection