@extends('layouts.app')

@section('title', 'Profile Sekar - SEKAR')

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
                    <a href="{{ route('profile.index') }}" class="flex items-center px-3 py-2.5 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                        </svg>
                        Profile Sekar
                    </a>
                    <a href="#" class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg text-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Advokasi dan Aspirasi
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- ID Card / Kartu Anggota -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <h3 class="text-white font-semibold text-lg">Kartu Anggota</h3>
                        </div>
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 text-lg">{{ $user->name }}</h4>
                            <p class="text-gray-600 text-sm">NIK: {{ $user->nik }}</p>
                            @if($karyawan)
                                <p class="text-gray-600 text-sm">{{ $karyawan->V_SHORT_POSISI }}</p>
                                <p class="text-gray-600 text-xs mt-1">{{ $karyawan->V_SHORT_DIVISI }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Anggota</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <p class="text-gray-900">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                                    <p class="text-gray-900">{{ $user->nik }}</p>
                                </div>
                                @if($karyawan)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                        <p class="text-gray-900">{{ $karyawan->V_SHORT_POSISI }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
                                        <p class="text-gray-900">{{ $karyawan->V_SHORT_UNIT }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                                        <p class="text-gray-900">{{ $karyawan->V_SHORT_DIVISI }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kerja</label>
                                        <p class="text-gray-900">{{ $karyawan->V_KOTA_GEDUNG }}</p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                                    <p class="text-gray-900">{{ $joinDate->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Iuran Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Iuran</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-blue-700 mb-1">Iuran Wajib</label>
                                    <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($iuranWajib, 0, ',', '.') }}</p>
                                    <p class="text-xs text-blue-600">/bulan</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-green-700 mb-1">Iuran Sukarela</label>
                                    <p class="text-2xl font-bold text-green-900">Rp {{ number_format($iuranSukarela, 0, ',', '.') }}</p>
                                    <p class="text-xs text-green-600">/bulan</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total per Bulan</label>
                                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalIuranPerBulan, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-600">wajib + sukarela</p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Iuran Terbayar</label>
                                <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($totalIuran, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-600">Sejak bergabung {{ $joinDate->format('F Y') }}</p>
                            </div>

                            <!-- Edit Iuran Sukarela -->
                            <div class="border-t pt-4">
                                <button id="editIuranBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                                    Edit Iuran Sukarela
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Edit Iuran Sukarela -->
<div id="editIuranModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Iuran Sukarela</h3>
        
        <form method="POST" action="{{ route('profile.update-iuran') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal Iuran Sukarela</label>
                <input 
                    type="number" 
                    name="iuran_sukarela" 
                    value="{{ $iuranSukarela }}"
                    min="0"
                    step="5000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="0"
                >
                <p class="text-xs text-gray-500 mt-1">Dalam kelipatan Rp 5.000</p>
            </div>
            
            <div class="bg-yellow-50 p-3 rounded-lg mb-4">
                <p class="text-xs text-yellow-800">
                    <strong>Catatan:</strong> Perubahan iuran sukarela akan diproses dalam 1 bulan dan terimplementasi dalam 2 bulan sesuai kebijakan HC.
                </p>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" id="cancelEditBtn" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editIuranBtn');
    const modal = document.getElementById('editIuranModal');
    const cancelBtn = document.getElementById('cancelEditBtn');
    
    editBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });
    
    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection