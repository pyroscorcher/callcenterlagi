{{--
    Komponen: <x-balai.form-laporan-bencana :laporan="$laporan ?? null" :readonly="true|false" />
--}}

@props([
    'laporan' => null,
    'readonly' => false,
])

@php
    $disabled = $readonly ? 'disabled' : '';
    $ro = $readonly ? 'bg-gray-50 text-gray-600' : ''; // dipakai HANYA di mode edit/create (tetap ada select/input)

    // Baris label-kiri / isi-kanan untuk mode readonly.
    // Dipanggil lewat @include biar tidak menulis ulang markup yang sama berkali-kali.
    $detailRow = function (string $label, $value) {
        $value = $value === null || $value === '' ? '-' : $value;
        return '<div class="flex flex-col sm:flex-row py-1.5 gap-1 sm:gap-6">
            <div class="w-56 shrink-0 text-sm text-gray-500">' . e($label) . '</div>
            <div class="flex-1 text-sm text-gray-900 whitespace-pre-line">' . e($value) . '</div>
        </div>';
    };
@endphp

<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm">

        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Laporan Bencana Unit Pelaksana</h1>
                <p class="text-sm text-gray-500">
                    {{ $readonly ? 'Detail Laporan (Read Only)' : ($laporan ? 'Edit Data' : 'Input Data') }}
                </p>
            </div>
        </div>

        <form action="{{ $laporan ? route('balai.laporan-penanganan-balai.update', $laporan->id) : route('balai.laporan-penanganan-balai.store') }}"
              method="POST" class="px-8 py-6 {{ $readonly ? 'space-y-8' : 'space-y-12' }}">
            @csrf
            @if($laporan)
                @method('PUT')
            @endif

            {{-- ================= Identitas Kejadian ================= --}}
            @if($readonly)
                <div>
                    {!! $detailRow('Jenis Bencana', $laporan->jenis_bencana ?? null) !!}
                    {!! $detailRow('Nama Kejadian', $laporan->nama_bencana ?? null) !!}
                    {!! $detailRow('Tanggal Pelaporan', $laporan?->created_at?->format('d/m/Y')) !!}
                    {!! $detailRow('Tanggal & Jam Kejadian', trim(($laporan->waktu_kejadian ?? '') . ' ' . ($laporan->wilayah_waktu ?? ''))) !!}
                </div>
            @else
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Bencana</label>
                        <select name="jenis_bencana" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Jenis Kejadian Bencana</option>
                            <option value="{{ $laporan->jenis_bencana ?? '' }}" selected>{{ $laporan->jenis_bencana ?? '' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kejadian</label>
                        <select name="nama_bencana" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Nama Kejadian</option>
                            <option value="{{ $laporan->nama_bencana ?? '' }}" selected>{{ $laporan->nama_bencana ?? '' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pelaporan</label>
                        <input type="date" name="tanggal_pelaporan"
                               value="{{ $laporan?->created_at?->format('Y-m-d') }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal & Jam Kejadian</label>
                        <div class="flex gap-3">
                            <input type="text" name="waktu_kejadian" placeholder="Tanggal Kejadian"
                                   value="{{ $laporan->waktu_kejadian ?? '' }}"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <select name="wilayah_waktu" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option value="">Wilayah Waktu</option>
                                <option value="WIB" @selected(($laporan->wilayah_waktu ?? null) === 'WIB')>WIB</option>
                                <option value="WITA" @selected(($laporan->wilayah_waktu ?? null) === 'WITA')>WITA</option>
                                <option value="WIT" @selected(($laporan->wilayah_waktu ?? null) === 'WIT')>WIT</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= Lokasi Kejadian ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Lokasi Kejadian</h2>

                @if($readonly)
                    {!! $detailRow('Provinsi', $laporan->provinsi?->nama ?? null) !!}
                    {!! $detailRow('Kabupaten / Kota', $laporan->kabupatenKota?->nama ?? null) !!}
                    {!! $detailRow('Kecamatan', $laporan->kecamatan?->nama ?? null) !!}
                    {!! $detailRow('Kelurahan', $laporan->kelurahan?->nama ?? null) !!}
                    {!! $detailRow('Detail Lokasi', $laporan->detail_lokasi ?? null) !!}
                    {!! $detailRow('DAS dan WS', $laporan->das_ws ?? null) !!}
                    {!! $detailRow('PCH dan Intensitas Curah Hujan', $laporan->pch ?? null) !!}
                    {!! $detailRow('Ruas Jalan', $laporan->ruas_jalan ?? null) !!}
                    {!! $detailRow('Titik Kejadian', isset($laporan->lintang, $laporan->bujur) ? $laporan->lintang . ' , ' . $laporan->bujur : null) !!}
                    {!! $detailRow('Kewenangan Infrastruktur', $laporan->kewenangan_infrastruktur ?? null) !!}
                @else
                    <div class="grid grid-cols-3 gap-5 mb-5">
                        <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option>{{ $laporan->provinsi?->nama ?? 'Pilih Provinsi' }}</option>
                        </select>
                        <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option>{{ $laporan->kabupatenKota?->nama ?? 'Pilih Kab/Kota' }}</option>
                        </select>
                        <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option>{{ $laporan->kecamatan?->nama ?? 'Pilih Kecamatan' }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-5 mb-5">
                        <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option>{{ $laporan->kelurahan?->nama ?? 'Pilih Kelurahan' }}</option>
                        </select>
                    </div>

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Lokasi</label>
                    <input type="text" name="lokasi" placeholder="Detail Lokasi"
                           value="{{ $laporan->lokasi ?? '' }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-5">

                    <div class="grid grid-cols-3 gap-5 mb-5">
                        <input type="text" name="das_ws" placeholder="Tambahkan Nama DAS dan WS"
                               value="{{ $laporan->das_ws ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <input type="text" name="pch" placeholder="Tambahkan PCH dan intensitas curah hujan"
                               value="{{ $laporan->pch ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <input type="text" name="ruas_jalan" placeholder="Tambahkan ruas jalan dan nomor ruas"
                               value="{{ $laporan->ruas_jalan ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <input type="text" name="lintang" placeholder="Latitude (contoh: -6.200000)"
                               value="{{ $laporan->lintang ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <input type="text" name="bujur" placeholder="Longitude (contoh: 106.816666)"
                               value="{{ $laporan->bujur ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>

                    <div class="mt-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kewenangan Infrastruktur</label>
                        <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option>{{ $laporan->kewenangan_infrastruktur ?? 'Pilih Kewenangan' }}</option>
                        </select>
                    </div>
                @endif
            </div>

            {{-- ================= Deskripsi ================= --}}
            @if($readonly)
                <div>
                    {!! $detailRow('Deskripsi Penyebab Bencana', $laporan->deskripsi ?? null) !!}
                    {!! $detailRow('Dampak Kerusakan', $laporan->dampak_bencana ?? null) !!}
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Penyebab Bencana</label>
                    <textarea name="deskripsi" rows="4"
                              placeholder="Contoh pendangkalan sungai ..., WS Kewenangan Balai/Pemerintah Daerah Kota/Kabupaten/Provinsi"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ $laporan->deskripsi ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Dampak Kerusakan</label>
                    <textarea name="dampak_bencana" rows="4"
                              placeholder="(a. Dampak kepada Infrastruktur ..., kewenangan ... Balai/Pemerintah Daerah Kota/Kabupaten/Provinsi)"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ $laporan->dampak_bencana ?? '' }}</textarea>
                </div>
            @endif

            {{-- ================= Dokumentasi Bencana ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Dokumentasi Bencana</h2>

                @if($readonly)
                    @forelse (($laporan->fotos ?? []) as $foto)
                        {!! $detailRow('Foto ' . $loop->iteration, $foto->keterangan ?? null) !!}
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div class="space-y-4" id="dokumentasi-bencana-list">
                        @forelse (($laporan->fotos ?? []) as $foto)
                            <div class="flex gap-4 items-start">
                                <button type="button" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Image/Video</button>
                                <textarea rows="2" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Keterangan foto">{{ $foto->keterangan ?? '' }}</textarea>
                                <button type="button" onclick="this.closest('div').remove()" class="{{ $loop->first ? 'hidden ' : '' }}shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                            </div>
                        @empty
                            <div class="flex gap-4 items-start">
                                <button type="button" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Image/Video</button>
                                <textarea rows="2" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Keterangan foto"></textarea>
                                <button type="button" onclick="this.closest('div').remove()" class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="tambah-dokumentasi"
                            class="mt-4 rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                        + Tambah Foto/Video
                    </button>
                @endif
            </div>

            {{-- ================= Kebutuhan Mendesak ================= --}}
            @if($readonly)
                {!! $detailRow('Kebutuhan Mendesak', $laporan->kebutuhan_mendesak ?? null) !!}
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kebutuhan Mendesak</label>
                    <textarea name="kebutuhan_mendesak" rows="3" placeholder="Tambahkan Kebutuhan Mendesak"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ $laporan->kebutuhan_mendesak ?? '' }}</textarea>
                </div>
            @endif

            {{-- ================= Infrastruktur Terdampak ================= --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-900">Infrastruktur Terdampak</h2>
                    @unless($readonly)
                        <button type="button" data-add="infrastruktur"
                                class="rounded-full bg-blue-600 text-white w-8 h-8 flex items-center justify-center hover:bg-blue-700 transition">+</button>
                    @endunless
                </div>

                @if($readonly)
                    @forelse (($laporan->infrastruktur ?? []) as $item)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Infrastruktur {{ $loop->iteration }}</p>
                            {!! $detailRow('Unit Organisasi', $item->unit_organisasi ?? null) !!}
                            {!! $detailRow('Kategori Infrastruktur', $item->kategori ?? null) !!}
                            {!! $detailRow('Nama Infrastruktur', $item->nama ?? null) !!}
                            {!! $detailRow('Satuan', $item->satuan ?? null) !!}
                            {!! $detailRow('Jumlah', $item->jumlah ?? null) !!}
                            {!! $detailRow('Keterangan', $item->keterangan ?? null) !!}
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="infrastruktur-list" class="space-y-5">
                        <div class="infrastruktur-row border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Infrastruktur</span>
                                <button type="button" data-remove-row
                                        class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                            </div>
                            <div class="grid grid-cols-3 gap-5">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Unit Organisasi</option>
                                </select>
                                <input type="text" placeholder="Kategori Infrastruktur" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <input type="text" placeholder="Nama Infrastruktur" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-3 gap-5">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Satuan</option>
                                </select>
                                <input type="number" placeholder="Jumlah" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <input type="text" placeholder="Jalan tergenang/jembatan putus" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto/Video Dokumentasi</label>
                                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Image/Video</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= Penanganan Sementara ================= --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-900">Penanganan Sementara</h2>
                    @unless($readonly)
                        <button type="button" data-add="penanganan"
                                class="rounded-full bg-blue-600 text-white w-8 h-8 flex items-center justify-center hover:bg-blue-700 transition">+</button>
                    @endunless
                </div>

                @if($readonly)
                    @forelse (($laporan->penanganan_sementara ?? []) as $p)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">{{ $p->judul ?? ('Penanganan ' . $loop->iteration) }}</p>
                            {!! $detailRow('Tanggal', $p->tanggal ?? null) !!}
                            {!! $detailRow('Kewenangan', $p->kewenangan ?? null) !!}
                            {!! $detailRow('Unit Organisasi', $p->unit_organisasi ?? null) !!}
                            {!! $detailRow('Unit Kerja', $p->unit_kerja ?? null) !!}
                            {!! $detailRow('Deskripsi', $p->deskripsi ?? null) !!}

                            @forelse (($p->fotos ?? []) as $foto)
                                <div class="pl-4 border-l-2 border-gray-100 mt-2">
                                    {!! $detailRow('Foto/Lokasi ' . $loop->iteration, trim(($foto->latitude ?? '') . ', ' . ($foto->longitude ?? '') . ' — ' . ($foto->keterangan ?? ''))) !!}
                                </div>
                            @empty
                            @endforelse
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="penanganan-list" class="space-y-5">
                        <div class="penanganan-row border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-4">
                                <input type="text" placeholder="Pembersihan lumpur banjir"
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <button type="button" data-remove-row
                                        class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <input type="date" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Pilih Kewenangan</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Unit Organisasi</option>
                                </select>
                                <input type="text" placeholder="Unit Kerja" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <textarea rows="3" placeholder="(a. Penanganan ... oleh Balai/Pemerintah Daerah Kota/Kabupaten/Provinsi ...)"
                                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>

                            <div class="penanganan-foto-list space-y-4">
                                <div class="penanganan-foto-row flex gap-4 items-start">
                                    <button type="button" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Image/Video</button>
                                    <div class="w-40 space-y-2">
                                        <input type="text" placeholder="Latitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <input type="text" placeholder="Longitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    </div>
                                    <textarea rows="2" placeholder="description" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                                    <button type="button" data-remove-foto-row
                                            class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                                </div>
                            </div>
                            <button type="button" data-add-foto
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50 transition">
                                + Tambah Foto/Lokasi
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= Sumberdaya ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Sumberdaya</h2>

                @if($readonly)
                    {!! $detailRow('Total Personel', $laporan->total_personel ?? null) !!}

                    <p class="text-sm font-semibold text-gray-700 mt-4 mb-1">Alat</p>
                    @forelse (($laporan->sumberdaya_alat ?? []) as $alat)
                        <div class="{{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
                            {!! $detailRow('Kategori / Kelas / Model', trim(($alat->kategori ?? '-') . ' / ' . ($alat->kelas ?? '-') . ' / ' . ($alat->model ?? '-'))) !!}
                            {!! $detailRow('Total', $alat->total ?? null) !!}
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse

                    <p class="text-sm font-semibold text-gray-700 mt-4 mb-1">Bahan</p>
                    @forelse (($laporan->sumberdaya_bahan ?? []) as $bahan)
                        <div class="{{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
                            {!! $detailRow('Kategori / Kelas / Model', trim(($bahan->kategori ?? '-') . ' / ' . ($bahan->kelas ?? '-') . ' / ' . ($bahan->model ?? '-'))) !!}
                            {!! $detailRow('Total', $bahan->total ?? null) !!}
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Personel</label>
                    <input type="number" placeholder="Total Personel"
                           value="{{ $laporan->total_personel ?? '' }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-5">

                    <div class="flex items-center gap-4 mb-5 text-sm">
                        <button type="button" id="tambah-alat" class="rounded-lg bg-blue-100 text-blue-700 px-4 py-2 font-medium hover:bg-blue-200 transition">+ Tambah Alat</button>
                        <button type="button" id="tambah-bahan" class="rounded-lg bg-yellow-100 text-yellow-700 px-4 py-2 font-medium hover:bg-yellow-200 transition">+ Tambah Bahan</button>
                        <a href="#" class="text-blue-600 underline">Kamus Alat & Bahan</a>
                    </div>

                    <template id="tpl-sumberdaya-alat">
                        <div class="sumberdaya-alat-row grid grid-cols-[1fr_1fr_1fr_1fr_auto] gap-4">
                            <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option>Kategori Alat</option></select>
                            <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option>Kelas</option></select>
                            <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option>Model</option></select>
                            <input type="number" placeholder="Total" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <button type="button" data-remove-row
                                    class="rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">−</button>
                        </div>
                    </template>
                    <template id="tpl-sumberdaya-bahan">
                        <div class="sumberdaya-bahan-row grid grid-cols-[1fr_1fr_1fr_1fr_auto] gap-4">
                            <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option>Kategori Bahan</option></select>
                            <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option>Kelas</option></select>
                            <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option>Model</option></select>
                            <input type="number" placeholder="Total" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <button type="button" data-remove-row
                                    class="rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">−</button>
                        </div>
                    </template>

                    <div id="sumberdaya-alat-list" class="space-y-4 mb-4"></div>
                    <div id="sumberdaya-bahan-list" class="space-y-4"></div>
                @endif
            </div>

            {{-- ================= Penanganan Permanen ================= --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-900">Penanganan Permanen</h2>
                    @unless($readonly)
                        <button type="button" data-add="penanganan-permanen"
                                class="rounded-full bg-blue-600 text-white w-8 h-8 flex items-center justify-center hover:bg-blue-700 transition">+</button>
                    @endunless
                </div>

                @if($readonly)
                    @forelse (($laporan->penanganan_permanen ?? []) as $pp)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Penanganan Permanen {{ $loop->iteration }}</p>
                            {!! $detailRow('Tanggal', $pp->tanggal ?? null) !!}
                            {!! $detailRow('Kewenangan', $pp->kewenangan ?? null) !!}
                            {!! $detailRow('Unit Organisasi', $pp->unit_organisasi ?? null) !!}
                            {!! $detailRow('Unit Kerja', $pp->unit_kerja ?? null) !!}
                            {!! $detailRow('Deskripsi Penanganan', $pp->deskripsi ?? null) !!}

                            @forelse (($pp->fotos ?? []) as $foto)
                                <div class="pl-4 border-l-2 border-gray-100">
                                    {!! $detailRow('Foto/Video ' . $loop->iteration, $foto->keterangan ?? null) !!}
                                </div>
                            @empty
                            @endforelse
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="penanganan-permanen-list" class="space-y-5">
                        <div class="penanganan-permanen-row border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Penanganan Permanen</span>
                                <button type="button" data-remove-row
                                        class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                            </div>

                            <div class="grid grid-cols-2 gap-5">
                                <input type="date" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Pilih Kewenangan</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Unit Organisasi</option>
                                </select>
                                <input type="text" placeholder="Unit Kerja" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Penanganan</label>
                                <div class="rounded-lg border border-gray-300 overflow-hidden">
                                    <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 px-2 py-1.5">
                                        <button type="button" disabled class="w-7 h-7 rounded text-sm font-bold text-gray-500">B</button>
                                        <button type="button" disabled class="w-7 h-7 rounded text-sm italic text-gray-500">I</button>
                                        <button type="button" disabled class="w-7 h-7 rounded text-sm underline text-gray-500">U</button>
                                    </div>
                                    <textarea name="deskripsi_penanganan_permanen" rows="4"
                                              placeholder="(a. Memobilisasi alat berat berupa ... untuk normalisasi sungai ...)"
                                              class="w-full border-0 px-3 py-2 text-sm focus:ring-0 focus:outline-none"></textarea>
                                </div>
                            </div>

                            <div class="penanganan-permanen-foto-list space-y-4"></div>
                            <button type="button" data-add-foto-permanen
                                    class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                                + Tambah Foto/Video
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= Status Terkini =================
                 Sengaja dipisah dari card Penanganan Permanen di atas — ini status
                 tunggal untuk keseluruhan laporan, bukan per-entri penanganan. --}}
            <div>
                @if($readonly)
                    {!! $detailRow('Status Terkini', $laporan->status_terkini ?? null) !!}
                @else
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Terkini</label>
                    <div class="rounded-lg border border-gray-300 overflow-hidden">
                        <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 px-2 py-1.5">
                            <button type="button" disabled class="w-7 h-7 rounded text-sm font-bold text-gray-500">B</button>
                            <button type="button" disabled class="w-7 h-7 rounded text-sm italic text-gray-500">I</button>
                            <button type="button" disabled class="w-7 h-7 rounded text-sm underline text-gray-500">U</button>
                        </div>
                        <textarea name="status_terkini" rows="4"
                                  placeholder="(a. Air berangsur/sudah surut dari ketinggian ... menjadi ...)"
                                  class="w-full border-0 px-3 py-2 text-sm focus:ring-0 focus:outline-none">{{ $laporan->status_terkini ?? '' }}</textarea>
                    </div>
                @endif
            </div>

            {{-- ================= Dokumen Laporan Pimpinan ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Dokumen Laporan Pimpinan</h2>

                @if($readonly)
                    @forelse (($laporan->dokumen_laporan ?? []) as $dok)
                        {!! $detailRow('Dokumen ' . $loop->iteration, $dok->nama ?? null) !!}
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="dokumen-laporan-list" class="space-y-3 mb-4"></div>
                    <button type="button" id="tambah-dokumen"
                            class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                        + Tambah Dokumen
                    </button>
                @endif
            </div>

            {{-- ================= Dilaporkan Oleh ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Dilaporkan oleh</h2>

                @if($readonly)
                    @forelse (($laporan->pic ?? []) as $pic)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">PIC {{ $loop->iteration }}</p>
                            {!! $detailRow('Unit Organisasi', $pic->unit_organisasi ?? null) !!}
                            {!! $detailRow('Unit Kerja', $pic->unit_kerja ?? null) !!}
                            {!! $detailRow('Nama PIC', $pic->nama ?? null) !!}
                            {!! $detailRow('No. HP', $pic->no_hp ?? null) !!}
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="pic-list" class="space-y-5">
                        <div class="pic-row border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">PIC</span>
                                <button type="button" data-remove-row
                                        class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <select class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option>Unit Organisasi</option>
                                </select>
                                <input type="text" placeholder="Unit Kerja" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <input type="text" placeholder="Nama PIC" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <input type="text" placeholder="No. HP" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <input type="text" placeholder="PIC Lainnya" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    </div>
                    <button type="button" id="tambah-pic"
                            class="mt-4 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium hover:bg-gray-50 transition">
                        + Tambah PIC
                    </button>
                @endif
            </div>

            {{-- ================= Log Perubahan Data (khusus mode readonly) =================
                 Data ini nantinya diambil dari relasi/tabel log perubahan laporan
                 (misal $laporan->logs). Selama tabelnya belum ada, forelse di bawah
                 otomatis jatuh ke "Tidak ada data". --}}
            @if($readonly)
                <div class="pt-6 border-t border-gray-100">
                    <h2 class="font-bold text-gray-900 mb-4">Log Perubahan Data</h2>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-blue-300">
                                    <th class="px-4 py-3 font-semibold text-gray-900 text-center">Tanggal Perubahan</th>
                                    <th class="px-4 py-3 font-semibold text-gray-900 text-center">Aktivitas</th>
                                    <th class="px-4 py-3 font-semibold text-gray-900 text-center">Oleh</th>
                                    <th class="px-4 py-3 font-semibold text-gray-900 text-center">Unker</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($laporan->logs ?? []) as $log)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                        <td class="px-4 py-3 text-center text-gray-700">{{ $log->tanggal ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-gray-700">{{ $log->aktivitas ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-gray-700">{{ $log->oleh ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-gray-700 text-xs">{{ $log->unit_kerja ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ================= Footer Actions ================= --}}
            @if($readonly)
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100">
                    <button type="button" onclick="if (document.referrer) { window.history.back(); } else { window.location.href = '{{ route('balai.laporan-penanganan-balai') }}'; }"
                       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </button>

                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-800 cursor-pointer hover:bg-gray-50 transition">
                            Laporan Selesai?
                            <input type="checkbox" id="laporanSelesaiCheckbox" class="w-4 h-4 rounded border-gray-400 text-[#161446] focus:ring-[#161446]">
                        </label>

                        {{-- TODO: sambungkan ke aksi kirim pesan konfirmasi kalau sudah siap --}}
                        <button type="button" id="kirimKonfirmasiButton" disabled
                                class="rounded-lg bg-gray-300 px-5 py-2.5 text-gray-500 font-medium cursor-not-allowed transition-colors">
                            Kirim Pesan Konfirmasi
                        </button>
                    </div>
                </div>
            @else
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100">
                    <button type="button" onclick="if (document.referrer) { window.history.back(); } else { window.location.href = '{{ route('balai.laporan-penanganan-balai') }}'; }"
                       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </button>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-[#161446] px-6 py-2.5 text-white font-medium hover:bg-[#110e36] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-6 0V5a2 2 0 012-2h0a2 2 0 012 2v2m-4 0h4" />
                            </svg>
                            {{ $laporan ? 'Update' : 'Submit Data' }}
                        </button>

                        <label class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-800 cursor-pointer hover:bg-gray-50 transition">
                            Laporan Selesai?
                            <input type="checkbox" id="laporanSelesaiCheckbox" class="w-4 h-4 rounded border-gray-400 text-[#161446] focus:ring-[#161446]">
                        </label>

                        <button type="button" id="kirimKonfirmasiButton" disabled
                                class="rounded-lg bg-gray-300 px-5 py-2.5 text-gray-500 font-medium cursor-not-allowed transition-colors">
                            Kirim Pesan Konfirmasi
                        </button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('laporanSelesaiCheckbox');
        const button = document.getElementById('kirimKonfirmasiButton');
        if (!checkbox || !button) return;

        checkbox.addEventListener('change', function () {
            button.disabled = !checkbox.checked;
            button.classList.toggle('bg-gray-300', !checkbox.checked);
            button.classList.toggle('text-gray-500', !checkbox.checked);
            button.classList.toggle('cursor-not-allowed', !checkbox.checked);
            button.classList.toggle('bg-[#161446]', checkbox.checked);
            button.classList.toggle('text-white', checkbox.checked);
            button.classList.toggle('cursor-pointer', checkbox.checked);
            button.classList.toggle('hover:bg-[#110e36]', checkbox.checked);
        });
    });
</script>

@unless($readonly)

<script>
    document.addEventListener('DOMContentLoaded', function () {

        function showRemoveButtons(scopeEl) {
            scopeEl.querySelectorAll(
                '[data-remove-row], [data-remove-foto-row], [data-remove-permanen-foto-row], [data-remove-dokumen-row]'
            ).forEach(btn => btn.classList.remove('hidden'));
        }

        document.querySelectorAll('[data-add]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const key = btn.dataset.add;
                const container = document.getElementById(key + '-list');
                const rows = container.querySelectorAll(':scope > .' + key + '-row');
                const clone = rows[rows.length - 1].cloneNode(true);
                clone.querySelectorAll('input, textarea').forEach(el => el.value = '');
                showRemoveButtons(clone);
                container.appendChild(clone);
            });
        });

        function addSumberdayaRow(buttonId, listId, templateId) {
            document.getElementById(buttonId)?.addEventListener('click', function () {
                const container = document.getElementById(listId);
                const template = document.getElementById(templateId);
                const clone = template.content.firstElementChild.cloneNode(true);
                container.appendChild(clone);
            });
        }
        addSumberdayaRow('tambah-alat', 'sumberdaya-alat-list', 'tpl-sumberdaya-alat');
        addSumberdayaRow('tambah-bahan', 'sumberdaya-bahan-list', 'tpl-sumberdaya-bahan');

        document.getElementById('tambah-pic')?.addEventListener('click', function () {
            const container = document.querySelector('#pic-list');
            const rows = container.querySelectorAll('.pic-row');
            const clone = rows[rows.length - 1].cloneNode(true);
            clone.querySelectorAll('input').forEach(el => el.value = '');
            showRemoveButtons(clone);
            container.appendChild(clone);
        });

        document.getElementById('tambah-dokumentasi')?.addEventListener('click', function () {
            const container = document.querySelector('#dokumentasi-bencana-list');
            const div = document.createElement('div');
            div.className = 'flex gap-4 items-start';
            div.innerHTML = `
                <button type="button" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Image/Video</button>
                <textarea rows="2" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Keterangan foto"></textarea>
                <button type="button" onclick="this.closest('div').remove()" class="shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
            `;
            container.appendChild(div);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-add-foto]')) {
                const row = e.target.closest('.penanganan-row');
                const list = row.querySelector('.penanganan-foto-list');
                const rows = list.querySelectorAll('.penanganan-foto-row');
                const clone = rows[rows.length - 1].cloneNode(true);
                clone.querySelectorAll('input, textarea').forEach(el => el.value = '');
                showRemoveButtons(clone);
                list.appendChild(clone);
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-add-foto-permanen]')) {
                const row = e.target.closest('.penanganan-permanen-row');
                const list = row.querySelector('.penanganan-permanen-foto-list');
                const div = document.createElement('div');
                div.className = 'penanganan-permanen-foto-row flex gap-4 items-start';
                div.innerHTML = `
                    <button type="button" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Image/Video</button>
                    <textarea rows="2" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Keterangan foto"></textarea>
                    <button type="button" data-remove-permanen-foto-row class="shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                `;
                list.appendChild(div);
            }
        });
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-permanen-foto-row]');
            if (!btn) return;
            btn.closest('.penanganan-permanen-foto-row')?.remove();
        });

        document.getElementById('tambah-dokumen')?.addEventListener('click', function () {
            const container = document.getElementById('dokumen-laporan-list');
            const div = document.createElement('div');
            div.className = 'dokumen-laporan-row flex items-center gap-4';
            div.innerHTML = `
                <button type="button" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm">Browse Dokumen</button>
                <input type="text" placeholder="Nama/Keterangan Dokumen" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <button type="button" data-remove-dokumen-row class="shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
            `;
            container.appendChild(div);
        });
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-dokumen-row]');
            if (!btn) return;
            btn.closest('.dokumen-laporan-row')?.remove();
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-row]');
            if (!btn) return;

            const row = btn.closest('.infrastruktur-row, .penanganan-row, .penanganan-permanen-row, .sumberdaya-alat-row, .sumberdaya-bahan-row, .pic-row');
            if (!row) return;

            if (row.classList.contains('sumberdaya-alat-row') || row.classList.contains('sumberdaya-bahan-row')) {
                row.remove();
                return;
            }

            const container = row.parentElement;
            const siblingsCount = container.querySelectorAll(':scope > ' + row.className.split(' ').map(c => '.' + c).join('')).length;

            if (siblingsCount > 1) {
                row.remove();
            }
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-foto-row]');
            if (!btn) return;

            const list = btn.closest('.penanganan-foto-list');
            const rows = list.querySelectorAll('.penanganan-foto-row');
            if (rows.length > 1) {
                btn.closest('.penanganan-foto-row').remove();
            }
        });
    });
</script>
@endunless