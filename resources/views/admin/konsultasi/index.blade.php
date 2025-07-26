{{-- resources/views/admin/konsultasi/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelola Konsultasi')
@section('header', 'Kelola Konsultasi & Aspirasi')
@section('description', 'Manage dan tanggapi konsultasi dari anggota SEKAR')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards - Same style as dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Konsultasi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(isset($stats['total']) ? $stats['total'] : 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Menunggu Tanggapan</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format(isset($stats['open']) ? $stats['open'] : 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Sedang Diproses</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format(isset($stats['in_progress']) ? $stats['in_progress'] : 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Selesai</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format(isset($stats['closed']) ? $stats['closed'] : 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search - Same style as user -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.konsultasi.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           placeholder="Cari judul, deskripsi, atau nama..."
                           value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Menunggu Tanggapan</option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <!-- Jenis Filter -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="ADVOKASI" {{ request('jenis') === 'ADVOKASI' ? 'selected' : '' }}>Advokasi</option>
                        <option value="ASPIRASI" {{ request('jenis') === 'ASPIRASI' ? 'selected' : '' }}>Aspirasi</option>
                    </select>
                </div>

                <!-- Tujuan Filter -->
                <div>
                    <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                    <select name="tujuan" id="tujuan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tujuan</option>
                        <option value="DPD" {{ request('tujuan') === 'DPD' ? 'selected' : '' }}>DPD</option>
                        <option value="DPW" {{ request('tujuan') === 'DPW' ? 'selected' : '' }}>DPW</option>
                        <option value="DPP" {{ request('tujuan') === 'DPP' ? 'selected' : '' }}>DPP</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.konsultasi.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                </div>
                
                <div class="flex gap-2">
                    <button type="button" onclick="refreshData()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <button type="button" onclick="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Filter Buttons - Same style as user -->
    <div class="flex items-center justify-between">
        <div class="flex gap-2">
            <a href="{{ route('admin.konsultasi.index') }}" 
               class="px-4 py-2 text-sm rounded-lg {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.konsultasi.index') }}?status=OPEN" 
               class="px-4 py-2 text-sm rounded-lg {{ request('status') === 'OPEN' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.konsultasi.index') }}?status=IN_PROGRESS" 
               class="px-4 py-2 text-sm rounded-lg {{ request('status') === 'IN_PROGRESS' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Diproses
            </a>
            <a href="{{ route('admin.konsultasi.index') }}?status=CLOSED" 
               class="px-4 py-2 text-sm rounded-lg {{ request('status') === 'CLOSED' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Selesai
            </a>
        </div>

        @if($adminInfo && isset($adminInfo->role_name))
        <div class="text-sm text-gray-600">
            <i class="fas fa-shield-alt mr-1"></i>
            Access Level: <span class="font-medium text-red-600">{{ $adminInfo->role_name }}</span>
        </div>
        @endif
    </div>
    
    <!-- Konsultasi Table - Same style as user tables -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        @if($konsultasi && $konsultasi->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">ID</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Pengaju</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Jenis</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Judul</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Tujuan</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Komentar</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Tanggal</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs border-b uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($konsultasi as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-xs font-medium text-gray-900">
                            #{{ $item->ID }}
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 text-xs font-semibold">
                                        {{ isset($item->pengaju_nama) ? substr($item->pengaju_nama, 0, 2) : 'N/A' }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ isset($item->pengaju_nama) ? $item->pengaju_nama : 'N/A' }}</div>
                                    <div class="text-gray-500">{{ isset($item->pengaju_nik) ? $item->pengaju_nik : 'N/A' }}</div>
                                    @if(isset($item->pengaju_lokasi))
                                    <div class="text-gray-400 text-xs">{{ $item->pengaju_lokasi }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            @php
                                $jenis = isset($item->JENIS) ? $item->JENIS : 'N/A';
                                $jenisClass = match($jenis) {
                                    'ADVOKASI' => 'bg-red-100 text-red-800',
                                    'ASPIRASI' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $jenisClass }}">
                                {{ $jenis }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <div class="max-w-xs">
                                <div class="font-medium text-gray-900 truncate">
                                    {{ isset($item->JUDUL) ? $item->JUDUL : 'N/A' }}
                                </div>
                                @if(isset($item->KATEGORI_ADVOKASI) && $item->KATEGORI_ADVOKASI)
                                <div class="text-gray-500 text-xs">{{ $item->KATEGORI_ADVOKASI }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ isset($item->TUJUAN) ? $item->TUJUAN : 'N/A' }}
                                </span>
                                @if(isset($item->TUJUAN_SPESIFIK))
                                <div class="text-gray-500 text-xs mt-1">{{ $item->TUJUAN_SPESIFIK }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            @php
                                $status = isset($item->STATUS) ? $item->STATUS : 'UNKNOWN';
                                $statusClass = match($status) {
                                    'OPEN' => 'bg-yellow-100 text-yellow-800',
                                    'IN_PROGRESS' => 'bg-purple-100 text-purple-800',
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
                            @php
                                $totalKomentar = isset($item->total_komentar) ? $item->total_komentar : 0;
                            @endphp
                            <div class="flex items-center">
                                <i class="fas fa-comments mr-1 text-gray-400"></i>
                                {{ $totalKomentar }}
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">
                            {{ isset($item->CREATED_AT) ? date('d/m/Y H:i', strtotime($item->CREATED_AT)) : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <div class="flex items-center space-x-2">
                                @if(Route::has('admin.konsultasi.show'))
                                <a href="{{ route('admin.konsultasi.show', $item->ID) }}" 
                                   class="text-blue-600 hover:text-blue-900 font-medium">
                                    Detail
                                </a>
                                @endif
                                
                                @if($status !== 'CLOSED')
                                <button onclick="updateStatus({{ $item->ID }}, 'CLOSED')" 
                                        class="text-green-600 hover:text-green-900 font-medium">
                                    Tutup
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination - Same style as user -->
        @if(isset($pagination) && $pagination['total'] > $pagination['per_page'])
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex justify-between flex-1 sm:hidden">
                    @if($pagination['has_previous_pages'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                       class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Previous
                    </a>
                    @endif
                    @if($pagination['has_more_pages'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                       class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Next
                    </a>
                    @endif
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing 
                            <span class="font-medium">{{ ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 }}</span>
                            to 
                            <span class="font-medium">{{ min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) }}</span>
                            of 
                            <span class="font-medium">{{ $pagination['total'] }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            @if($pagination['has_previous_pages'])
                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            @endif
                            
                            @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium {{ $i === $pagination['current_page'] ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                            @endfor
                            
                            @if($pagination['has_more_pages'])
                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="p-6 text-center">
            <div class="text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p class="text-lg font-medium">Tidak ada konsultasi ditemukan</p>
                <p class="text-sm">Belum ada konsultasi yang sesuai dengan filter yang dipilih</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- JavaScript Functions -->
<script>
function refreshData() {
    window.location.reload();
}

function exportData() {
    // Implement export functionality
    alert('Fitur export akan segera tersedia');
}

function updateStatus(id, status) {
    if (confirm('Apakah Anda yakin ingin mengubah status konsultasi ini?')) {
        fetch(`/admin/konsultasi/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Gagal mengubah status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
}
</script>
@endsection