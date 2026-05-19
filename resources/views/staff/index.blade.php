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
        <div class="flex gap-2">
            <a href="{{ route('staff.create') }}" 
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined">add</span>
                Tambah Karyawan
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-slate-700 mb-2">Cari Karyawan</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Nama, posisi, atau telepon..."
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Posisi</label>
                <select name="position" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Posisi</option>
                    <option value="Washer" {{ request('position') == 'Washer' ? 'selected' : '' }}>Washer</option>
                    <option value="Detailer" {{ request('position') == 'Detailer' ? 'selected' : '' }}>Detailer</option>
                    <option value="Supervisor" {{ request('position') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                    <option value="Manager" {{ request('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <select name="status" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined">search</span>
            </button>
            <a href="{{ route('staff.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition-colors">
                Reset
            </a>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">group</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $staff->count() }}</p>
                    <p class="text-sm text-slate-600">Total Karyawan</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $staff->where('is_active', true)->count() }}</p>
                    <p class="text-sm text-slate-600">Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600">local_car_wash</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">
                        {{ $staff->sum(function($member) { return $member->washOrders()->whereMonth('created_at', now()->month)->count(); }) }}
                    </p>
                    <p class="text-sm text-slate-600">Pesanan Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600">payments</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">
                        Rp {{ number_format($staff->sum(function($member) { 
                            return $member->washOrders()
                                ->whereMonth('created_at', now()->month)
                                ->where('status', 'completed')
                                ->where('payment_status', 'paid')
                                ->get()
                                ->sum(function($order) use ($member) { 
                                    return $order->total_price * ($member->commission_rate / 100); 
                                }); 
                        }), 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-slate-600">Total Komisi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($staff as $member)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
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
                    
                    @if($member->email)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-sm">email</span>
                        <span class="text-sm text-slate-600">{{ $member->email }}</span>
                    </div>
                    @endif
                    
                    @if($member->salary)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-sm">payments</span>
                        <span class="text-sm text-slate-600">Gaji: Rp {{ number_format($member->salary, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-sm">percent</span>
                        <span class="text-sm text-slate-600">Komisi: {{ $member->commission_rate ?? 0 }}%</span>
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
                                Rp {{ number_format($member->washOrders()->whereMonth('created_at', now()->month)->where('status', 'completed')->where('payment_status', 'paid')->get()->sum(function($order) use ($member) { return $order->total_price * (($member->commission_rate ?? 0) / 100); }), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('staff.edit', $member) }}" 
                        class="flex-1 bg-slate-100 text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-200 transition-colors text-sm text-center">
                        Edit
                    </a>
                    <a href="{{ route('staff.show', $member) }}" 
                        class="bg-blue-100 text-blue-700 py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                    </a>
                    <form action="{{ route('staff.destroy', $member) }}" method="POST" class="inline"
                        onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
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

    @if($staff->isEmpty())
    <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">group</span>
        <h3 class="text-lg font-medium text-slate-900 mb-2">
            {{ request()->hasAny(['search', 'position', 'status']) ? 'Tidak ada karyawan yang sesuai filter' : 'Belum ada karyawan' }}
        </h3>
        <p class="text-slate-600 mb-4">
            {{ request()->hasAny(['search', 'position', 'status']) ? 'Coba ubah filter pencarian' : 'Tambahkan karyawan pertama untuk memulai' }}
        </p>
        @if(!request()->hasAny(['search', 'position', 'status']))
        <a href="{{ route('staff.create') }}" 
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Tambah Karyawan
        </a>
        @endif
    </div>
    @endif
</div>
@endsection