<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masuk Bencana - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#161446]">

    {{-- Mobile Header (Hidden on Desktop) --}}
    <div class="md:hidden flex items-center justify-between p-4 bg-[#161446] text-white border-b border-white/10">
        <div class="font-bold text-lg">SITABA</div>
        <button id="open-sidebar-btn" class="p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div class="flex min-h-screen relative w-full overflow-hidden">

        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        {{-- Main content --}}
        <main class="flex-1 p-8">

            <div class="flex items-center justify-between mb-6 gap-4">
                <div class="flex items-center gap-3 flex-1 justify-end">
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

                    <a href=""
                       class="flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 font-medium text-gray-800 transition hover:bg-gray-100">
                        Export
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                        </svg>
                    </a>
                </div>
            </div>

            <x-opps.laporan-table :laporans="$laporans" />

        </main>
    </div>

    @if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Laporan Berhasil Dibuat',
            text: '{{ session("success") }}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#161446',
            iconColor: '#7C8B44',
            timer: 2500,
            timerProgressBar: true
        });
    });
    </script>
    @endif


    <script>
        // Search functionality for both desktop and mobile views
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            if (!searchInput) return;
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                // Desktop rows
                const rows = document.querySelectorAll('#laporanTableBody tr');
                rows.forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(query)
                        ? ''
                        : 'none';
                });
                // Mobile cards
                const cards = document.querySelectorAll('.laporan-card');
                cards.forEach(card => {
                    card.style.display = card.textContent.toLowerCase().includes(query)
                        ? ''
                        : 'none';
                });
            });
        });

        // Delete confirmation using SweetAlert2
        document.querySelectorAll('.delete-form').forEach(form => {

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Hapus Laporan?',
                    text: 'Data laporan akan dihapus secara permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const openBtn = document.getElementById('open-sidebar-btn');
            const closeBtn = document.getElementById('close-sidebar-btn');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            // Event Listeners
            if(openBtn) openBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>

</body>
</html>