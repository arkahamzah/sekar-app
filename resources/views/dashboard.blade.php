@extends('layouts.app')

@section('title', 'Dashboard - SEKAR')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Main Content -->
    <div class="p-6">
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