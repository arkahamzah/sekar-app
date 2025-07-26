@extends('layouts.admin')

@section('title', 'Detail Konsultasi #' . $konsultasiData->ID)
@section('header', 'Detail Konsultasi #' . $konsultasiData->ID)
@section('description', 'Kelola dan tanggapi konsultasi dari anggota')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.konsultasi.index') }}" 
           class="flex items-center text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Konsultasi
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Konsultasi Detail Card -->
            <div class="admin-card rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Informasi Konsultasi</h3>
                            <p class="text-gray-600 text-sm">ID: #{{ $konsultasiData->ID }}</p>
                        </div>
                        <div class="flex space-x-2">
                            @switch($konsultasiData->STATUS)
                                @case('OPEN')
                                    <span class="status-badge bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Menunggu Tanggapan
                                    </span>
                                    @break
                                @case('IN_PROGRESS')
                                    <span class="status-badge bg-purple-100 text-purple-800">
                                        <i class="fas fa-spinner mr-1"></i>Sedang Diproses
                                    </span>
                                    @break
                                @case('CLOSED')
                                    <span class="status-badge bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Selesai
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis</label>
                            <div class="flex items-center">
                                @if($konsultasiData->JENIS === 'ADVOKASI')
                                    <span class="status-badge bg-red-100 text-red-800">{{ $konsultasiData->JENIS }}</span>
                                @else
                                    <span class="status-badge bg-blue-100 text-blue-800">{{ $konsultasiData->JENIS }}</span>
                                @endif
                                @if($konsultasiData->KATEGORI_ADVOKASI)
                                <span class="ml-2 text-sm text-gray-600">{{ $konsultasiData->KATEGORI_ADVOKASI }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan</label>
                            <div>
                                <span class="status-badge bg-gray-100 text-gray-800">{{ $konsultasiData->TUJUAN }}</span>
                                @if($konsultasiData->TUJUAN_SPESIFIK)
                                <div class="text-sm text-gray-600 mt-1">{{ $konsultasiData->TUJUAN_SPESIFIK }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Judul</label>
                        <h4 class="text-lg font-medium text-gray-900">{{ $konsultasiData->JUDUL }}</h4>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700">{{ $konsultasiData->DESKRIPSI }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($konsultasiData->CREATED_AT)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        
                        @if($konsultasiData->UPDATED_AT && $konsultasiData->UPDATED_AT != $konsultasiData->CREATED_AT)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diperbarui</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($konsultasiData->UPDATED_AT)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if($konsultasiData->CLOSED_AT)
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <div>
                                <p class="text-green-800 font-medium">Konsultasi Ditutup</p>
                                <p class="text-green-700 text-sm">
                                    {{ \Carbon\Carbon::parse($konsultasiData->CLOSED_AT)->format('d/m/Y H:i:s') }}
                                    @if($konsultasiData->CLOSED_BY)
                                    oleh {{ $konsultasiData->CLOSED_BY }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="admin-card rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-comments mr-2"></i>
                        Diskusi & Tanggapan ({{ count($comments) }})
                    </h3>
                </div>
                
                <div class="p-6">
                    @if(count($comments) > 0)
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                        <div class="flex space-x-4 {{ $comment->PENGIRIM_ROLE === 'ADMIN' ? 'bg-blue-50' : 'bg-gray-50' }} p-4 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 {{ $comment->PENGIRIM_ROLE === 'ADMIN' ? 'bg-blue-500' : 'bg-gray-500' }} rounded-full flex items-center justify-center">
                                    @if($comment->PENGIRIM_ROLE === 'ADMIN')
                                        <i class="fas fa-user-shield text-white"></i>
                                    @else
                                        <i class="fas fa-user text-white"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h5 class="font-medium text-gray-900">{{ $comment->nama_pengirim ?? $comment->N_NIK }}</h5>
                                        @if($comment->PENGIRIM_ROLE === 'ADMIN')
                                        <span class="status-badge bg-blue-100 text-blue-800">Admin</span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($comment->CREATED_AT)->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-gray-700">{{ $comment->KOMENTAR }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments fa-3x text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada komentar atau tanggapan</p>
                    </div>
                    @endif
                    
                    @if($konsultasiData->STATUS !== 'CLOSED')
                    <!-- Add Comment Form -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-4">Tambah Tanggapan Admin</h4>
                        <form action="{{ route('admin.konsultasi.comment', $konsultasiData->ID) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <textarea name="komentar" rows="4" 
                                          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                                          placeholder="Tulis tanggapan Anda sebagai admin..." required></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500">
                                        <i class="fas fa-paper-plane mr-2"></i>Kirim Tanggapan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Info Card -->
            <div class="admin-card rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Pengaju</h3>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-purple-600 text-xl font-bold">
                                {{ substr($konsultasiData->nama_pengaju ?? 'U', 0, 1) }}
                            </span>
                        </div>
                        <h4 class="font-semibold text-gray-900">{{ $konsultasiData->nama_pengaju ?? 'Unknown' }}</h4>
                        <p class="text-gray-600 text-sm">{{ $konsultasiData->N_NIK }}</p>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        @if($konsultasiData->jabatan_pengaju)
                        <div>
                            <span class="text-gray-500">Jabatan:</span>
                            <p class="font-medium">{{ $konsultasiData->jabatan_pengaju }}</p>
                        </div>
                        @endif
                        
                        @if($konsultasiData->unit_pengaju)
                        <div>
                            <span class="text-gray-500">Unit Kerja:</span>
                            <p class="font-medium">{{ $konsultasiData->unit_pengaju }}</p>
                        </div>
                        @endif
                        
                        @if($konsultasiData->lokasi_pengaju)
                        <div>
                            <span class="text-gray-500">Lokasi:</span>
                            <p class="font-medium">{{ $konsultasiData->lokasi_pengaju }}</p>
                        </div>
                        @endif
                        
                        @if($konsultasiData->no_hp_pengaju)
                        <div>
                            <span class="text-gray-500">No. HP:</span>
                            <p class="font-medium">{{ $konsultasiData->no_hp_pengaju }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            @if($konsultasiData->STATUS !== 'CLOSED')
            <div class="admin-card rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Tindakan Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="showCloseModal()" 
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-check mr-2"></i>Tutup Konsultasi
                    </button>
                    
                    <button onclick="showEscalateModal()" 
                            class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500">
                        <i class="fas fa-arrow-up mr-2"></i>Eskalasi ke Level Atas
                    </button>
                </div>
            </div>
            @endif

            <!-- Statistics Card -->
            <div class="admin-card rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Statistik Konsultasi</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-purple-600">{{ count($comments) }}</div>
                            <div class="text-sm text-gray-500">Total Komentar</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ count(array_filter($comments, fn($c) => $c->PENGIRIM_ROLE === 'ADMIN')) }}
                            </div>
                            <div class="text-sm text-gray-500">Tanggapan Admin</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                        <div class="text-sm text-gray-500 mb-1">Durasi Konsultasi</div>
                        <div class="font-medium text-gray-900">
                            @if($konsultasiData->STATUS === 'CLOSED' && $konsultasiData->CLOSED_AT)
                                {{ \Carbon\Carbon::parse($konsultasiData->CREATED_AT)->diffInDays(\Carbon\Carbon::parse($konsultasiData->CLOSED_AT)) }} hari
                            @else
                                {{ \Carbon\Carbon::parse($konsultasiData->CREATED_AT)->diffInDays(now()) }} hari (berlangsung)
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Close Modal -->
<div id="closeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tutup Konsultasi</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 mt-0.5"></i>
                        <p class="text-yellow-800 text-sm">
                            Setelah ditutup, konsultasi tidak dapat dibuka kembali dan user tidak dapat menambahkan komentar.
                        </p>
                    </div>
                </div>
                <form action="{{ route('admin.konsultasi.close', $konsultasiData->ID) }}" method="POST">
                    @csrf
                    <textarea name="closing_note" rows="4" 
                              class="w-full border border-gray-300 rounded-lg p-3 mb-4" 
                              placeholder="Tambahkan ringkasan penyelesaian atau catatan penutupan..."></textarea>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideCloseModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i>Tutup Konsultasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Escalate Modal -->
<div id="escalateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Eskalasi Konsultasi</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mr-3 mt-0.5"></i>
                        <p class="text-blue-800 text-sm">
                            Eskalasi akan memindahkan konsultasi ke level yang lebih tinggi untuk penanganan lebih lanjut.
                        </p>
                    </div>
                </div>
                <form action="{{ route('admin.konsultasi.escalate', $konsultasiData->ID) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Eskalasi Ke</label>
                            <select name="escalate_to" class="w-full border border-gray-300 rounded-lg p-3" required>
                                <option value="">Pilih Level Eskalasi</option>
                                <option value="DPW">DPW (Dewan Pengurus Wilayah)</option>
                                <option value="DPP">DPP (Dewan Pengurus Pusat)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Eskalasi</label>
                            <textarea name="escalation_note" rows="4" 
                                      class="w-full border border-gray-300 rounded-lg p-3" 
                                      placeholder="Jelaskan mengapa konsultasi ini perlu dieskalasi ke level yang lebih tinggi..." required></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="hideEscalateModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-arrow-up mr-2"></i>Eskalasi
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
function showCloseModal() {
    document.getElementById('closeModal').classList.remove('hidden');
}

function hideCloseModal() {
    document.getElementById('closeModal').classList.add('hidden');
}

function showEscalateModal() {
    document.getElementById('escalateModal').classList.remove('hidden');
}

function hideEscalateModal() {
    document.getElementById('escalateModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('closeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCloseModal();
    }
});

document.getElementById('escalateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideEscalateModal();
    }
});
</script>
@endpush