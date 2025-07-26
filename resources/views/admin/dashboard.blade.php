{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Admin')
@section('description', 'Overview sistem dan statistik terkini')

@section('content')
<div class="space-y-6">
    <!-- Admin Info Card - Same style as user dashboard -->
    @if($adminInfo)
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user-shield text-white text-2xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                <p class="text-gray-600">{{ isset($adminInfo->role_desc) ? $adminInfo->role_desc : 'Administrator' }}</p>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                    <span><i class="fas fa-id-card mr-1"></i>{{ Auth::user()->nik }}</span>
                    @if(isset($adminInfo->DPW) && $adminInfo->DPW)
                        <span><i class="fas fa-building mr-1"></i>DPW: {{ $adminInfo->DPW }}</span>
                    @endif
                    @if(isset($adminInfo->DPD) && $adminInfo->DPD)
                        <span><i class="fas fa-map-marker-alt mr-1"></i>DPD: {{ $adminInfo->DPD }}</span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <i class="fas fa-shield-alt mr-1"></i>
                    {{ isset($adminInfo->role_name) ? $adminInfo->role_name : 'ADMIN' }}
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards - Same style as user dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Konsultasi -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Konsultasi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['total_konsultasi']) ? $stats['total_konsultasi'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua konsultasi</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Menunggu Tanggapan -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Menunggu Tanggapan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['konsultasi_open']) ? $stats['konsultasi_open'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Perlu ditanggapi</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Sedang Diproses -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Sedang Diproses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['konsultasi_in_progress']) ? $stats['konsultasi_in_progress'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Dalam proses</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Selesai -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['konsultasi_closed']) ? $stats['konsultasi_closed'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sudah selesai</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- User Statistics Row - Same style as user dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Anggota Aktif -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Anggota Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['anggota_aktif']) ? $stats['anggota_aktif'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Anggota terdaftar</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Pengurus -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Pengurus</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['total_pengurus']) ? $stats['total_pengurus'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total pengurus aktif</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-cog text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Karyawan -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['total_karyawan']) ? $stats['total_karyawan'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Karyawan (non-GPTP)</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tie text-cyan-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Hari Ini -->
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Konsultasi Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['today_konsultasi']) ? $stats['today_konsultasi'] : 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Konsultasi baru</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Konsultasi - Same style as user dashboard -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Konsultasi Terbaru</h3>
                @if(Route::has('admin.konsultasi.index'))
                <a href="{{ route('admin.konsultasi.index') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @endif
            </div>
        </div>
        
        <div class="overflow-x-auto">
            @if($recentKonsultasi && $recentKonsultasi->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">ID</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Pengaju</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Jenis</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Judul</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Tanggal</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentKonsultasi as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-xs font-medium text-gray-900">
                                #{{ $item->ID }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div class="text-gray-900 font-medium">{{ isset($item->pengaju_nama) ? $item->pengaju_nama : 'N/A' }}</div>
                                <div class="text-gray-500">{{ isset($item->pengaju_nik) ? $item->pengaju_nik : 'N/A' }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    {{ isset($item->JENIS) ? $item->JENIS : 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-900">
                                {{ isset($item->JUDUL) ? Str::limit($item->JUDUL, 50) : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @php
                                    $status = isset($item->STATUS) ? $item->STATUS : 'UNKNOWN';
                                    $statusClass = match($status) {
                                        'OPEN' => 'bg-yellow-100 text-yellow-800',
                                        'IN_PROGRESS' => 'bg-blue-100 text-blue-800',
                                        'CLOSED' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $statusText = match($status) {
                                        'OPEN' => 'Menunggu',
                                        'IN_PROGRESS' => 'Diproses',
                                        'CLOSED' => 'Selesai',
                                        default => 'Unknown'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-500">
                                {{ isset($item->CREATED_AT) ? date('d/m/Y H:i', strtotime($item->CREATED_AT)) : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if(Route::has('admin.konsultasi.show'))
                                <a href="{{ route('admin.konsultasi.show', $item->ID) }}" 
                                   class="text-blue-600 hover:text-blue-900 font-medium">
                                    Detail
                                </a>
                                @else
                                <span class="text-gray-400">Detail</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-6 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>Belum ada konsultasi terbaru</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions - Same style as user dashboard -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(Route::has('admin.konsultasi.index'))
            <a href="{{ route('admin.konsultasi.index') }}" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-comments text-blue-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Kelola Konsultasi</p>
                    <p class="text-sm text-gray-500">Lihat dan tanggapi konsultasi</p>
                </div>
            </a>
            @endif

            @if(Route::has('setting.index'))
            <a href="{{ route('setting.index') }}" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-cog text-green-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Pengaturan</p>
                    <p class="text-sm text-gray-500">Konfigurasi sistem</p>
                </div>
            </a>
            @endif

            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors cursor-pointer"
                 onclick="refreshDashboard()">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-sync-alt text-purple-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Refresh Data</p>
                    <p class="text-sm text-gray-500">Perbarui statistik</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshDashboard() {
    window.location.reload();
}
</script>
@endsection