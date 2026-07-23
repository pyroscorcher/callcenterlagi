<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail PIC Balai - SITABA</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#161446]">

    <div class="flex min-h-screen">

        {{-- Sidebar Component --}}
        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        <main class="flex-1 p-8">
            {{-- Memanggil komponen detail dan melempar variabel $balai --}}
            <x-opps.data-pic-show :balai="$balai" />
        </main>
    </div>

</body>
</html>