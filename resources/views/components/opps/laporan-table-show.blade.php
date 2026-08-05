@props([
    'laporan',
])

<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <a href="{{ route('laporan.masuk-bencana') }}" class="hover:underline">Laporan Masuk Bencana</a>
        <span class="mx-2 text-white/50">/</span>
        <span class="font-bold">Detail Laporan</span>
    </div>

    {{-- Card --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">

        {{-- Header Card (Judul & Tombol Edit) --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg font-bold text-gray-900">Detail Laporan</h1>
            
            {{-- Tombol Edit Laporan --}}
            <a href="{{ route('laporan.edit', $laporan->id) }}" 
               class="flex items-center gap-2 rounded-lg bg-[#161446] px-4 py-2 text-sm font-medium text-white hover:bg-[#110e36] transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Laporan
            </a>
        </div>

        <dl class="space-y-5">
            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Jenis Bencana</dt>
                <dd class="text-gray-900">{{ $laporan->jenis_bencana ?: '-' }}</dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Nama Kejadian</dt>
                <dd class="text-gray-900">{{ $laporan->nama_bencana ?: '-' }}</dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Tanggal Kejadian</dt>
                <dd class="text-gray-900">{{ $laporan->waktu_kejadian ?: '-' }}</dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Waktu Pelaporan</dt>
                <dd class="text-gray-900">{{ $laporan->created_at?->translatedFormat('d F Y \p\u\k\u\l H.i') ?? '-' }}</dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">No WhatsApp</dt>
                <dd class="text-gray-900">{{ $laporan->telepon ?: '-' }}</dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Lokasi Kejadian</dt>
                <dd class="text-gray-900">{{ $laporan->lokasi ?: '-' }}</dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Provinsi</dt>
                <dd class="text-gray-900">
                    {{ $laporan->provinsi?->nama ?? '-' }}
                </dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Kabupaten / Kota</dt>
                <dd class="text-gray-900">
                    {{ $laporan->kabupatenKota?->nama ?? '-' }}
                </dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Kecamatan</dt>
                <dd class="text-gray-900">
                    {{ $laporan->kecamatan?->nama ?? '-' }}
                </dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Kelurahan</dt>
                <dd class="text-gray-900">
                    {{ $laporan->kelurahan?->nama ?? '-' }}
                </dd>
            </div>

            {{-- Titik Kejadian --}}
            <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                <dt class="text-gray-700">Titik Kejadian</dt>
                <dd class="text-gray-900 flex items-center justify-between">
                    <span>{{ $laporan->lintang ?? '-' }} , {{ $laporan->bujur ?? '-' }}</span>
                    <a href="{{ route('laporan.edit-lokasi', $laporan->id) }}" 
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Edit Titik Lokasi
                    </a>
                </dd>
            </div>

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Dampak Bencana</dt>
                <dd class="text-gray-900">{{ $laporan->dampak_bencana ?: '-' }}</dd>
            </div>
        </dl>

        {{-- Foto Bencana — Menampilkan multiple foto dari tabel fotos --}}
        <div class="mt-8">
            <h2 class="text-gray-900 mb-3 font-semibold">Foto Bencana</h2>
            
            @if ($laporan->fotos && $laporan->fotos->count() > 0)
                <div class="flex flex-wrap gap-4">
                    @foreach ($laporan->fotos as $foto)
                        <div class="w-64 rounded-xl border border-gray-200 bg-white p-3">
                            <img
                                src="{{ Storage::disk('public')->url($foto->file_path) }}"
                                alt="Foto Bencana"
                                class="w-full h-40 object-cover rounded-lg hover:opacity-90 transition-opacity cursor-pointer"
                            />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="w-64 h-40 rounded-xl border border-dashed border-gray-300 bg-white flex items-center justify-center text-gray-400">
                    Tidak ada foto
                </div>
            @endif
        </div>

        {{-- Kronologi Bencana --}}
        <div class="grid grid-cols-[220px_1fr] gap-4 mt-8">
            <dt class="text-gray-700">Kronologi Bencana</dt>
            <dd class="text-gray-900 leading-relaxed">{{ $laporan->deskripsi ?: '-' }}</dd>
        </div>

        {{-- Kebutuhan Mendesak --}}
        <div class="grid grid-cols-[220px_1fr] gap-4 mt-6">
            <dt class="text-gray-700">Kebutuhan Mendesak</dt>
            <dd class="text-gray-900 leading-relaxed">{{ $laporan->kebutuhan_mendesak ?: '-' }}</dd>
        </div>

        {{-- Unor yang Bertugas (Grouped by Unor) --}}
        <div class="mt-8">
            <h3 class="text-gray-900 font-bold mb-4">Balai yang Bertugas</h3>
            
            @if(isset($laporan->balais) && $laporan->balais->count() > 0)
                <div class="flex flex-col gap-4">
                    @foreach($laporan->balais->groupBy('unor') as $unor => $balaiGroup)
                        <div class="grid grid-cols-[220px_1fr] gap-4 items-start">
                            <div class="text-gray-700">{{ $unor ?: 'Tidak Diketahui' }}</div>
                            <div class="text-gray-900 leading-relaxed space-y-1">
                                @foreach($balaiGroup as $balai)
                                    <div>{{ $balai->nama_balai }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic text-sm">Belum ada balai yang ditugaskan pada laporan ini.</p>
            @endif
        </div>

        {{-- Footer actions --}}
        <div class="flex items-center justify-between mt-10">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('laporan.masuk-bencana') }}"
               class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            <div class="flex items-center gap-3">
                {{-- NEW: Verifikasi Checkbox (Loads initial state from database) --}}
                <label class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-800 cursor-pointer hover:bg-gray-50 transition" id="checkboxWrapper">
                    Laporan Valid?
                    <input type="checkbox" id="laporanValidCheckbox" @checked($laporan->verifikasi) class="w-4 h-4 rounded border-gray-400 text-[#161446] focus:ring-[#161446]">
                </label>

                <button
                    type="button"
                    id="kirimPicButton"
                    class="rounded-lg px-5 py-2.5 font-medium transition-colors bg-gray-300 text-gray-500 cursor-not-allowed"
                >
                    Kirim Pesan Kepada PIC
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('laporanValidCheckbox');
    const button = document.getElementById('kirimPicButton');
    const wrapper = document.getElementById('checkboxWrapper');

    // Function to apply button styles based on checked state
    function updateButtonUI(isChecked) {
        button.disabled = !isChecked;
        button.classList.toggle('bg-gray-300', !isChecked);
        button.classList.toggle('text-gray-500', !isChecked);
        button.classList.toggle('cursor-not-allowed', !isChecked);
        button.classList.toggle('bg-[#161446]', isChecked);
        button.classList.toggle('text-white', isChecked);
    }

    // Set initial button state based on the database verification value
    updateButtonUI(checkbox.checked);

    // NEW: Handle checkbox click (sends AJAX to update database verifikasi)
    checkbox.addEventListener('change', async function () {
        const isChecked = checkbox.checked;
        
        // Prevent spam clicking
        checkbox.disabled = true;
        wrapper.classList.add('opacity-50', 'cursor-wait');

        try {
            const response = await fetch('{{ route('laporan.toggle-verifikasi', $laporan->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ verifikasi: isChecked })
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.message || 'Gagal mengubah status verifikasi.');
            }

            // Sync successful, update UI
            updateButtonUI(isChecked);

        } catch (err) {
            console.error(err);
            alert(err.message);
            // Revert checkbox if it failed
            checkbox.checked = !isChecked; 
            updateButtonUI(!isChecked);
        } finally {
            // Re-enable checkbox
            checkbox.disabled = false;
            wrapper.classList.remove('opacity-50', 'cursor-wait');
        }
    });

    // Handle Kirim Pesan PIC (unchanged)
    button.addEventListener('click', async function () {
        if (button.disabled) return;

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Mengirim...';

        try {
            const response = await fetch('{{ route('laporan.kirim-pic', $laporan->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.message || 'Gagal mengirim pesan.');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            button.textContent = 'Pesan Terkirim';
            alert(data.message);
        } catch (err) {
            console.error(err); 
            alert(err.message);
            button.disabled = false;
            button.textContent = originalText;
        }
    });
});
</script>