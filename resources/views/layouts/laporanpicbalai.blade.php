<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail PIC Balai - SITABA</title>
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

        <main class="flex-1 p-8">
            <x-dynamic-component 
                :component="$componentName" 
                :balais="$balais ?? null" 
                :balai="$balai ?? null"
                :laporan="$laporan ?? null" 
                :provinsis="$provinsis ?? []"
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
                timer: 2000, // Closes automatically after 2 seconds
                iconColor: '#7C8B44', // Matches the olive green from your image
                customClass: {
                    title: 'text-xl font-medium text-gray-800'
                }
            });
        });
    </script>
    @endif

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