<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SEKAR') }}</title>
    
    <!-- Tailwind CSS dari CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <style>
        .nav-active {
            border-left: 3px solid #2563eb;
            background: linear-gradient(90deg, #dbeafe 0%, transparent 100%);
        }
        
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Custom animations */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-enter {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 150ms ease-out, transform 150ms ease-out;
        }

        .dropdown-enter-active {
            opacity: 1;
            transform: scale(1);
        }

        .dropdown-exit {
            opacity: 1;
            transform: scale(1);
            transition: opacity 75ms ease-in, transform 75ms ease-in;
        }

        .dropdown-exit-active {
            opacity: 0;
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Header - Fixed -->
        <header class="fixed top-0 left-0 right-0 bg-white shadow-sm border-b border-gray-200 z-50" style="height: 3.5rem;">
            <div class="flex items-center justify-between h-full px-4">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-sm font-bold">S</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">SEKAR</h1>
                            <p class="text-xs text-gray-500 -mt-1">Sistem Elektronik Karyawan</p>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button id="userDropdownButton" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">NIK: {{ Auth::user()->nik }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 dropdown-enter">
                        <!-- User Info Header -->
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">NIK: {{ Auth::user()->nik }}</p>
                                    @if(Auth::user()->pengurus && Auth::user()->pengurus->role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                            {{ Auth::user()->pengurus->role->NAME }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Main Menu Items -->
                        <div class="py-1">
                            <!-- Profile Menu -->
                            <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Profile Sekar</span>
                            </a>
                            
                            <!-- Sertifikat Menu -->
                            <a href="{{ route('sertifikat.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Sertifikat Anggota</span>
                            </a>

                            <!-- PERBAIKAN: Change Password Link -->
                            <a href="{{ route('profile.change-password') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2a2 2 0 01-2-2m0 0a2 2 0 012-2m0 0a2 2 0 00-2 2m-6 8a6 6 0 1112 0 6 6 0 01-12 0zm6-3a1 1 0 011-1h.01a1 1 0 110 2H13a1 1 0 01-1-1z"></path>
                                </svg>
                                <span>Ubah Password</span>
                            </a>
                        </div>
                        
                        <!-- Admin Menu Section -->
                        @if(auth()->user()->pengurus && auth()->user()->pengurus->role && in_array(auth()->user()->pengurus->role->NAME, ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD']))
                        <div class="border-t border-gray-100 mt-1">
                            <div class="px-4 py-2 bg-blue-50">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-blue-600">Menu Admin</span>
                                </div>
                            </div>
                            
                            @if(auth()->user()->pengurus->role->NAME === 'ADM')
                            <div class="py-1">
                                <a href="{{ route('admin.password-reset.tokens') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Token Reset Password</span>
                                    <span class="ml-auto text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded-full">Super</span>
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Logout Section -->
                        <div class="border-t border-gray-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Keluar dari Sistem</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sidebar - Fixed -->
        <aside class="fixed left-0 top-14 w-64 bg-white shadow-sm border-r border-gray-200 z-40" style="height: calc(100vh - 3.5rem);">
            <nav class="h-full overflow-y-auto py-6 sidebar-scroll">
                <div class="px-3 space-y-2">
                    <!-- Main Navigation -->
                    <div class="menu-group pb-4">
                        <div class="px-3 mb-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Utama</p>
                        </div>
                        
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 nav-active' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg text-sm transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('profile.index') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('profile.*') && !request()->routeIs('profile.change-password') ? 'text-blue-600 bg-blue-50 nav-active' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg text-sm transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Profile Sekar</span>
                        </a>

                        <a href="{{ route('data-anggota.index') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('data-anggota.*') ? 'text-blue-600 bg-blue-50 nav-active' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg text-sm transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                            <span>Data Anggota</span>
                        </a>

                        <!-- Submenu untuk Profile jika sedang di halaman change password -->
                        @if(request()->routeIs('profile.change-password'))
                        <div class="ml-8 space-y-1">
                            <a href="{{ route('profile.change-password') }}" class="flex items-center px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm nav-active">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2a2 2 0 01-2-2m0 0a2 2 0 012-2m0 0a2 2 0 00-2 2m-6 8a6 6 0 1112 0 6 6 0 01-12 0zm6-3a1 1 0 011-1h.01a1 1 0 110 2H13a1 1 0 01-1-1z"></path>
                                </svg>
                                <span>Ubah Password</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 pt-14 min-h-screen">
            @yield('content')
        </main>
    </div>

    <!-- JavaScript untuk Dropdown (Vanilla JS) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButton = document.getElementById('userDropdownButton');
            const dropdownMenu = document.getElementById('userDropdown');

            if (dropdownButton && dropdownMenu) {
                dropdownButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    if (dropdownMenu.classList.contains('hidden')) {
                        // Show dropdown
                        dropdownMenu.classList.remove('hidden');
                        dropdownMenu.classList.add('dropdown-enter');
                        setTimeout(() => {
                            dropdownMenu.classList.add('dropdown-enter-active');
                        }, 10);
                    } else {
                        // Hide dropdown
                        hideDropdown();
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        hideDropdown();
                    }
                });

                // Close dropdown when pressing Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        hideDropdown();
                    }
                });

                function hideDropdown() {
                    dropdownMenu.classList.remove('dropdown-enter-active');
                    dropdownMenu.classList.add('dropdown-exit-active');
                    setTimeout(() => {
                        dropdownMenu.classList.add('hidden');
                        dropdownMenu.classList.remove('dropdown-enter', 'dropdown-exit-active');
                    }, 150);
                }
            }

            // CSRF Token untuk AJAX calls (jika diperlukan)
            window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
    </script>
</body>
</html>