@extends('layouts.app')

@section('title', 'Detail Advokasi & Aspirasi - SEKAR')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Breadcrumb -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('konsultasi.index') }}" class="hover:text-blue-600">Advokasi & Aspirasi</a>
                <span>/</span>
                <span class="text-gray-900">Detail</span>
            </div>
        </div>

        <div class="max-w-4xl">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                {{ $konsultasi->JENIS === 'ADVOKASI' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $konsultasi->JENIS }}
                            </span>
                            @if($konsultasi->KATEGORI_ADVOKASI)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                    {{ $konsultasi->KATEGORI_ADVOKASI }}
                                </span>
                            @endif
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $konsultasi->status_color }}-100 text-{{ $konsultasi->status_color }}-700">
                                {{ $konsultasi->status_text }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $konsultasi->JUDUL }}</h1>
                        <div class="flex items-center text-sm text-gray-600 space-x-4">
                            <span>{{ $konsultasi->TUJUAN }} {{ $konsultasi->TUJUAN_SPESIFIK ? '- ' . $konsultasi->TUJUAN_SPESIFIK : '' }}</span>
                            <span>{{ $konsultasi->CREATED_AT->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-line">{{ $konsultasi->DESKRIPSI }}</p>
                </div>

                @if($konsultasi->karyawan)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-600">{{ substr($konsultasi->karyawan->V_NAMA_KARYAWAN, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $konsultasi->karyawan->V_NAMA_KARYAWAN }}</p>
                            <p class="text-sm text-gray-600">{{ $konsultasi->karyawan->V_SHORT_UNIT }} - {{ $konsultasi->karyawan->V_KOTA_GEDUNG }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Forum Diskusi ({{ $konsultasi->komentar->count() }})</h3>
                </div>
                
                <div class="p-6">
                    <!-- Comments List -->
                    @forelse($konsultasi->komentar as $komentar)
                    <div class="flex space-x-3 mb-6 last:mb-0">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-medium text-gray-600">
                                @if($komentar->karyawan)
                                    {{ substr($komentar->karyawan->V_NAMA_KARYAWAN, 0, 1) }}
                                @else
                                    {{ substr($komentar->N_NIK, 0, 1) }}
                                @endif
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-medium text-gray-900">
                                    @if($komentar->karyawan)
                                        {{ $komentar->karyawan->V_NAMA_KARYAWAN }}
                                    @else
                                        {{ $komentar->N_NIK }}
                                    @endif
                                </span>
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $komentar->PENGIRIM_ROLE === 'ADMIN' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $komentar->PENGIRIM_ROLE === 'ADMIN' ? 'Admin' : 'Member' }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $komentar->CREATED_AT->format('d M Y, H:i') }}</span>
                            </div>
                            <p class="text-gray-700 whitespace-pre-line">{{ $komentar->KOMENTAR }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-gray-600">Belum ada komentar. Mulai diskusi dengan menambahkan komentar.</p>
                    </div>
                    @endforelse

                    <!-- Add Comment Form -->
                    @if($konsultasi->STATUS !== 'CLOSED')
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <form method="POST" action="{{ route('konsultasi.comment', $konsultasi->ID) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Komentar</label>
                                <textarea name="komentar" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Tulis komentar atau pertanyaan Anda..." required></textarea>
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-gray-500">Komentar akan dikirim ke admin terkait untuk ditindaklanjuti.</p>
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    Kirim Komentar
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <p class="text-gray-600 text-sm">Advokasi/Aspirasi ini telah ditutup. Tidak dapat menambahkan komentar baru.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection