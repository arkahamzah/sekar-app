@extends('layouts.app')

@section('title', 'Buat Konsultasi & Aspirasi - SEKAR')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Breadcrumb -->
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