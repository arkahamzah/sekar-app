<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - SEKAR</title>
    <link rel="icon" type="image/png" href="{{ asset('asset/logo-tabs.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        /* Form input focus */
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Modal overlay */
        .modal-overlay {
            backdrop-filter: blur(4px);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Fixed header transition */
        header {
            transition: box-shadow 0.2s ease;
        }

        /* Sidebar scrolling */
        aside {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        aside::-webkit-scrollbar {
            width: 4px;
        }

        aside::-webkit-scrollbar-track {
            background: transparent;
        }

        aside::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 2px;
        }

        aside::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        /* Dropdown animation */
        .dropdown-enter {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        
        .dropdown-enter-active {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: all 0.15s ease-out;
        }
        
        .dropdown-exit {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        
        .dropdown-exit-active {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
            transition: all 0.1s ease-in;
        }

        /* Content area smooth scrolling */
        main {
            scroll-behavior: smooth;
        }

        /* Prevent content jumping when scrollbar appears */
        body {
            overflow-y: scroll;
        }

        /* Active nav indicator */
        .nav-active {
            position: relative;
        }

        .nav-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background-color: #2563eb;
            border-radius: 0 2px 2px 0;
        }

        /* Admin section styling */
        .admin-section {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
        }

        /* Menu group styling */
        .menu-group {
            position: relative;
        }

        .menu-group:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 12px;
            right: 12px;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e5e7eb 20%, #e5e7eb 80%, transparent 100%);
        }

        /* Status badge styling */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }

        /* Card styling */
        .admin-card {
            background: white;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .admin-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    @if(Auth::check())
        <!-- Header dengan User Dropdown - Fixed -->
        <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
            <div class="max-w-none px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-14">
                    <div class="flex items-center">
                        <img src="{{ asset('asset/logo.png') }}" alt="SEKAR Logo" class="h-8">
                        <span class="ml-3 text-sm font-medium text-red-600 bg-red-50 px-2 py-1 rounded-md">
                            <i class="fas fa-shield-alt mr-1"></i>Admin Panel
                        </span>
                    </div>
                    
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg px-2 py-1">
                            <div class="w-7 h-7 bg-red-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" id="userMenuChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu (TANPA opsi Tampilan User) -->
                        <div id="userDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 hidden">
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->nik }}</p>
                                <p class="text-xs text-red-600 font-medium">
                                    <i class="fas fa-shield-alt mr-1"></i>Administrator
                                </p>
                            </div>
                            
                            <!-- MENGHAPUS bagian "Switch to User View" -->
                            <!-- Tidak ada lagi link ke route('dashboard') -->
                            
                            <!-- Admin Options -->
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                                Dashboard Admin
                            </a>
                            
                            <!-- Pengaturan Admin (jika ada route setting) -->
                            @if(Route::has('setting.index'))
                            <a href="{{ route('setting.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog mr-3 text-gray-400"></i>
                                Pengaturan Sistem
                            </a>
                            @endif
                            
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Layout Container -->
        <div class="flex pt-14 min-h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 fixed left-0 top-14 bottom-0 z-40">
                <div class="h-full flex flex-col">
                    <!-- Admin Info Card -->
                    <div class="admin-section rounded-lg p-4 m-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-shield-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Admin Panel</p>
                                <p class="text-red-600 text-sm">Management System</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="flex-1 px-4 pb-4 space-y-1">
                        <!-- Dashboard -->
                        <div class="menu-group">
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-tachometer-alt w-5 h-5"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                        </div>

                        <!-- Konsultasi & Aspirasi -->
                        <div class="menu-group">
                            <a href="{{ route('admin.konsultasi.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('admin.konsultasi.*') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-comments w-5 h-5"></i>
                                <span class="font-medium">Konsultasi & Aspirasi</span>
                            </a>
                        </div>

                        <!-- Data Anggota (jika ada) -->
                        @if(Route::has('admin.data-anggota.index'))
                        <div class="menu-group">
                            <a href="{{ route('admin.data-anggota.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('admin.data-anggota.*') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-users w-5 h-5"></i>
                                <span class="font-medium">Data Anggota</span>
                            </a>
                        </div>
                        @endif

                        <!-- Bantuan Perusahaan (jika ada) -->
                        @if(Route::has('admin.banpers.index'))
                        <div class="menu-group">
                            <a href="{{ route('admin.banpers.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('admin.banpers.*') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-hands-helping w-5 h-5"></i>
                                <span class="font-medium">Bantuan Perusahaan</span>
                            </a>
                        </div>
                        @endif

                        <!-- Management Section -->
                        <div class="menu-group">
                            <div class="px-3 py-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
                            </div>
                            
                            @if(Route::has('admin.users.index'))
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-user-cog w-5 h-5"></i>
                                <span class="font-medium">Manajemen User</span>
                            </a>
                            @endif

                            @if(Route::has('admin.reports.index'))
                            <a href="{{ route('admin.reports.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-chart-bar w-5 h-5"></i>
                                <span class="font-medium">Laporan</span>
                            </a>
                            @endif

                            @if(Route::has('setting.index'))
                            <a href="{{ route('setting.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ request()->routeIs('setting.*') ? 'bg-blue-50 text-blue-700 nav-active' : '' }}">
                                <i class="fas fa-cog w-5 h-5"></i>
                                <span class="font-medium">Pengaturan</span>
                            </a>
                            @endif
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 ml-64">
                <div class="p-6">
                    <!-- Page Header -->
                    @hasSection('header')
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">@yield('header')</h1>
                                    @hasSection('description')
                                        <p class="text-gray-600 mt-1">@yield('description')</p>
                                    @endif
                                </div>
                                @hasSection('header-actions')
                                    <div class="flex items-center space-x-3">
                                        @yield('header-actions')
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                {{ session('warning') }}
                            </div>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                {{ session('info') }}
                            </div>
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <!-- Jika tidak login, redirect ke login -->
        <script>
            window.location.href = "{{ route('login') }}";
        </script>
    @endif

    <!-- JavaScript untuk Dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User dropdown toggle
            const userMenuButton = document.getElementById('userMenuButton');
            const userDropdown = document.getElementById('userDropdown');
            const userMenuChevron = document.getElementById('userMenuChevron');

            if (userMenuButton && userDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    if (userDropdown.classList.contains('hidden')) {
                        userDropdown.classList.remove('hidden');
                        userMenuChevron.style.transform = 'rotate(180deg)';
                    } else {
                        userDropdown.classList.add('hidden');
                        userMenuChevron.style.transform = 'rotate(0deg)';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                        userDropdown.classList.add('hidden');
                        userMenuChevron.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Auto-hide flash messages after 5 seconds
            const flashMessages = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"], [class*="bg-yellow-50"], [class*="bg-blue-50"]');
            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    message.style.transition = 'opacity 0.5s ease-out';
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.remove();
                    }, 500);
                }, 5000);
            });
        });

        // Function untuk toggle dropdown (jika diperlukan di view lain)
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>