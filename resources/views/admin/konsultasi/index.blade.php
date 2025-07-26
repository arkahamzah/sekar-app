@extends('layouts.admin')

@section('title', 'Manajemen Konsultasi')
@section('header', 'Manajemen Konsultasi & Aspirasi')
@section('description', 'Kelola semua pengajuan advokasi dan aspirasi anggota')

@section('content')
<div class="space-y-6">
    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="admin-card rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-comments text-blue-600"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total</p>
                    <p class="text-xl font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Menunggu</p>
                    <p class="text-xl font-bold">{{ $stats['open'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-spinner text-purple-600"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Diproses</p>
                    <p class="text-xl font-bold">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Selesai</p>
                    <p class="text-xl font-bold">{{ $stats['closed'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters and Actions -->
    <div class="admin-card rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.konsultasi.index') }}" 
                   class="px-4 py-2 text-sm rounded-lg {{ !request('status') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
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
            
            <div class="flex gap-2">
                <button onclick="refreshData()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button onclick="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </div>
    </div>
    
    <!-- Konsultasi Table -->
    <div class="admin-card rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($konsultasiList as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $item->ID }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-purple-600 text-xs font-semibold">
                                        {{ substr($item->nama_pengaju ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->nama_pengaju ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->N_NIK }}</div>
                                    @if($item->lokasi_pengaju)
                                    <div class="text-xs text-gray-400">{{ $item->lokasi_pengaju }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->JENIS === 'ADVOKASI')
                                <span class="status-badge bg-red-100 text-red-800">{{ $item->JENIS }}</span>
                                @if($item->KATEGORI_ADVOKASI)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($item->KATEGORI_ADVOKASI, 20) }}</div>
                                @endif
                            @else
                                <span class="status-badge bg-blue-100 text-blue-800">{{ $item->JENIS }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($item->JUDUL, 40) }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($item->DESKRIPSI, 60) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge bg-gray-100 text-gray-800">{{ $item->TUJUAN }}</span>
                            @if($item->TUJUAN_SPESIFIK)
                            <div class="text-xs text-gray-500 mt-1">{{ $item->TUJUAN_SPESIFIK }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($item->STATUS)
                                @case('OPEN')
                                    <span class="status-badge bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Menunggu
                                    </span>
                                    @break
                                @case('IN_PROGRESS')
                                    <span class="status-badge bg-purple-100 text-purple-800">
                                        <i class="fas fa-spinner mr-1"></i>Diproses
                                    </span>
                                    @break
                                @case('CLOSED')
                                    <span class="status-badge bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Selesai
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="status-badge bg-gray-100 text-gray-800">
                                <i class="fas fa-comments mr-1"></i>{{ $item->comment_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($item->CREATED_AT)->format('d/m/Y') }}
                            <div class="text-xs">{{ \Carbon\Carbon::parse($item->CREATED_AT)->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.konsultasi.show', $item->ID) }}" 
                                   class="text-purple-600 hover:text-purple-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($item->STATUS !== 'CLOSED')
                                <button onclick="quickClose({{ $item->ID }})" 
                                        class="text-green-600 hover:text-green-900" title="Tutup">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-comments fa-3x text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada konsultasi</h3>
                                <p class="text-gray-500">Belum ada pengajuan konsultasi atau aspirasi{{ request('status') ? ' dengan status tersebut' : '' }}.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(count($konsultasiList) > 0)
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Menampilkan {{ count($konsultasiList) }} dari {{ $totalKonsultasi }} konsultasi
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Close Modal -->
<div id="quickCloseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tutup Konsultasi</h3>
                <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menutup konsultasi ini?</p>
                <form id="quickCloseForm">
                    <textarea id="closingNote" name="closing_note" rows="3" 
                              class="w-full border border-gray-300 rounded-lg p-3 mb-4" 
                              placeholder="Catatan penutupan (opsional)..."></textarea>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Tutup Konsultasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentKonsultasiId = null;

function refreshData() {
    window.location.reload();
}

function exportData() {
    // Implement export functionality
    alert('Export functionality will be implemented');
}

function quickClose(id) {
    currentKonsultasiId = id;
    document.getElementById('quickCloseModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('quickCloseModal').classList.add('hidden');
    currentKonsultasiId = null;
    document.getElementById('closingNote').value = '';
}

document.getElementById('quickCloseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentKonsultasiId) return;
    
    const closingNote = document.getElementById('closingNote').value;
    
    // Create form data
    const formData = new FormData();
    formData.append('closing_note', closingNote);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Submit to server
    fetch(`/admin/konsultasi/${currentKonsultasiId}/close`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert('Konsultasi berhasil ditutup');
            closeModal();
            refreshData();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});
</script>
@endpush