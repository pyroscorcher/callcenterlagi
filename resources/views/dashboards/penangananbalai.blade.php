<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penanganan Balai - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#161446]">

    {{-- Mobile Header --}}
    <div class="md:hidden flex items-center justify-between p-4 bg-[#161446] text-white border-b border-white/10">
        <div class="font-bold text-lg">SITABA</div>

        <button id="open-sidebar-btn" class="p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div class="flex min-h-screen relative overflow-hidden">

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden"></div>

        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        <main class="flex-1 p-0 md:p-8 min-w-0 bg-[#161446]">

            <x-opps.laporan-unit-pelaksana 
                :bencanaTerkini="$bencanaTerkini" 
                :laporanMasyarakat="$laporanMasyarakat" 
            />

        </main>
    </div>

    {{-- Alert Success Session --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
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
        document.addEventListener('DOMContentLoaded', function () {
            
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

            // Sidebar Toggle Functionality untuk Mobile
            const sidebar = document.getElementById('sidebar'); 
            const openBtn = document.getElementById('open-sidebar-btn');
            const closeBtn = document.getElementById('close-sidebar-btn');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                if (sidebar) sidebar.classList.toggle('-translate-x-full');
                if (overlay) overlay.classList.toggle('hidden');
            }

            if(openBtn) openBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>