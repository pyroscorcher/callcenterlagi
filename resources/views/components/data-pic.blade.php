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

            <div class="bg-[#F4F5F9] rounded-2xl p-8 text-gray-600">
                {{--
                    TODO: this page is waiting on the data model / ERD for
                    Data PIC Balai. This is also the data the "Kirim Pesan
                    Kepada PIC" button on the Laporan detail page needs, so
                    building this out unblocks that too.
                --}}
                Halaman ini masih menunggu model data PIC Balai.
            </div>
        </main>
    </div>

</body>
</html>