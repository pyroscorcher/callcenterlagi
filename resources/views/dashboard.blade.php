<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masuk Bencana - SITABA</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#161446]">

    <div class="flex min-h-screen">

        {{-- Sidebar — pass logo-url="{{ asset('images/logo.png') }}" once you have the real logo asset --}}
        <x-sidebar :logo-url="asset('logositaba.png')" />

        {{-- Main content --}}
        <main class="flex-1 p-8">

            <div class="flex items-center justify-between mb-6 gap-4">
                <h1 class="text-xl font-bold text-white whitespace-nowrap">
                    Laporan Masuk Bencana
                </h1>

                <div class="flex items-center gap-3 flex-1 justify-end">
                    <form method="GET" class="relative w-full max-w-xs">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari Laporan...."
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10
                                   focus:outline-none focus:ring-2 focus:ring-[#3B39C4]"
                        />
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </form>

                    <button type="button"
                            class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 font-medium text-gray-700">
                        Filter
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4h18M6 8h12M10 12h4M11 16h2" />
                        </svg>
                    </button>

                    {{-- <a href="{{ route('laporan.export') }}"
                       class="flex items-center gap-2 rounded-lg bg-[#161446] px-5 py-2.5 font-medium text-white">
                        Export
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                        </svg>
                    </a> --}}
                </div>
            </div>

            <x-laporan-table :laporans="$laporans" />

        </main>
    </div>

</body>
</html>