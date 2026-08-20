<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul ?? 'Laporan Bencana' }} - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#161446]">

    @php
        $mode = $mode ?? 'create';
        $readonly = $mode === 'detail';
        $judul = match ($mode) {
            'create' => 'Tambah Laporan Bencana',
            'edit'   => 'Edit Laporan Bencana',
            default  => 'Detail Laporan Bencana',
        };
    @endphp

    <div class="flex min-h-screen">
        <x-balai.sidebarbalai :logo-url="asset('logositaba.png')" />

        <main class="flex-1 min-w-0 p-8">
            <div class="mb-6">
                <h1 class="text-xl font-bold text-white">{{ $judul }}</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                    <p class="font-medium mb-1">Periksa kembali data yang diisi:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <x-balai.form-laporan-bencana
                :laporan="$laporan ?? null"
                :laporan-balai="$laporanBalai ?? null"
                :provinsis="$provinsis ?? []"
                :readonly="$readonly"
                :balais="$balais ?? []"
            />
        </main>
    </div>

</body>
</html>