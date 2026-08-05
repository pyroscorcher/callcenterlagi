<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penanganan Balai - SITABA</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#161446]">

    <div class="flex min-h-screen">

        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        {{-- Main content --}}
        <main class="flex-1 min-w-0 bg-[#F4F5F9]">
            
            {{-- Seluruh UI (Header, Search, Filter, Tabs, Tabel) dipanggil dari komponen ini --}}
            <x-opps.laporan-unit-pelaksana 
                :bencanaTerkini="$bencanaTerkini" 
                :laporanMasyarakat="$laporanMasyarakat" 
            />

        </main>
    </div>

</body>
</html>