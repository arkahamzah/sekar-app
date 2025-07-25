@extends('layouts.app')

@section('title', 'Konsultasi & Aspirasi - SEKAR')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
    </div>
</div>
@endsection