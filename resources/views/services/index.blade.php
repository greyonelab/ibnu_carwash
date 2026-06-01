@extends('layouts.app')

@section('title', 'Layanan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Layanan Cuci</h1>
            <p class="text-slate-600">Kelola jenis layanan cuci yang tersedia</p>
        </div>
        <a href="{{ route('services.create') }}" 
            class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined">add</span>
            Tambah Layanan
        </a>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($services as $service)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
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
                        <div class="w-12 h-12 rounded-lg {{ $colors[$service->type] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center">
                            <span class="material-symbols-outlined">{{ $icons[$service->type] ?? 'local_car_wash' }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $service->name }}</h3>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <p class="text-slate-600 mb-4">{{ $service->description }}</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Harga:</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Durasi:</span>
                        <span class="font-medium text-slate-700">{{ $service->duration_minutes }} menit</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Kategori:</span>
                        <span class="font-medium text-slate-700 capitalize">{{ $service->category ?? 'mobil' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Tipe:</span>
                        <span class="font-medium text-slate-700 capitalize">{{ $service->type }}</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('services.edit', $service) }}" 
                        class="flex-1 bg-slate-100 text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-200 transition-colors text-sm text-center">
                        Edit
                    </a>
                    <a href="{{ route('services.show', $service) }}" 
                        class="bg-blue-100 text-blue-700 py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                    </a>
                    <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline"
                        onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="bg-red-100 text-red-700 py-2 px-3 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($services->isEmpty())
    <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">cleaning_services</span>
        <h3 class="text-lg font-medium text-slate-900 mb-2">Belum ada layanan</h3>
        <p class="text-slate-600 mb-4">Tambahkan layanan cuci pertama untuk memulai</p>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Tambah Layanan
        </button>
    </div>
    @endif
</div>
@endsection