@extends('layouts.app')

@section('title', 'Dashboard - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Welcome Section -->
    <div class="flex flex-col gap-2">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">RINGKASAN</p>
        <h2 class="text-2xl font-bold text-slate-900">Selamat {{ date('H') < 12 ? 'Pagi' : (date('H') < 18 ? 'Siang' : 'Malam') }}, {{ auth()->user()->name }}</h2>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($revenueChange >= 0)
                    <span class="text-xs font-bold text-green-600 flex items-center bg-green-50 px-2 py-1 rounded-full">
                        <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                        +{{ number_format($revenueChange, 1) }}%
                    </span>
                @else
                    <span class="text-xs font-bold text-red-600 flex items-center bg-red-50 px-2 py-1 rounded-full">
                        <span class="material-symbols-outlined text-sm mr-1">trending_down</span>
                        {{ number_format($revenueChange, 1) }}%
                    </span>
                @endif
                <span class="text-xs text-slate-500">vs kemarin</span>
            </div>
        </div>

        <!-- Cars Served Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mobil Terlayani</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">{{ $carsServed }}</p>
                </div>
                <div class="bg-green-100 text-green-600 p-2 rounded-lg">
                    <span class="material-symbols-outlined">local_car_wash</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">{{ $carsInQueue }} mobil sedang mengantri</span>
            </div>
        </div>

        <!-- Commission Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Komisi Karyawan</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">Rp {{ number_format($todayCommission, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-2 rounded-lg">
                    <span class="material-symbols-outlined">group</span>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div class="bg-blue-600 w-[65%] h-full"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2">65% dari target harian tercapai</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-slate-900">Aktivitas Terkini</h3>
                <a href="{{ route('orders.index') }}" class="text-blue-600 text-sm font-medium hover:text-blue-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kendaraan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentActivities as $activity)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 rounded flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-600">directions_car</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $activity->vehicle->model ?? $activity->vehicle->type }}</p>
                                        <p class="text-xs text-slate-500">{{ $activity->vehicle->license_plate }} • {{ $activity->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-900">{{ $activity->service->name }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'in_progress' => 'Proses',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$activity->status] }}">
                                    {{ $statusLabels[$activity->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 text-right">Rp {{ number_format($activity->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl mb-2 block">inbox</span>
                                Belum ada aktivitas hari ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bay Status -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Status Lajur</h3>
            <div class="space-y-3">
                @foreach($bayStatus as $bay)
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $bay['status'] === 'occupied' ? 'bg-red-500 animate-pulse' : 'bg-green-500' }}"></div>
                        <span class="font-medium text-slate-900">{{ $bay['name'] }}</span>
                    </div>
                    <span class="text-xs font-medium text-slate-500">
                        {{ $bay['status'] === 'occupied' ? $bay['remaining_minutes'] . ' mnt lagi' : 'Tersedia' }}
                    </span>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500">Progres Shift</span>
                    <span class="text-xs font-bold text-slate-900">6/8 Jam</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 w-[75%] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined">add</span>
            Pesanan Baru
        </a>
        <button onclick="openQuickOrderModal()" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
            <span class="material-symbols-outlined">flash_on</span>
            Cuci Express
        </button>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
            <span class="material-symbols-outlined">list</span>
            Lihat Semua Pesanan
        </a>
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
            <span class="material-symbols-outlined">assessment</span>
            Laporan
        </a>
    </div>

    <!-- Quick Order Modal -->
    <div id="quickOrderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Cuci Express</h3>
                <button onclick="closeQuickOrderModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="vehicle_type" value="Sedan">
                <input type="hidden" name="service_id" value="1">
                <input type="hidden" name="staff_id" value="1">
                <input type="hidden" name="auto_complete" value="1">
                <input type="hidden" name="redirect_to_create" value="1">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Plat Nomor</label>
                    <input type="text" name="license_plate" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="B 1234 ABC">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:border-green-300">
                            <input type="radio" name="payment_method" value="cash" class="sr-only" required>
                            <span class="material-symbols-outlined text-green-600 mb-1">payments</span>
                            <span class="text-xs">Cash</span>
                        </label>
                        <label class="flex flex-col items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-300">
                            <input type="radio" name="payment_method" value="qris" class="sr-only" required>
                            <span class="material-symbols-outlined text-blue-600 mb-1">qr_code</span>
                            <span class="text-xs">QRIS</span>
                        </label>
                        <label class="flex flex-col items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:border-purple-300">
                            <input type="radio" name="payment_method" value="transfer" class="sr-only" required>
                            <span class="material-symbols-outlined text-purple-600 mb-1">account_balance</span>
                            <span class="text-xs">Transfer</span>
                        </label>
                    </div>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeQuickOrderModal()" class="flex-1 bg-slate-200 text-slate-700 py-2 px-4 rounded-lg hover:bg-slate-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        Buat & Print
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openQuickOrderModal() {
        document.getElementById('quickOrderModal').classList.remove('hidden');
    }
    
    function closeQuickOrderModal() {
        document.getElementById('quickOrderModal').classList.add('hidden');
    }
    
    // Quick order modal payment method styling
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#quickOrderModal input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Reset all labels
                document.querySelectorAll('#quickOrderModal label').forEach(label => {
                    label.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50', 'border-purple-500', 'bg-purple-50');
                    label.classList.add('border-slate-200');
                });
                
                // Style selected label
                if (this.checked) {
                    const label = this.closest('label');
                    label.classList.remove('border-slate-200');
                    if (this.value === 'cash') {
                        label.classList.add('border-green-500', 'bg-green-50');
                    } else if (this.value === 'qris') {
                        label.classList.add('border-blue-500', 'bg-blue-50');
                    } else if (this.value === 'transfer') {
                        label.classList.add('border-purple-500', 'bg-purple-50');
                    }
                }
            });
        });
        
        // Close modal when clicking outside
        document.getElementById('quickOrderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickOrderModal();
            }
        });
    });
</script>
@endpush