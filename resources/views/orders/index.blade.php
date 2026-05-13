@extends('layouts.app')

@section('title', 'Pesanan Cuci - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pesanan Cuci</h1>
            <p class="text-slate-600">Kelola semua pesanan cuci kendaraan</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined">add</span>
            Pesanan Baru
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nomor plat, model, atau nomor pesanan..."
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Proses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div>
                <input 
                    type="date" 
                    name="date" 
                    value="{{ request('date') }}"
                    class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'date']))
                <a href="{{ route('orders.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kendaraan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $order->vehicle->license_plate }}</p>
                                <p class="text-xs text-slate-500">{{ $order->vehicle->type }} {{ $order->vehicle->model }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-900">{{ $order->service->name }}</p>
                            <p class="text-xs text-slate-500">{{ $order->service->duration_minutes }} menit</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-900">{{ $order->staff->name }}</p>
                        </td>
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
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$order->status] }}">
                                {{ $statusLabels[$order->status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $paymentColors = [
                                    'unpaid' => 'bg-red-100 text-red-800',
                                    'paid' => 'bg-green-100 text-green-800'
                                ];
                                $paymentLabels = [
                                    'unpaid' => 'Belum Bayar',
                                    'paid' => 'Lunas'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] }}">
                                {{ $paymentLabels[$order->payment_status] }}
                            </span>
                            @if($order->payment_method)
                                <p class="text-xs text-slate-500 mt-1">{{ strtoupper($order->payment_method) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="text-sm font-semibold text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl mb-2 block">inbox</span>
                            <p>Belum ada pesanan</p>
                            <a href="{{ route('orders.create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Buat pesanan pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto open print tab if redirected from successful payment
    @if(session('open_print_tab') && session('print_order_id'))
    setTimeout(function() {
        const printUrl = '{{ route("orders.receipt", session("print_order_id")) }}';
        window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        
        // Highlight the new order in the table
        setTimeout(function() {
            const orderRows = document.querySelectorAll('tbody tr');
            if (orderRows.length > 0) {
                const firstRow = orderRows[0]; // Assuming newest order is first
                firstRow.classList.add('bg-green-50', 'border-green-200');
                firstRow.style.animation = 'pulse 2s ease-in-out 3';
                
                // Remove highlight after 10 seconds
                setTimeout(function() {
                    firstRow.classList.remove('bg-green-50', 'border-green-200');
                    firstRow.style.animation = '';
                }, 10000);
            }
        }, 1000);
    }, 500);
    @endif

    // Add CSS animation for pulse effect
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush