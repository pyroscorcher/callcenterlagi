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
            {{-- 
              Parameter :component menerima string nama komponen (misal: 'opps.data-pic')
              Atribut tambahan dikirim untuk menampung data dinamis.
            --}}
            <x-dynamic-component 
                :component="$componentName" 
                :balais="$balais ?? null" 
                :balai="$balai ?? null" 
                :laporan="$laporan ?? null" 
            />
        </main>
    </div>

</body>
</html>