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

        <x-opps.laporan-table-show :laporan="$laporan" />

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('laporanTableBody');
 
            if (!searchInput || !tableBody) return;
 
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.trim().toLowerCase();
                const rows = tableBody.querySelectorAll('tr');
 
                rows.forEach(function (row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        });
    </script>

</body>
</html>