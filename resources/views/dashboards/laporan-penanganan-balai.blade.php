<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penanganan Balai</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-[#161446]">

<div class="flex min-h-screen">

    <x-balai.sidebarbalai
        :logo-url="asset('logositaba.png')" />

    <main class="flex-1 p-8">

        <x-balai.laporan-penanganan-balai />

    </main>

</div>

</body>

</html>