<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PIC Balai - SITABA</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#161446]">

    <div class="flex min-h-screen">

        <x-sidebar :logo-url="asset('logositaba.png')" />

        <main class="flex-1 p-8">
            <h1 class="text-xl font-bold text-white mb-6">Data PIC Balai</h1>

            <x-data-pic-balai :items="$items ?? []" />
        </main>
    </div>

</body>
</html>