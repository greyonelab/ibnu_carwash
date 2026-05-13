@extends('layouts.app')

@section('title', 'Karyawan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Karyawan</h1>
            <p class="text-slate-600">Kelola data karyawan dan komisi</p>
        </div>
        <button class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined">add</span>
            Tambah Karyawan
        </button>
    </div>

    <!-- Staff Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($staff as $member)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-600">person</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $member->name }}</h3>
                            <p class="text-sm text-slate-600">{{ $member->position }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    @if($member->phone)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-sm">phone</span>
                        <span class="text-sm text-slate-600">{{ $member->phone }}</span>
                    </div>
                    @endif
                    
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-sm">percent</span>
                        <span class="text-sm text-slate-600">Komisi: {{ $member->commission_rate }}%</span>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="bg-slate-50 rounded-lg p-3 mb-4">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Pesanan Bulan Ini</p>
                            <p class="text-lg font-bold text-slate-900">
                                {{ $member->washOrders()->whereMonth('created_at', now()->month)->count() }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Komisi Bulan Ini</p>
                            <p class="text-lg font-bold text-green-600">
                                Rp {{ number_format($member->washOrders()->whereMonth('created_at', now()->month)->where('status', 'completed')->where('payment_status', 'paid')->get()->sum(function($order) use ($member) { return $order->total_price * ($member->commission_rate / 100); }), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button class="flex-1 bg-slate-100 text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-200 transition-colors text-sm">
                        Edit
                    </button>
                    <button class="bg-blue-100 text-blue-700 py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($staff->isEmpty())
    <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">group</span>
        <h3 class="text-lg font-medium text-slate-900 mb-2">Belum ada karyawan</h3>
        <p class="text-slate-600 mb-4">Tambahkan karyawan pertama untuk memulai</p>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Tambah Karyawan
        </button>
    </div>
    @endif
</div>
@endsection