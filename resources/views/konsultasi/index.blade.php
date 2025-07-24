@extends('layouts.app')

@section('title', 'Konsultasi & Aspirasi - SEKAR')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                <div class="flex items-center">
                    <img src="{{ asset('asset/logo.png') }}" alt="SEKAR Logo" class="h-8">
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-gray-400 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="text-gray-700 text-sm font-medium">{{ Auth::user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-56 bg-white shadow-sm flex-shrink-0">
            <nav class="mt-6">
                <div class="px-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg text-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('profile.index') }}" class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg text-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                        </svg>
                        Profile Sekar
                    </a>
                    <a href="{{ route('konsultasi.index') }}" class="flex items-center px-3 py-2.5 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Konsultasi & Aspirasi
                    </a>
                    <a href="#" class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg text-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                        </svg>
                        Banpers
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-auto">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Konsultasi & Aspirasi</h1>
                <a href="{{ route('konsultasi.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Buat Baru
                </a>
            </div>

            <!-- List Konsultasi -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @forelse($konsultasi as $item)
                <div class="border-b border-gray-200 last:border-b-0 p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $item->JENIS === 'ADVOKASI' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $item->JENIS }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-700">
                                    {{ $item->status_text }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $item->TUJUAN }} {{ $item->TUJUAN_SPESIFIK ? '- ' . $item->TUJUAN_SPESIFIK : '' }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $item->JUDUL }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item->DESKRIPSI, 150) }}</p>
                            <div class="flex items-center text-xs text-gray-500 space-x-4">
                                <span>{{ $item->CREATED_AT->format('d M Y, H:i') }}</span>
                                <span>{{ $item->komentar->count() }} komentar</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('konsultasi.show', $item->ID) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada konsultasi</h3>
                    <p class="text-gray-600 mb-4">Mulai dengan membuat konsultasi atau aspirasi pertama Anda.</p>
                    <a href="{{ route('konsultasi.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Buat Konsultasi
                    </a>
                </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
@endsection