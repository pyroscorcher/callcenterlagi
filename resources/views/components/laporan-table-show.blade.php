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
    <div class="bg-[#F4F5F9] rounded-2xl p-8">

        <h1 class="text-lg font-bold text-gray-900 mb-6">Detail Laporan</h1>

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

            {{--
                Titik Kejadian (lat/lng) isn't in the current migration yet.
                Once a latitude/longitude column pair exists on
                laporan_masyarakats, swap the line below back in:

                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <dt class="text-gray-700">Titik Kejadian</dt>
                    <dd class="text-gray-900 flex items-center justify-between">
                        <span>{{ $laporan->latitude ?? '-' }} , {{ $laporan->longitude ?? '-' }}</span>
                        <a href="#" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">
                            Edit Titik Lokasi
                        </a>
                    </dd>
                </div>
            --}}

            <div class="grid grid-cols-[220px_1fr] gap-4">
                <dt class="text-gray-700">Dampak Bencana</dt>
                <dd class="text-gray-900">{{ $laporan->dampak_bencana ?: '-' }}</dd>
            </div>
        </dl>

        {{-- Foto Bencana — always shown, with a placeholder when none was attached --}}
        <div class="mt-8">
            <h2 class="text-gray-900 mb-3">Foto Bencana</h2>
            @if ($laporan->foto)
                <div class="w-64 rounded-xl border border-gray-200 bg-white p-3">
                    <img
                        src="{{ Storage::disk('public')->url($laporan->foto) }}"
                        alt="Foto Bencana"
                        class="w-full h-40 object-cover rounded-lg"
                    />
                    {{--
                        No dedicated caption column exists yet (the migration
                        only stores the file path). If you want a caption like
                        "Bangunan aula gedung hancur..." per photo, that needs
                        a `keterangan_foto` column added to laporan_masyarakats.
                    --}}
                </div>
            @else
                <div class="w-64 h-40 rounded-xl border border-dashed border-gray-300 bg-white flex items-center justify-center text-gray-400">
                    -
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

        {{-- Footer actions --}}
        <div class="flex items-center justify-between mt-10">
            <a href="{{ route('laporan.masuk-bencana') }}"
               class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-800 cursor-pointer">
                    Laporan Valid?
                    <input type="checkbox" id="laporanValidCheckbox" class="w-4 h-4 rounded border-gray-400">
                </label>

                {{--
                    Sending to a PIC needs PIC/Balai data, which — like
                    latitude/longitude above — isn't modeled yet. This button
                    is wired to enable only once "Laporan Valid?" is checked;
                    the actual send action is a TODO for when Data PIC Balai exists.
                --}}
                <button
                    type="button"
                    id="kirimPicButton"
                    disabled
                    class="rounded-lg bg-gray-300 px-5 py-2.5 text-gray-500 font-medium cursor-not-allowed"
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

        checkbox.addEventListener('change', function () {
            button.disabled = !checkbox.checked;
            button.classList.toggle('bg-gray-300', !checkbox.checked);
            button.classList.toggle('text-gray-500', !checkbox.checked);
            button.classList.toggle('cursor-not-allowed', !checkbox.checked);
            button.classList.toggle('bg-[#161446]', checkbox.checked);
            button.classList.toggle('text-white', checkbox.checked);
            button.classList.toggle('cursor-pointer', checkbox.checked);
        });
    });
</script>