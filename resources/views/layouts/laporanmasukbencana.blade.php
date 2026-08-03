<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        {{-- Mobile Overlay (Hidden by default) --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden transition-opacity"></div>

        {{-- Sidebar Component --}}
        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        {{-- Main content --}}
        <main class="flex-1 p-4 md:p-8 w-full max-w-full overflow-x-hidden">

            <x-dynamic-component 
                :component="$component" 
                :laporan="$laporan ?? null" 
                :provinsis="$provinsis ?? null" 
                :kabupatenkotas="$kabupatenkotas ?? null" 
                :kecamatans="$kecamatans ?? null" 
                :kelurahans="$kelurahans ?? null" 
                :balais="$balais ?? null" 
                :assignedBalais="$assignedBalais ?? null"
            />

        </main>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Perubahan Disimpan!',
                showConfirmButton: false,
                timer: 2000,
                iconColor: '#7C8B44',
                customClass: {
                    title: 'text-xl font-medium text-gray-800'
                }
            });
        });
    </script>
    @endif

    {{-- Script to handle sidebar toggle --}}
    <script>
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