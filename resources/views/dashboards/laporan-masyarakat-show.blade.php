<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#161446]">

    <div class="flex min-h-screen">

        <x-balai.sidebarbalai :logo-url="asset('logositaba.png')" />

        {{-- Main content --}}
        <main class="flex-1 p-8">

            <div class="flex items-center justify-between mb-6">
                <div class="text-white">
                    <a href="{{ route('balai.dashboard') }}" class="text-white/70 hover:text-white">
                        Laporan Penanganan Balai
                    </a>
                    <span class="text-white/70"> / </span>
                    <span class="font-bold">Detail Laporan</span>
                </div>

                {{-- Ganti route('...') di bawah dengan route export asli kamu kalau sudah ada --}}
                <a href="#"
                   class="flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 font-medium text-[#161446]">
                    Export
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                    </svg>
                </a>
            </div>

            <x-balai.detail-laporan :laporan="$laporan" />

        </main>
    </div>

    <x-balai.modal-update-status :laporan="$laporan" />

</body>
</html>