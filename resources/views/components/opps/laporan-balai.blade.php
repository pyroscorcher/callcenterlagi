@props([
    'laporans' => [],
])

<div class="max-w-6xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <span class="font-bold">Laporan Masuk Bencana</span>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
        
        {{-- Header & Search Bar --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Daftar Laporan Masuk</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola dan pantau seluruh laporan kejadian bencana dari masyarakat.
                </p>
            </div>

            {{-- Kolom Pencarian --}}
            <div class="relative w-full max-w-xs">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari Laporan...."
                    class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-[#3B39C4]"
                />
                <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
        </div>

        {{-- Tabel Daftar Laporan --}}
        @if($laporans && count($laporans) > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-[#161446] text-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-medium">Waktu Pelaporan</th>
                                <th scope="col" class="px-6 py-4 font-medium">Waktu Kejadian</th>
                                <th scope="col" class="px-6 py-4 font-medium">Jenis Bencana</th>
                                <th scope="col" class="px-6 py-4 font-medium">Nama Bencana</th>
                                <th scope="col" class="px-6 py-4 font-medium">Lokasi Bencana</th>
                                <th scope="col" class="px-6 py-4 font-medium">Detail Status</th>
                                <th scope="col" class="px-6 py-4 font-medium">Pelapor</th>
                                <th scope="col" class="px-6 py-4 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="laporanTableBody">
                            @foreach ($laporans as $index => $laporan)
                                <tr class="hover:bg-gray-50 transition-colors {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->created_at?->translatedFormat('d F Y \p\u\k\u\l H.i') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->waktu_kejadian }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->jenis_bencana }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->nama_bencana }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->lokasi }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->detail_status }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->pelapor }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Route yang sudah disesuaikan --}}
                                            <a href="{{ route('laporan-penanganan-balai.show', $laporan->id) }}"
                                               class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                                Detail
                                            </a>

                                            <form action="{{ route('laporan.destroy', $laporan->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 flex flex-col items-center justify-center text-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 font-medium">Belum ada laporan bencana yang masuk.</p>
            </div>
        @endif

        {{-- Pagination --}}
        @if (method_exists($laporans, 'links'))
            <div class="mt-6">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Script JS Live Search --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('laporanTableBody');

        if (!searchInput || !tableBody) return;

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
</script>