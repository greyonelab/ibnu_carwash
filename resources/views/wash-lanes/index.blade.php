@extends('layouts.app')

@section('title', 'Jalur Cuci - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Jalur Cuci</h1>
            <p class="text-slate-600">Kelola jalur cuci dan antrian kendaraan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('queue-display.index') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <span class="material-symbols-outlined">tv</span>
                Display Antrian
            </a>
            <a href="{{ route('wash-lanes.create') }}" 
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined">add</span>
                Tambah Jalur
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">route</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $lanes->count() }}</p>
                    <p class="text-sm text-slate-600">Total Jalur</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $lanes->where('is_active', true)->count() }}</p>
                    <p class="text-sm text-slate-600">Jalur Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600">queue</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $lanes->sum('current_queue') }}</p>
                    <p class="text-sm text-slate-600">Total Antrian</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600">speed</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $lanes->sum('max_queue') }}</p>
                    <p class="text-sm text-slate-600">Kapasitas Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lanes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($lanes as $lane)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ substr($lane->name, -1) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $lane->name }}</h3>
                            <p class="text-sm text-slate-600 capitalize">{{ $lane->type }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $lane->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $lane->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                
                <!-- Queue Status -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-slate-600">Antrian</span>
                        <span class="text-sm font-medium text-slate-900">{{ $lane->current_queue }}/{{ $lane->max_queue }}</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $lane->max_queue > 0 ? ($lane->current_queue / $lane->max_queue) * 100 : 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 mt-1">
                        <span>{{ $lane->current_queue > 0 ? 'Ada antrian' : 'Kosong' }}</span>
                        <span>{{ $lane->current_queue >= $lane->max_queue ? 'Penuh' : 'Tersedia' }}</span>
                    </div>
                </div>

                @if($lane->description)
                <p class="text-sm text-slate-600 mb-4">{{ $lane->description }}</p>
                @endif
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('wash-lanes.show', $lane) }}" 
                        class="flex-1 bg-slate-100 text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-200 transition-colors text-sm text-center">
                        Lihat Antrian
                    </a>
                    <a href="{{ route('wash-lanes.edit', $lane) }}" 
                        class="bg-blue-100 text-blue-700 py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </a>
                    <form action="{{ route('wash-lanes.destroy', $lane) }}" method="POST" class="inline"
                        onsubmit="return confirm('Yakin ingin menghapus jalur ini?')">
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

    @if($lanes->isEmpty())
    <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">route</span>
        <h3 class="text-lg font-medium text-slate-900 mb-2">Belum ada jalur cuci</h3>
        <p class="text-slate-600 mb-4">Tambahkan jalur cuci pertama untuk memulai</p>
        <a href="{{ route('wash-lanes.create') }}" 
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Tambah Jalur
        </a>
    </div>
    @endif
</div>
@endsection