<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Balai - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#161446]">

    {{-- Mobile Header --}}
    <div class="md:hidden flex items-center justify-between p-4 bg-[#161446] text-white border-b border-white/10">
        <div class="font-bold text-lg">SITABA</div>
        <button id="open-sidebarbalai-btn" class="p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div class="flex min-h-screen relative overflow-hidden">
        {{-- Mobile Overlay (Penting agar script drawer bekerja) --}}
        <div id="sidebarbalai-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden transition-opacity"></div>

        {{-- Sidebar Component --}}
        <x-balai.sidebarbalai :logo-url="asset('logositaba.png')" />

        {{-- Main Content Area --}}
        <main class="flex-1 min-w-0 p-3 sm:p-6 md:p-8">
            <x-balai.detail-pic-balai :balai="$balai" />
        </main>
    </div>

    {{-- Toggle Script Mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarBalai = document.getElementById('sidebarbalai');
            const openBalaiBtn = document.getElementById('open-sidebarbalai-btn');
            const closeBalaiBtn = document.getElementById('close-sidebarbalai-btn');
            const overlayBalai = document.getElementById('sidebarbalai-overlay');

            function toggleSidebarBalai() {
                if (sidebarBalai) sidebarBalai.classList.toggle('-translate-x-full');
                if (overlayBalai) overlayBalai.classList.toggle('hidden');
            }

            if (openBalaiBtn) openBalaiBtn.addEventListener('click', toggleSidebarBalai);
            if (closeBalaiBtn) closeBalaiBtn.addEventListener('click', toggleSidebarBalai);
            if (overlayBalai) overlayBalai.addEventListener('click', toggleSidebarBalai);
        });
    </script>
</body>
</html>