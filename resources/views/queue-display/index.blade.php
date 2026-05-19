<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Cuci Mobil - WashManager Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .pulse-slow { animation: pulse 3s infinite; }
        .slide-up { animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-gradient-to-r from-blue-500 to-purple-600">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-2xl">local_car_wash</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">WashManager Pro</h1>
                        <p class="text-slate-600">Antrian Cuci Kendaraan</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-slate-900" id="current-time"></div>
                    <div class="text-sm text-slate-600" id="current-date"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-2xl">route</span>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" id="total-lanes">{{ $lanes->count() }}</div>
                        <div class="text-sm text-slate-600">Jalur Aktif</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-yellow-600 text-2xl">queue</span>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" id="total-queue">
                            {{ $lanes->sum(function($lane) { return $lane->washOrders->count(); }) }}
                        </div>
                        <div class="text-sm text-slate-600">Total Antrian</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-2xl">local_car_wash</span>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" id="in-progress">
                            {{ $lanes->sum(function($lane) { return $lane->washOrders->where('status', 'in_progress')->count(); }) }}
                        </div>
                        <div class="text-sm text-slate-600">Sedang Dicuci</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600 text-2xl">schedule</span>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" id="avg-wait">~15</div>
                        <div class="text-sm text-slate-600">Menit Tunggu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lanes Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8" id="lanes-container">
            @foreach($lanes as $lane)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden lane-card" data-lane-id="{{ $lane->id }}">
                <!-- Lane Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-xl">{{ substr($lane->name, -1) }}</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">{{ $lane->name }}</h3>
                                <p class="text-blue-100 capitalize">{{ $lane->type }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">{{ $lane->washOrders->count() }}</div>
                            <div class="text-blue-100 text-sm">Antrian</div>
                        </div>
                    </div>
                </div>

                <!-- Queue List -->
                <div class="p-6">
                    @if($lane->washOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach($lane->washOrders->take(5) as $index => $order)
                            <div class="flex items-center gap-4 p-4 rounded-xl border-2 {{ $order->status === 'in_progress' ? 'border-green-200 bg-green-50' : 'border-slate-200 bg-slate-50' }} queue-item">
                                <div class="flex-shrink-0">
                                    @if($order->status === 'in_progress')
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center pulse-slow">
                                            <span class="material-symbols-outlined text-white text-sm">local_car_wash</span>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-slate-400 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ $order->queue_position }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-slate-900">{{ $order->vehicle->license_plate }}</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                            {{ $order->vehicle->type }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-slate-600">{{ $order->service->name }}</div>
                                </div>
                                
                                <div class="text-right">
                                    @if($order->status === 'in_progress')
                                        <div class="text-green-600 font-bold text-sm">SEDANG DICUCI</div>
                                    @else
                                        <div class="text-slate-600 text-sm">
                                            {{ $order->queued_at ? $order->queued_at->format('H:i') : '-' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            
                            @if($lane->washOrders->count() > 5)
                            <div class="text-center py-2">
                                <span class="text-slate-500 text-sm">+{{ $lane->washOrders->count() - 5 }} antrian lainnya</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-slate-300 text-4xl">check_circle</span>
                            <p class="text-slate-500 mt-2">Jalur Kosong</p>
                            <p class="text-green-600 font-medium">Siap Melayani</p>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($lanes->isEmpty())
        <div class="text-center py-16">
            <span class="material-symbols-outlined text-slate-300 text-6xl">route</span>
            <h3 class="text-xl font-medium text-slate-900 mt-4">Belum Ada Jalur Aktif</h3>
            <p class="text-slate-600">Silakan hubungi petugas untuk informasi lebih lanjut</p>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="bg-white border-t border-slate-200 mt-12">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    Terakhir diperbarui: <span id="last-updated"></span>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Live Update
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID');
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Auto refresh data
        async function refreshData() {
            try {
                const response = await fetch('/api/queue-display');
                const data = await response.json();
                
                if (data.success) {
                    updateDisplay(data.data);
                    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString('id-ID');
                }
            } catch (error) {
                console.error('Error refreshing data:', error);
            }
        }

        function updateDisplay(lanes) {
            // Update summary stats
            document.getElementById('total-lanes').textContent = lanes.length;
            document.getElementById('total-queue').textContent = lanes.reduce((sum, lane) => sum + lane.current_queue, 0);
            document.getElementById('in-progress').textContent = lanes.reduce((sum, lane) => 
                sum + lane.queue.filter(order => order.status === 'in_progress').length, 0
            );

            // Update lanes
            lanes.forEach(lane => {
                const laneCard = document.querySelector(`[data-lane-id="${lane.id}"]`);
                if (laneCard) {
                    // Update queue count in header
                    const queueCount = laneCard.querySelector('.text-2xl.font-bold');
                    if (queueCount) {
                        queueCount.textContent = lane.current_queue;
                    }
                    
                    // Update queue items (simplified for this example)
                    // In a real implementation, you'd want to update the entire queue list
                }
            });
        }

        // Initialize
        updateTime();
        setInterval(updateTime, 1000);
        setInterval(refreshData, 10000); // Refresh every 10 seconds
        
        // Initial last updated time
        document.getElementById('last-updated').textContent = new Date().toLocaleTimeString('id-ID');
    </script>
</body>
</html>