<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - SEKAR')

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
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium">
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
                    <a href="{{ route('konsultasi.index') }}" class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg text-sm">
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
            <!-- Statistics Cards -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <!-- Anggota Aktif -->
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Anggota Aktif</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($anggotaAktif) }}</p>
                        </div>
                        <div class="bg-{{ str_starts_with($growthData['anggota_aktif_growth'], '+') ? 'green' : 'red' }}-100 text-{{ str_starts_with($growthData['anggota_aktif_growth'], '+') ? 'green' : 'red' }}-700 px-2 py-1 rounded text-xs font-medium">
                            {{ $growthData['anggota_aktif_growth'] }}
                        </div>
                    </div>
                </div>

                <!-- Pengurus -->
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Pengurus</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalPengurus) }}</p>
                        </div>
                        <div class="bg-{{ str_starts_with($growthData['pengurus_growth'], '+') ? 'green' : 'red' }}-100 text-{{ str_starts_with($growthData['pengurus_growth'], '+') ? 'green' : 'red' }}-700 px-2 py-1 rounded text-xs font-medium">
                            {{ $growthData['pengurus_growth'] }}
                        </div>
                    </div>
                </div>

                <!-- Anggota Keluar -->
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Anggota Keluar</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($anggotaKeluar) }}</p>
                        </div>
                        <div class="bg-{{ str_starts_with($growthData['anggota_keluar_growth'], '+') ? 'green' : 'red' }}-100 text-{{ str_starts_with($growthData['anggota_keluar_growth'], '+') ? 'green' : 'red' }}-700 px-2 py-1 rounded text-xs font-medium">
                            {{ $growthData['anggota_keluar_growth'] }}
                        </div>
                    </div>
                </div>

                <!-- Non Anggota -->
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Non Anggota</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($nonAnggota) }}</p>
                        </div>
                        <div class="bg-{{ str_starts_with($growthData['non_anggota_growth'], '+') ? 'green' : 'red' }}-100 text-{{ str_starts_with($growthData['non_anggota_growth'], '+') ? 'green' : 'red' }}-700 px-2 py-1 rounded text-xs font-medium">
                            {{ $growthData['non_anggota_growth'] }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapping DPW Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 flex-1 flex flex-col">
                <div class="p-4 border-b border-gray-200 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-900">Mapping DPW</h3>
                </div>
                
                <div class="p-4 flex-1 flex flex-col">
                    <!-- Filters -->
                    <div class="flex space-x-4 mb-4 flex-shrink-0">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">DPW :</label>
                            <select id="filterDPW" class="border border-gray-300 rounded px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="">Semua</option>
                                @foreach($mappingWithStats->pluck('dpw')->unique() as $dpw)
                                    <option value="{{ $dpw }}">{{ $dpw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">DPD :</label>
                            <input 
                                type="text" 
                                id="filterDPD"
                                placeholder="Masukkan DPD" 
                                class="border border-gray-300 rounded px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            >
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="flex-1 overflow-auto rounded-lg border border-gray-200">
                        <table class="w-full" id="mappingTable">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">No.</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">DPW</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">DPD</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">Anggota Aktif</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">Pengurus</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">Anggota Keluar</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700 text-xs">Non Anggota</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($mappingWithStats as $index => $mapping)
                                <tr class="hover:bg-gray-50 transition-colors mapping-row" 
                                    data-dpw="{{ $mapping->dpw }}" 
                                    data-dpd="{{ $mapping->dpd }}">
                                    <td class="py-3 px-4 text-xs text-gray-900">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 text-xs font-medium text-gray-900">{{ $mapping->dpw }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-900">{{ $mapping->dpd }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-900">{{ number_format($mapping->anggota_aktif) }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-900">{{ number_format($mapping->pengurus) }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-900">{{ number_format($mapping->anggota_keluar) }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-900">{{ number_format($mapping->non_anggota) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-6 px-4 text-center text-gray-500 text-xs">
                                        Tidak ada data mapping DPW
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterDPW = document.getElementById('filterDPW');
    const filterDPD = document.getElementById('filterDPD');
    const mappingRows = document.querySelectorAll('.mapping-row');
    
    function filterTable() {
        const dpwValue = filterDPW.value.toLowerCase();
        const dpdValue = filterDPD.value.toLowerCase();
        
        mappingRows.forEach(row => {
            const rowDPW = row.dataset.dpw.toLowerCase();
            const rowDPD = row.dataset.dpd.toLowerCase();
            
            const dpwMatch = !dpwValue || rowDPW.includes(dpwValue);
            const dpdMatch = !dpdValue || rowDPD.includes(dpdValue);
            
            if (dpwMatch && dpdMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update row numbers
        let visibleIndex = 1;
        mappingRows.forEach(row => {
            if (row.style.display !== 'none') {
                row.querySelector('td:first-child').textContent = visibleIndex++;
            }
        });
    }
    
    filterDPW.addEventListener('change', filterTable);
    filterDPD.addEventListener('input', filterTable);
});
</script>

<style>
/* Custom scrollbar for table */
.overflow-auto::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

@endsection