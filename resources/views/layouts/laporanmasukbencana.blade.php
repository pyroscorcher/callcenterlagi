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

        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        {{-- Main content --}}
        <main class="flex-1 p-8">

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

</body>
</html>