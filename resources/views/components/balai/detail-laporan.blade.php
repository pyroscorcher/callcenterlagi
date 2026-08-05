{{--
    Komponen: <x-balai.detail-laporan :laporan="$laporan" />

    Prop:
    - laporan : model LaporanMasyarakat (dengan relasi fotos, provinsi, kabupatenKota, kecamatan, kelurahan)
--}}
@props([
    'laporan',
])

<div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
    <h1 class="text-lg font-bold text-gray-900 mb-8">Detail Laporan</h1>

    <div class="grid grid-cols-[220px_1fr] gap-y-7 text-sm">

        <div class="text-gray-700">Jenis Bencana</div>
        <div class="text-gray-900">{{ $laporan->jenis_bencana }}</div>

        <div class="text-gray-700">Nama Kejadian</div>
        <div class="text-gray-900">{{ $laporan->nama_bencana }}</div>

        <div class="text-gray-700">Tanggal Kejadian</div>
        <div class="text-gray-900">{{ $laporan->waktu_kejadian }}</div>

        <div class="text-gray-700">Waktu Pelaporan</div>
        <div class="text-gray-900">{{ $laporan->created_at?->translatedFormat('d F Y \p\u\k\u\l H.i') }}</div>

        <div class="text-gray-700">No WhatsApp</div>
        <div class="text-gray-900">{{ $laporan->telepon }}</div>

        <div class="text-gray-700">Lokasi Kejadian</div>
        <div class="text-gray-900">
            {{ collect([
                $laporan->provinsi?->nama,
                $laporan->kabupatenKota?->nama,
                $laporan->kecamatan?->nama,
                $laporan->kelurahan?->nama,
            ])->filter()->implode(', ') ?: '-' }}
        </div>

        <div class="text-gray-700">Titik Kejadian</div>
        <div class="text-gray-900">{{ $laporan->lintang }} , {{ $laporan->bujur }}</div>

        <div class="text-gray-700">Dampak Bencana</div>
        <div class="text-gray-900">{{ $laporan->dampak_bencana }}</div>

        <div class="text-gray-700 self-start">Foto Bencana</div>
        <div class="flex flex-wrap gap-4">
            @forelse ($laporan->fotos as $foto)
                <div class="w-64 rounded-lg overflow-hidden border border-gray-200 bg-white">
                    <img src="{{ Storage::disk('public')->url($foto->file_path) }}"
                         alt="Foto Bencana" class="w-full h-40 object-cover">
                    @if ($foto->keterangan ?? false)
                        <div class="p-3">
                            <p class="text-xs text-gray-500">Keterangan Foto</p>
                            <p class="text-gray-900">{{ $foto->keterangan }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-400">Tidak ada foto.</p>
            @endforelse
        </div>

        <div class="text-gray-700 self-start">Kronologi Bencana</div>
        <div class="text-gray-900 max-w-2xl leading-relaxed">{{ $laporan->deskripsi }}</div>

        <div class="text-gray-700 self-start">Kebutuhan Mendesak</div>
        <div class="text-gray-900 max-w-2xl leading-relaxed">{{ $laporan->kebutuhan_mendesak }}</div>
    </div>

    {{-- Status Laporan (per balai) --}}
    <div class="mt-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-gray-900">Status Laporan</h2>
            <button type="button"
                    onclick="document.getElementById('modal-update-status').classList.remove('hidden')"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">
                Update Status
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-[#C9D6F5] text-gray-900">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Unor</th>
                        <th class="px-4 py-3 font-semibold">Unker</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Detail Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    {{--
                        Unor & Unker diambil dari akun balai yang sedang login (auth:balai).
                        Status & Detail Status ambil dari data laporan.
                        Nanti kalau tabel balai_laporan sudah jadi (1 laporan bisa ditugaskan ke
                        banyak balai), ganti semua jadi:
                        @forelse ($laporan->balaiLaporans as $bl)
                            <tr>
                                <td class="px-4 py-3">{{ $bl->balai->unor }}</td>
                                <td class="px-4 py-3">{{ $bl->balai->nama_balai }}</td>
                                <td class="px-4 py-3">{{ $bl->status ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $bl->detail_status ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada balai yang ditugaskan.</td></tr>
                        @endforelse
                    --}}
                    <tr>
                        <td class="px-4 py-3">{{ auth('balai')->user()->unor ?? '-' }}</td>
                        <td class="px-4 py-3">{{ auth('balai')->user()->nama_balai ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $laporan->status ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $laporan->detail_status ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-10">
        <a href="{{ url()->previous()}}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>