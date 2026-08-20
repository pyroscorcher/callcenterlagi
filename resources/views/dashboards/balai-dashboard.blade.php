<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penanganan Balai - SITABA</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#161446]">

    {{-- Mobile Header (Hidden on Desktop) --}}
    <div class="md:hidden flex items-center justify-between p-4 bg-[#161446] text-white border-b border-white/10">
        <div class="font-bold text-lg">SITABA</div>
        <button id="open-sidebarbalai-btn" class="p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div class="flex min-h-screen">

        <x-balai.sidebarbalai :logo-url="asset('logositaba.png')" />

        {{-- Main content (ADDED min-w-0 HERE) --}}
        <main class="flex-1 min-w-0 p-8">

            <div class="flex items-center justify-between mb-6 gap-4">
                <h1 class="text-xl font-bold text-white">Laporan Penanganan Balai</h1>

                <div class="flex items-center gap-3">
                    <div class="relative w-full max-w-xs">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Cari Laporan...."
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10
                                focus:outline-none focus:ring-2 focus:ring-[#3B39C4]"
                        />
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </div>

                    <button type="button"
                            class="flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 text-white px-4 py-2.5 text-sm font-medium">
                        Filter
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4h18M6 12h12M10 20h4" />
                        </svg>
                    </button>

                    {{-- TODO: sambungkan ke route export kalau sudah siap --}}
                    <a href="#"
                       class="flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 font-medium text-[#161446]">
                        Export
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm">

                {{-- Tab --}}
                <div class="flex items-center gap-6 border-b border-gray-200 mb-4">
                    <button type="button" data-tab="bencana-terkini"
                            class="tab-btn pb-3 text-sm font-semibold border-b-2 border-[#3B39C4] text-[#3B39C4]">
                        Bencana Terkini
                    </button>
                    <button type="button" data-tab="laporan-masyarakat"
                            class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500">
                        Laporan Masyarakat
                    </button>
                </div>

                {{-- Isi tab: Bencana Terkini (status = ditangani) --}}
                <div id="tab-bencana-terkini" class="tab-panel w-full">
                    <x-balai.laporan-table :type="'bencana-terkini'" :laporans="$bencanaTerkini" />
                    <div class="mt-4">{{ $bencanaTerkini->links() }}</div>
                </div>

                {{-- Isi tab: Laporan Masyarakat (status kosong / ditolak / ditutup) --}}
                <div id="tab-laporan-masyarakat" class="tab-panel hidden w-full">
                    <x-balai.laporan-table :type="'laporan-masyarakat'" :laporans="$laporanMasyarakat" />
                    <div class="mt-4">{{ $laporanMasyarakat->links() }}</div>
                </div>

            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. TAB SWITCHING ---
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = btn.dataset.tab;

                tabButtons.forEach(function (b) {
                    b.classList.remove('border-[#3B39C4]', 'text-[#3B39C4]', 'font-semibold');
                    b.classList.add('border-transparent', 'text-gray-500', 'font-medium');
                });
                btn.classList.remove('border-transparent', 'text-gray-500', 'font-medium');
                btn.classList.add('border-[#3B39C4]', 'text-[#3B39C4]', 'font-semibold');

                tabPanels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
                });
            });
        });

        // --- 2. LIVE SEARCH TABLE ---
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

        // --- 3. TOGGLE SIDEBAR BALAI ---
        const sidebarBalai = document.getElementById('sidebarbalai');
        const openBtn = document.getElementById('open-sidebarbalai-btn');
        const closeBtn = document.getElementById('close-sidebarbalai-btn');
        const overlay = document.getElementById('sidebarbalai-overlay');

        function toggleSidebarBalai() {
            if (sidebarBalai) sidebarBalai.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        if (openBtn) openBtn.addEventListener('click', toggleSidebarBalai);
        if (closeBtn) closeBtn.addEventListener('click', toggleSidebarBalai);
        if (overlay) overlay.addEventListener('click', toggleSidebarBalai);
    });
</script>

</body>
</html>