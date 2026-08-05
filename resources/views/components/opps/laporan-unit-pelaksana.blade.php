@props([
    'bencanaTerkini' => null,
    'laporanMasyarakat' => null,
])

<div class="bg-[#161446] max-w-6xl mx-auto px-8 py-8 min-w-0">

    {{-- Main Container Card --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        
        {{-- Header Area: Title & Actions (Search, Filter, Export) --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between mb-8 gap-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Laporan Bencana Unit Pelaksana</h1>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Search Input --}}
                <div class="relative w-full sm:w-auto min-w-[250px]">
                    <input type="text" id="searchInput" placeholder="Cari Laporan...."
                           class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-[#161446] focus:bg-white transition-colors" />
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>

                {{-- Filter Button --}}
                <button type="button" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white text-gray-700 px-4 py-2.5 text-sm font-medium hover:bg-gray-50 transition-colors">
                    Filter
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4" />
                    </svg>
                </button>

                {{-- Export Button --}}
                <a href="#" class="flex items-center gap-2 rounded-lg bg-[#161446] text-white px-5 py-2.5 text-sm font-medium hover:bg-[#110e36] transition-colors shadow-sm">
                    Export
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="flex items-center gap-6 border-b border-gray-200 mb-6">
            <button type="button" data-tab="bencana-terkini"
                    class="tab-btn pb-3 text-sm font-semibold border-b-2 border-[#161446] text-[#161446] transition-colors">
                Bencana Terkini
            </button>
            <button type="button" data-tab="laporan-masyarakat"
                    class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                Laporan Terverifikasi
            </button>
        </div>

        {{-- Tab Content: Bencana Terkini --}}
        <div id="tab-bencana-terkini" class="tab-panel w-full">
            @if($bencanaTerkini && $bencanaTerkini->count() > 0)
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-[#161446] text-white">
                            <tr>
                                <th class="px-6 py-4 font-medium">Waktu Pelaporan</th>
                                <th class="px-6 py-4 font-medium">Waktu Kejadian</th>
                                <th class="px-6 py-4 font-medium">Jenis Bencana</th>
                                <th class="px-6 py-4 font-medium">Lokasi Bencana</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($bencanaTerkini as $laporan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->created_at?->translatedFormat('d F Y H:i') }}</td>
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->waktu_kejadian }}</td>
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->jenis_bencana }}</td>
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->lokasi }}</td>
                                    <td class="px-6 py-4 text-gray-800">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                            {{ $laporan->status ?? 'Menunggu' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('laporan.show', $laporan->id) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $bencanaTerkini->links() }}</div>
            @else
                <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-10 flex flex-col items-center justify-center text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <p class="text-gray-500 font-medium">Belum ada laporan terkini yang sedang ditangani dan terverifikasi.</p>
                </div>
            @endif
        </div>

        {{-- Tab Content: Laporan Masyarakat --}}
        <div id="tab-laporan-masyarakat" class="tab-panel w-full hidden">
            @if($laporanMasyarakat && $laporanMasyarakat->count() > 0)
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-[#161446] text-white">
                            <tr>
                                <th class="px-6 py-4 font-medium">Waktu Pelaporan</th>
                                <th class="px-6 py-4 font-medium">Waktu Kejadian</th>
                                <th class="px-6 py-4 font-medium">Jenis Bencana</th>
                                <th class="px-6 py-4 font-medium">Lokasi Bencana</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($laporanMasyarakat as $laporan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->created_at?->translatedFormat('d F Y H:i') }}</td>
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->waktu_kejadian }}</td>
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->jenis_bencana }}</td>
                                    <td class="px-6 py-4 text-gray-800">{{ $laporan->lokasi }}</td>
                                    <td class="px-6 py-4 text-gray-800">
                                        @php
                                            $status = strtolower($laporan->status);
                                            $bg = 'bg-gray-100 text-gray-800';
                                            if($status == 'ditangani') $bg = 'bg-blue-100 text-blue-800';
                                            elseif($status == 'ditutup') $bg = 'bg-green-100 text-green-800';
                                            elseif($status == 'ditolak') $bg = 'bg-red-100 text-red-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bg }} capitalize">
                                            {{ $laporan->status ?? 'Menunggu' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('laporan.show', $laporan->id) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">Detail</a>
                                            <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST" class="delete-form">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $laporanMasyarakat->links() }}</div>
            @else
                <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-10 flex flex-col items-center justify-center text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <p class="text-gray-500 font-medium">Belum ada laporan bencana yang terverifikasi.</p>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');

        // 1. Ambil tab terakhir yang dibuka dari Session Storage (default: bencana-terkini)
        let activeTab = sessionStorage.getItem('activeTab_Pelaksana') || 'bencana-terkini';

        // 2. Cek URL Parameter (Jika user baru saja menekan tombol pagination)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('laporan_masyarakat_page')) {
            activeTab = 'laporan-masyarakat';
        } else if (urlParams.has('bencana_terkini_page')) {
            activeTab = 'bencana-terkini';
        }

        // Fungsi untuk mengubah tab
        function switchTab(target) {
            // Reset semua tombol tab
            tabButtons.forEach(function (b) {
                b.classList.remove('border-[#161446]', 'text-[#161446]', 'font-semibold');
                b.classList.add('border-transparent', 'text-gray-500', 'font-medium');
            });
            
            // Highlight tombol tab yang aktif
            const activeBtn = document.querySelector(`.tab-btn[data-tab="${target}"]`);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-gray-500', 'font-medium');
                activeBtn.classList.add('border-[#161446]', 'text-[#161446]', 'font-semibold');
            }

            // Sembunyikan semua panel, lalu tampilkan yang sesuai
            tabPanels.forEach(function (panel) {
                panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
            });

            // Simpan status tab ke Session Storage
            sessionStorage.setItem('activeTab_Pelaksana', target);
        }

        // 3. Jalankan fungsi saat halaman pertama kali dimuat
        switchTab(activeTab);

        // 4. Tambahkan event listener untuk klik tab secara manual
        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                switchTab(btn.dataset.tab);
            });
        });

        // Search Filtering Logic (Filters currently visible table)
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.trim().toLowerCase();
                const activePanel = document.querySelector('.tab-panel:not(.hidden)');
                if (!activePanel) return;

                const rows = activePanel.querySelectorAll('tbody tr');
                rows.forEach(function (row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    });
</script>   