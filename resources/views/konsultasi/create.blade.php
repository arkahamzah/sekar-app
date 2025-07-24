@extends('layouts.app')

@section('title', 'Buat Konsultasi & Aspirasi - SEKAR')

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
            <div class="mb-6">
                <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                    <a href="{{ route('konsultasi.index') }}" class="hover:text-blue-600">Konsultasi & Aspirasi</a>
                    <span>/</span>
                    <span class="text-gray-900">Buat Baru</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Buat Konsultasi & Aspirasi</h1>
            </div>

            <div class="max-w-2xl">
                <form method="POST" action="{{ route('konsultasi.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Jenis -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jenis</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="jenis" value="ADVOKASI" class="mr-3" required>
                                <div>
                                    <div class="font-medium text-gray-900">Advokasi</div>
                                    <div class="text-sm text-gray-600">Bantuan hukum atau perlindungan hak</div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="jenis" value="ASPIRASI" class="mr-3" required>
                                <div>
                                    <div class="font-medium text-gray-900">Aspirasi</div>
                                    <div class="text-sm text-gray-600">Saran atau masukan untuk SEKAR</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Kategori Advokasi -->
                    <div id="kategoriAdvokasi" class="mb-6 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Advokasi</label>
                        <select name="kategori_advokasi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoriAdvokasi as $kategori)
                                <option value="{{ $kategori }}">{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tujuan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                        <select name="tujuan" id="tujuanSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Tujuan</option>
                            @foreach($availableTargets as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tujuan Spesifik -->
                    <div id="tujuanSpesifik" class="mb-6 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Spesifik</label>
                        <input type="text" name="tujuan_spesifik" id="tujuanSpesifikInput" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nama DPW/DPD tujuan">
                    </div>

                    <!-- Judul -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Judul konsultasi/aspirasi" required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="6" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Jelaskan secara detail konsultasi atau aspirasi Anda..." required>{{ old('deskripsi') }}</textarea>
                    </div>

                    <!-- Info Card -->
                    @if($karyawan)
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-blue-900 mb-2">Informasi Pengirim</h4>
                        <div class="text-sm text-blue-700">
                            <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
                            <p><strong>NIK:</strong> {{ Auth::user()->nik }}</p>
                            <p><strong>Unit:</strong> {{ $karyawan->V_SHORT_UNIT }}</p>
                            <p><strong>Lokasi:</strong> {{ $karyawan->V_KOTA_GEDUNG }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <a href="{{ route('konsultasi.index') }}" 
                           class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-300 transition font-medium">
                            Batal
                        </a>
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisRadios = document.querySelectorAll('input[name="jenis"]');
    const kategoriAdvokasi = document.getElementById('kategoriAdvokasi');
    const tujuanSelect = document.getElementById('tujuanSelect');
    const tujuanSpesifik = document.getElementById('tujuanSpesifik');
    const tujuanSpesifikInput = document.getElementById('tujuanSpesifikInput');
    
    // Handle jenis change
    jenisRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'ADVOKASI') {
                kategoriAdvokasi.classList.remove('hidden');
                kategoriAdvokasi.querySelector('select').required = true;
            } else {
                kategoriAdvokasi.classList.add('hidden');
                kategoriAdvokasi.querySelector('select').required = false;
                kategoriAdvokasi.querySelector('select').value = '';
            }
        });
    });
    
    // Handle tujuan change
    tujuanSelect.addEventListener('change', function() {
        if (this.value && this.value !== 'GENERAL') {
            tujuanSpesifik.classList.remove('hidden');
            tujuanSpesifikInput.required = true;
            
            // Auto-fill based on selection
            const selectedOption = this.options[this.selectedIndex];
            tujuanSpesifikInput.value = selectedOption.text;
        } else {
            tujuanSpesifik.classList.add('hidden');
            tujuanSpesifikInput.required = false;
            tujuanSpesifikInput.value = '';
        }
    });
});
</script>
@endsection