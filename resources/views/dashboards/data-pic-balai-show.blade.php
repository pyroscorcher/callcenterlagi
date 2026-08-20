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

    <div class="flex min-h-screen">

        <x-balai.sidebarbalai :logo-url="asset('logositaba.png')" />

        <main class="flex-1 p-8">
            <x-balai.detail-pic-balai :balai="$balai" />
        </main>

    </div>

</body>
</html>