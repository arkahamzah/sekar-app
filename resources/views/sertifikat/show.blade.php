@extends('layouts.app')

@section('title', 'Sertifikat Anggota - SEKAR')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('profile.index') }}" class="hover:text-blue-600">Profile</a>
                <span>/</span>
                <span class="text-gray-900">Sertifikat Anggota</span>
            </div>
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Sertifikat Keanggotaan SEKAR</h1>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak
                    </button>
                </div>
            </div>
        </div>

        @if(!$isSignaturePeriodActive)
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-800">Sertifikat Belum Berlaku</p>
                    <p class="text-xs text-yellow-700">
                        Sertifikat akan berlaku pada periode:
                        @if($periode['start'] && $periode['end'])
                            {{ \Carbon\Carbon::parse($periode['start'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode['end'])->format('d M Y') }}
                        @else
                            Belum ditentukan
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Certificate Card -->
        <div class="bg-white rounded-lg shadow-lg border-2 border-blue-200 print:shadow-none print:border-gray-400" id="certificate">
            <!-- Header with Logo -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('asset/logo-tabs.png') }}" alt="SEKAR Logo" class="h-16 w-16 bg-white rounded-full p-2">
                        <div>
                            <h2 class="text-2xl font-bold">SERIKAT KARYAWAN TELKOM</h2>
                            <p class="text-blue-100">SERTIFIKAT KEANGGOTAAN</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-blue-100">No. Sertifikat</p>
                        <p class="font-mono text-lg">SEKAR/{{ $joinDate->format('Y/m') }}/{{ $user->nik }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-8">
                <div class="text-center mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">SERTIFIKAT KEANGGOTAAN</h3>
                    <p class="text-gray-600">Diberikan kepada:</p>
                </div>

                <!-- Member Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- Photo Placeholder -->
                    <div class="flex justify-center">
                        <div class="w-32 h-40 bg-gray-200 rounded-lg flex items-center justify-center border-2 border-gray-300">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <p class="text-xs text-gray-500">Foto Anggota</p>
                            </div>
                        </div>
                    </div>

                    <!-- Member Details -->
                    <div class="md:col-span-2 space-y-4">
                        <div class="text-center md:text-left">
                            <h4 class="text-2xl font-bold text-gray-900 mb-1">{{ strtoupper($user->name) }}</h4>
                            <p class="text-lg text-gray-600">NIK: {{ $user->nik }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600 font-medium">Jabatan:</p>
                                <p class="text-gray-900">{{ $karyawan->V_SHORT_POSISI }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Unit Kerja:</p>
                                <p class="text-gray-900">{{ $karyawan->V_SHORT_UNIT }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Divisi:</p>
                                <p class="text-gray-900">{{ $karyawan->V_SHORT_DIVISI }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Lokasi Kerja:</p>
                                <p class="text-gray-900">{{ $karyawan->V_KOTA_GEDUNG }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Tanggal Bergabung:</p>
                                <p class="text-gray-900">{{ $joinDate->format('d F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Status:</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 font-medium">
                                    Anggota Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificate Text -->
                <div class="text-center mb-8 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700 leading-relaxed">
                        Dengan ini menyatakan bahwa yang bersangkutan telah terdaftar sebagai
                        <strong>Anggota Serikat Karyawan Telkom (SEKAR)</strong>
                        sejak tanggal <strong>{{ $joinDate->format('d F Y') }}</strong>
                        dan memiliki hak serta kewajiban sesuai dengan Anggaran Dasar dan Anggaran Rumah Tangga SEKAR.
                    </p>
                </div>

                <!-- Signatures -->
                @if($isSignaturePeriodActive && ($signatures['sekjen'] || $signatures['waketum']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                    <!-- Sekjen Signature -->
                    @if($signatures['sekjen'])
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Sekretaris Jenderal</p>
                        <div class="h-20 mb-2 flex items-center justify-center">
                            <img src="{{ $signatures['sekjen'] }}" alt="Tanda Tangan Sekjen" class="max-h-16 max-w-32">
                        </div>
                        <div class="border-t border-gray-300 pt-2">
                            <p class="text-sm font-medium text-gray-900">( _____________________ )</p>
                        </div>
                    </div>
                    @endif

                    <!-- Waketum Signature -->
                    @if($signatures['waketum'])
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Wakil Ketua Umum</p>
                        <div class="h-20 mb-2 flex items-center justify-center">
                            <img src="{{ $signatures['waketum'] }}" alt="Tanda Tangan Waketum" class="max-h-16 max-w-32">
                        </div>
                        <div class="border-t border-gray-300 pt-2">
                            <p class="text-sm font-medium text-gray-900">( _____________________ )</p>
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                    <!-- Placeholder Signatures -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Sekretaris Jenderal</p>
                        <div class="h-20 mb-2 flex items-center justify-center">
                            <div class="w-32 h-16 border-2 border-dashed border-gray-300 rounded flex items-center justify-center">
                                <span class="text-xs text-gray-400">Tanda Tangan</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-300 pt-2">
                            <p class="text-sm font-medium text-gray-900">( _____________________ )</p>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Wakil Ketua Umum</p>
                        <div class="h-20 mb-2 flex items-center justify-center">
                            <div class="w-32 h-16 border-2 border-dashed border-gray-300 rounded flex items-center justify-center">
                                <span class="text-xs text-gray-400">Tanda Tangan</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-300 pt-2">
                            <p class="text-sm font-medium text-gray-900">( _____________________ )</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Footer -->
                <div class="text-center mt-8 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        Sertifikat ini diterbitkan secara digital oleh Sistem Informasi SEKAR<br>
                        Tanggal cetak: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        margin: 0;
        font-size: 12px;
    }

    #certificate {
        box-shadow: none !important;
        border: 1px solid #000 !important;
        margin: 0;
        page-break-inside: avoid;
    }

    .bg-gradient-to-r {
        background: #1d4ed8 !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}
</style>

@endsection