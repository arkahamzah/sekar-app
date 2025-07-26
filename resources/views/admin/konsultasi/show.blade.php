{{-- resources/views/admin/konsultasi/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Konsultasi #' . $konsultasi->ID)
@section('header', 'Detail Konsultasi #' . $konsultasi->ID)
@section('description', 'Kelola dan tanggapi konsultasi dari anggota SEKAR')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.konsultasi.index') }}" 
           class="flex items-center text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Konsultasi
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Konsultasi Detail Card - Same style as user -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ isset($konsultasi->JUDUL) ? $konsultasi->JUDUL : 'Detail Konsultasi' }}</h3>
                            <p class="text-gray-600 text-sm">ID: #{{ $konsultasi->ID }}</p>
                        </div>
                        <div class="flex space-x-2">
                            @php
                                $status = isset($konsultasi->STATUS) ? $konsultasi->STATUS : 'UNKNOWN';
                                $statusConfig = match($status) {
                                    'OPEN' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock', 'text' => 'Menunggu Tanggapan'],
                                    'IN_PROGRESS' => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-spinner', 'text' => 'Sedang Diproses'],
                                    'CLOSED' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle', 'text' => 'Selesai'],
                                    default => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question', 'text' => 'Unknown']
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusConfig['class'] }}">
                                <i class="fas {{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['text'] }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    <!-- Jenis & Kategori -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                            @php
                                $jenis = isset($konsultasi->JENIS) ? $konsultasi->JENIS : 'N/A';
                                $jenisClass = match($jenis) {
                                    'ADVOKASI' => 'bg-red-100 text-red-800',
                                    'ASPIRASI' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $jenisClass }}">
                                {{ $jenis }}
                            </span>
                        </div>
                        
                        @if(isset($konsultasi->KATEGORI_ADVOKASI) && $konsultasi->KATEGORI_ADVOKASI)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Advokasi</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ $konsultasi->KATEGORI_ADVOKASI }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Tujuan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ isset($konsultasi->TUJUAN) ? $konsultasi->TUJUAN : 'N/A' }}
                            </span>
                        </div>
                        
                        @if(isset($konsultasi->TUJUAN_SPESIFIK) && $konsultasi->TUJUAN_SPESIFIK)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Spesifik</label>
                            <p class="text-gray-900">{{ $konsultasi->TUJUAN_SPESIFIK }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ isset($konsultasi->DESKRIPSI) ? $konsultasi->DESKRIPSI : 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                            <p class="text-gray-900">{{ isset($konsultasi->CREATED_AT) ? date('d/m/Y H:i:s', strtotime($konsultasi->CREATED_AT)) : 'N/A' }}</p>
                        </div>
                        
                        @if(isset($konsultasi->UPDATED_AT) && $konsultasi->UPDATED_AT)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diupdate</label>
                            <p class="text-gray-900">{{ date('d/m/Y H:i:s', strtotime($konsultasi->UPDATED_AT)) }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if(isset($konsultasi->CLOSED_AT) && $konsultasi->CLOSED_AT)
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <div>
                                <p class="text-green-800 font-medium">Konsultasi Ditutup</p>
                                <p class="text-green-700 text-sm">
                                    {{ date('d/m/Y H:i:s', strtotime($konsultasi->CLOSED_AT)) }}
                                    @if(isset($konsultasi->CLOSED_BY) && $konsultasi->CLOSED_BY)
                                    oleh {{ $konsultasi->CLOSED_BY }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section - Same style as user -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-comments mr-2"></i>
                        Diskusi & Tanggapan ({{ count($komentar) }})
                    </h3>
                </div>
                
                <div class="p-6">
                    @if(count($komentar) > 0)
                    <div class="space-y-4">
                        @foreach($komentar as $comment)
                        @php
                            $isAdmin = (isset($comment->N_NIK_RESPONDER) && $comment->N_NIK_RESPONDER === Auth::user()->nik) ||
                                      str_contains(strtolower($comment->KOMENTAR), 'admin') ||
                                      str_contains(strtolower($comment->KOMENTAR), 'status diubah');
                        @endphp
                        <div class="flex space-x-4 {{ $isAdmin ? 'bg-blue-50' : 'bg-gray-50' }} p-4 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 {{ $isAdmin ? 'bg-blue-500' : 'bg-gray-500' }} rounded-full flex items-center justify-center">
                                    @if($isAdmin)
                                        <i class="fas fa-user-shield text-white"></i>
                                    @else
                                        <i class="fas fa-user text-white"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h5 class="font-medium text-gray-900">
                                            {{ isset($comment->responder_nama) ? $comment->responder_nama : (isset($comment->N_NIK_RESPONDER) ? $comment->N_NIK_RESPONDER : 'Unknown') }}
                                        </h5>
                                        @if($isAdmin)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-shield-alt mr-1"></i>Admin
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ isset($comment->CREATED_AT) ? date('d/m/Y H:i', strtotime($comment->CREATED_AT)) : 'N/A' }}
                                    </span>
                                </div>
                                <p class="text-gray-700">{{ isset($comment->KOMENTAR) ? $comment->KOMENTAR : 'No comment' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments fa-3x text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada komentar atau tanggapan</p>
                        <p class="text-gray-400 text-sm">Mulai diskusi dengan menambahkan tanggapan sebagai admin</p>
                    </div>
                    @endif
                    
                    @if($status !== 'CLOSED')
                    <!-- Add Comment Form -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-4">Tambah Tanggapan Admin</h4>
                        <form action="{{ route('admin.konsultasi.addResponse', $konsultasi->ID) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <textarea name="komentar" rows="4" 
                                          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                          placeholder="Tulis tanggapan Anda sebagai admin..." required></textarea>
                                <div class="flex justify-between items-center">
                                    <p class="text-xs text-gray-500">Tanggapan akan mengubah status menjadi "Sedang Diproses" jika masih "Menunggu"</p>
                                    <button type="submit" 
                                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-paper-plane mr-2"></i>Kirim Tanggapan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <p class="text-gray-600 text-sm">
                                <i class="fas fa-lock mr-2"></i>
                                Konsultasi ini telah ditutup. Tidak dapat menambahkan tanggapan baru.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Pengaju Info Card - Same style as user -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pengaju</h3>
                </div>
                <div class="p-6">
                    @if(isset($konsultasi->pengaju_nama))
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">{{ substr($konsultasi->pengaju_nama, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $konsultasi->pengaju_nama }}</p>
                            <p class="text-sm text-gray-600">{{ isset($konsultasi->pengaju_nik) ? $konsultasi->pengaju_nik : 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="space-y-3">
                        @if(isset($konsultasi->pengaju_email))
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium text-gray-900">{{ $konsultasi->pengaju_email }}</span>
                        </div>
                        @endif
                        
                        @if(isset($konsultasi->pengaju_lokasi))
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Lokasi:</span>
                            <span class="font-medium text-gray-900">{{ $konsultasi->pengaju_lokasi }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">NIK:</span>
                            <span class="font-medium text-gray-900">{{ isset($konsultasi->N_NIK_PENGAJU) ? $konsultasi->N_NIK_PENGAJU : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Admin</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($status !== 'CLOSED')
                    <!-- Status Change Actions -->
                    @if($status === 'OPEN')
                    <button onclick="updateStatus('IN_PROGRESS')" 
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Mulai Proses
                    </button>
                    @endif
                    
                    @if($status === 'IN_PROGRESS')
                    <button onclick="updateStatus('OPEN')" 
                            class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>Kembalikan ke Menunggu
                    </button>
                    @endif
                    
                    <button onclick="closeKonsultasi()" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Tutup Konsultasi
                    </button>
                    @endif
                    
                    <!-- Always available actions -->
                    <button onclick="printKonsultasi()" 
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>Cetak Detail
                    </button>
                    
                    @if($adminInfo && isset($adminInfo->role_name) && $adminInfo->role_name === 'ADM')
                    <button onclick="deleteKonsultasi()" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Hapus Konsultasi
                    </button>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex items-start space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="font-medium text-gray-900">Konsultasi Dibuat</p>
                                <p class="text-sm text-gray-600">{{ isset($konsultasi->CREATED_AT) ? date('d/m/Y H:i', strtotime($konsultasi->CREATED_AT)) : 'N/A' }}</p>
                            </div>
                        </div>
                        
                        @if(isset($konsultasi->UPDATED_AT) && $konsultasi->UPDATED_AT !== $konsultasi->CREATED_AT)
                        <!-- Updated -->
                        <div class="flex items-start space-x-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mt-2"></div>
                            <div>
                                <p class="font-medium text-gray-900">Terakhir Diupdate</p>
                                <p class="text-sm text-gray-600">{{ date('d/m/Y H:i', strtotime($konsultasi->UPDATED_AT)) }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($konsultasi->CLOSED_AT) && $konsultasi->CLOSED_AT)
                        <!-- Closed -->
                        <div class="flex items-start space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="font-medium text-gray-900">Konsultasi Ditutup</p>
                                <p class="text-sm text-gray-600">{{ date('d/m/Y H:i', strtotime($konsultasi->CLOSED_AT)) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Functions -->
<script>
function updateStatus(newStatus) {
    const statusText = {
        'OPEN': 'Menunggu Tanggapan',
        'IN_PROGRESS': 'Sedang Diproses',
        'CLOSED': 'Ditutup'
    };
    
    if (confirm(`Apakah Anda yakin ingin mengubah status menjadi "${statusText[newStatus]}"?`)) {
        fetch(`{{ route('admin.konsultasi.updateStatus', $konsultasi->ID) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus
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

function closeKonsultasi() {
    const catatan = prompt('Masukkan catatan penutupan (opsional):');
    
    if (catatan !== null) { // User didn't cancel
        fetch(`{{ route('admin.konsultasi.updateStatus', $konsultasi->ID) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: 'CLOSED',
                catatan: catatan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Gagal menutup konsultasi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menutup konsultasi');
        });
    }
}

function printKonsultasi() {
    window.print();
}

function deleteKonsultasi() {
    if (confirm('PERINGATAN: Apakah Anda yakin ingin menghapus konsultasi ini? Aksi ini tidak dapat dibatalkan!')) {
        fetch(`{{ route('admin.konsultasi.destroy', $konsultasi->ID) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Konsultasi berhasil dihapus');
                window.location.href = '{{ route("admin.konsultasi.index") }}';
            } else {
                alert('Gagal menghapus konsultasi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus konsultasi');
        });
    }
}
</script>

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-white { background: white !important; }
    .shadow-sm { box-shadow: none !important; }
}
</style>
@endpush
@endsection