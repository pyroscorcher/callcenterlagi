<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Masuk Bencana - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Perubahan Disimpan!',
                showConfirmButton: false,
                timer: 2000, // Closes automatically after 2 seconds
                iconColor: '#7C8B44', // Matches the olive green from your image
                customClass: {
                    title: 'text-xl font-medium text-gray-800'
                }
            });
        });
    </script>
    @endif

</body>
</html>