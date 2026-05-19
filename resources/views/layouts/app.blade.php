<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WashManager Pro')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-low": "#eff4ff",
                        "secondary-fixed": "#dbe1ff",
                        "on-surface-variant": "#45464d",
                        "surface": "#f8f9ff",
                        "on-secondary": "#ffffff",
                        "secondary-fixed-dim": "#b4c5ff",
                        "on-tertiary-container": "#008cc7",
                        "on-primary": "#ffffff",
                        "surface-container-highest": "#d3e4fe",
                        "tertiary-fixed-dim": "#89ceff",
                        "on-primary-fixed-variant": "#3f465c",
                        "surface-variant": "#d3e4fe",
                        "surface-dim": "#cbdbf5",
                        "tertiary": "#000000",
                        "on-primary-container": "#7c839b",
                        "on-tertiary-fixed": "#001e2f",
                        "on-surface": "#0b1c30",
                        "on-error-container": "#93000a",
                        "inverse-primary": "#bec6e0",
                        "surface-container-lowest": "#ffffff",
                        "error": "#ba1a1a",
                        "on-secondary-fixed-variant": "#003ea8",
                        "tertiary-fixed": "#c9e6ff",
                        "tertiary-container": "#001e2f",
                        "on-tertiary": "#ffffff",
                        "surface-bright": "#f8f9ff",
                        "on-error": "#ffffff",
                        "on-secondary-container": "#fefcff",
                        "on-tertiary-fixed-variant": "#004c6e",
                        "inverse-on-surface": "#eaf1ff",
                        "background": "#f8f9ff",
                        "surface-tint": "#565e74",
                        "on-primary-fixed": "#131b2e",
                        "primary-fixed-dim": "#bec6e0",
                        "primary-container": "#131b2e",
                        "secondary-container": "#316bf3",
                        "outline-variant": "#c6c6cd",
                        "secondary": "#0051d5",
                        "inverse-surface": "#213145",
                        "outline": "#76777d",
                        "surface-container": "#e5eeff",
                        "on-background": "#0b1c30",
                        "on-secondary-fixed": "#00174b",
                        "surface-container-high": "#dce9ff",
                        "primary-fixed": "#dae2fd",
                        "primary": "#000000",
                        "error-container": "#ffdad6"
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    spacing: {
                        "xl": "48px",
                        "base": "4px",
                        "sm": "16px",
                        "md": "24px",
                        "xs": "8px",
                        "margin": "32px",
                        "lg": "32px",
                        "gutter": "24px"
                    },
                    fontFamily: {
                        "body-md": ["Inter"],
                        "label-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "stats-num": ["Inter"],
                        "h1": ["Inter"],
                        "h3": ["Inter"],
                        "h2": ["Inter"]
                    },
                    fontSize: {
                        "body-md": ["14px", {"lineHeight": "1.5", "letterSpacing": "0", "fontWeight": "400"}],
                        "label-md": ["12px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "body-lg": ["16px", {"lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400"}],
                        "stats-num": ["28px", {"lineHeight": "1", "letterSpacing": "-0.03em", "fontWeight": "700"}],
                        "h1": ["32px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "h3": ["20px", {"lineHeight": "1.4", "letterSpacing": "0", "fontWeight": "600"}],
                        "h2": ["24px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-background text-on-surface">
    <!-- TopAppBar -->
    <header class="fixed top-0 left-0 w-full z-50 flex items-center justify-between px-4 h-16 bg-white shadow-[0px_1px_3px_rgba(15,23,42,0.08)] border-b border-slate-200">
        <div class="flex items-center gap-4">
            <button id="sidebar-toggle" class="p-2 hover:bg-slate-50 transition-colors rounded-lg active:opacity-80 lg:hidden">
                <span class="material-symbols-outlined text-slate-900">menu</span>
            </button>
            <h1 class="text-lg font-bold tracking-tighter text-slate-900">WashManager Pro</h1>
            
            <!-- Search Bar -->
            <div class="hidden md:block relative ml-8">
                <div class="relative">
                    <input 
                        type="text" 
                        id="global-search"
                        placeholder="Cari pesanan, plat nomor, karyawan..."
                        class="w-80 pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-sm">search</span>
                </div>
                
                <!-- Search Results Dropdown -->
                <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-96 overflow-y-auto z-50">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <!-- Mobile Search Button -->
            <button id="mobile-search-toggle" class="p-2 hover:bg-slate-50 transition-colors rounded-lg active:opacity-80 md:hidden">
                <span class="material-symbols-outlined text-slate-900">search</span>
            </button>
            
            <!-- Notifications -->
            <div class="relative">
                <button id="notifications-toggle" class="p-2 hover:bg-slate-50 transition-colors rounded-lg active:opacity-80 relative">
                    <span class="material-symbols-outlined text-slate-900">notifications</span>
                    <!-- Notification Badge -->
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                </button>
                
                <!-- Notifications Dropdown -->
                <div id="notifications-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-slate-200 py-2 z-50">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <h3 class="font-medium text-slate-900">Notifikasi</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <div class="px-4 py-3 hover:bg-slate-50 border-b border-slate-50">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-blue-600 text-sm">local_car_wash</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900">Pesanan Baru</p>
                                    <p class="text-xs text-slate-500">B 1234 ABC - Cuci Premium</p>
                                    <p class="text-xs text-slate-400 mt-1">2 menit yang lalu</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 hover:bg-slate-50 border-b border-slate-50">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900">Cuci Selesai</p>
                                    <p class="text-xs text-slate-500">D 5678 EFG telah selesai dicuci</p>
                                    <p class="text-xs text-slate-400 mt-1">5 menit yang lalu</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 hover:bg-slate-50">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-yellow-600 text-sm">schedule</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900">Antrian Panjang</p>
                                    <p class="text-xs text-slate-500">5 mobil sedang mengantri</p>
                                    <p class="text-xs text-slate-400 mt-1">10 menit yang lalu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-2 border-t border-slate-100">
                        <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua</button>
                    </div>
                </div>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profile-dropdown" class="flex items-center gap-2 p-2 hover:bg-slate-50 transition-colors rounded-lg">
                    <div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden border border-slate-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-slate-600">person</span>
                    </div>
                    <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ auth()->user()->name }}</span>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white border-r border-slate-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
            <nav class="p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('orders.*') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">local_car_wash</span>
                    <span>Pesanan Cuci</span>
                </a>
                
                <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('services.*') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">cleaning_services</span>
                    <span>Layanan</span>
                </a>
                
                <a href="{{ route('staff.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('staff.*') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">group</span>
                    <span>Karyawan</span>
                </a>
                
                <a href="{{ route('wash-lanes.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('wash-lanes.*') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">route</span>
                    <span>Jalur Cuci</span>
                </a>
                
                <a href="{{ route('commission.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('commission.*') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">percent</span>
                    <span>Pengaturan Komisi</span>
                </a>
                
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 {{ request()->routeIs('reports.*') ? 'bg-slate-100 text-slate-900 font-medium' : '' }}">
                    <span class="material-symbols-outlined">assessment</span>
                    <span>Laporan</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 pt-16 min-h-screen">
            <!-- Flash Messages -->
            @if(session('success'))
                <div id="success-notification" class="mx-4 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                        <button onclick="closeNotification('success-notification')" class="flex-shrink-0 text-green-400 hover:text-green-600">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div id="error-notification" class="mx-4 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-red-600 text-xl">error</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                        <button onclick="closeNotification('error-notification')" class="flex-shrink-0 text-red-400 hover:text-red-600">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div id="validation-errors" class="mx-4 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-red-600 text-xl">error</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-red-800 font-medium mb-2">Terjadi kesalahan:</p>
                            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="closeNotification('validation-errors')" class="flex-shrink-0 text-red-400 hover:text-red-600">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <script>
        // Sidebar toggle functionality
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        
        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
        
        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // Profile dropdown functionality
        const profileDropdown = document.getElementById('profile-dropdown');
        const profileMenu = document.getElementById('profile-menu');
        
        profileDropdown?.addEventListener('click', () => {
            profileMenu.classList.toggle('hidden');
            // Close other dropdowns
            document.getElementById('notifications-menu')?.classList.add('hidden');
        });

        // Notifications dropdown functionality
        const notificationsToggle = document.getElementById('notifications-toggle');
        const notificationsMenu = document.getElementById('notifications-menu');
        
        notificationsToggle?.addEventListener('click', () => {
            notificationsMenu.classList.toggle('hidden');
            // Close other dropdowns
            profileMenu?.classList.add('hidden');
        });
        
        // Global search functionality
        const globalSearch = document.getElementById('global-search');
        const searchResults = document.getElementById('search-results');
        let searchTimeout;
        
        globalSearch?.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        });
        
        function displaySearchResults(results) {
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="p-4 text-sm text-slate-500 text-center">Tidak ada hasil ditemukan</div>';
            } else {
                searchResults.innerHTML = results.map(result => `
                    <a href="${result.url}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-400 text-sm">${result.icon}</span>
                            <div>
                                <p class="text-sm font-medium text-slate-900">${result.title}</p>
                                <p class="text-xs text-slate-500">${result.subtitle}</p>
                            </div>
                        </div>
                    </a>
                `).join('');
            }
            searchResults.classList.remove('hidden');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileDropdown?.contains(e.target) && !profileMenu?.contains(e.target)) {
                profileMenu?.classList.add('hidden');
            }
            if (!notificationsToggle?.contains(e.target) && !notificationsMenu?.contains(e.target)) {
                notificationsMenu?.classList.add('hidden');
            }
            if (!globalSearch?.contains(e.target) && !searchResults?.contains(e.target)) {
                searchResults?.classList.add('hidden');
            }
        });

        // Auto-hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('[id$="-notification"]');
            flashMessages.forEach(message => {
                message.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                message.style.opacity = '0';
                message.style.transform = 'translateY(-10px)';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);

        // Function to close notification manually
        window.closeNotification = function(id) {
            const notification = document.getElementById(id);
            if (notification) {
                notification.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => notification.remove(), 300);
            }
        }

        // Add entrance animation to notifications
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('[id$="-notification"]');
            notifications.forEach(notification => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    notification.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateY(0)';
                }, 100);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>