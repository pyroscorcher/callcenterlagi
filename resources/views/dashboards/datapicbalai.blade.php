<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PIC Balai - SITABA</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#161446]">

    <div class="flex min-h-screen">

        <x-opps.sidebar :logo-url="asset('logositaba.png')" />

        <main class="flex-1 p-8">

            <div class="flex items-center justify-between mb-6 gap-4">
                <div class="flex items-center gap-3 flex-1 justify-end">
                    <div class="relative w-full max-w-xs">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Cari Balai...."
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10
                                focus:outline-none focus:ring-2 focus:ring-[#3B39C4]"
                        />
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <x-opps.data-pic :balais="$balais" />
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ==========================
        // Search
        // ==========================
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();

                row.style.display = rowText.includes(filter)
                    ? ''
                    : 'none';
            });
        });

        // ==========================
        // Delete Confirmation
        // ==========================
        document.querySelectorAll('.delete-form').forEach(form => {

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Data Balai yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });

    });
    </script>
</body>
</html>