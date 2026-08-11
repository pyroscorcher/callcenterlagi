{{--
    Komponen: <x-balai.form-laporan-bencana :laporan="$laporan ?? null" :readonly="true|false" />
--}}

@props([
    'laporan' => null,
    'foto' => [],
    'provinsis' => [],
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
                <h2 class="font-bold text-gray-900 mb-4">Identitas Kejadian</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Bencana</label>
                        <select name="jenis_bencana" id="jenis_bencana" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Pilih Jenis Bencana</option>
                            @php
                                $jenisOptions = [
                                    'Kebakaran Gedung dan Pemukiman', 'Gagal Teknologi', 'Epidemi dan Wabah Penyakit',
                                    'Kekeringan', 'Tanah Longsor', 'Gempabumi', 'Banjir', 'Konflik Sosial',
                                    'Cuaca Ekstrim', 'Erupsi Gunung Api', 'Gelombang Pasang dan Abrasi',
                                    'Kebakaran Hutan dan Lahan', 'Tsunami'
                                ];
                            @endphp
                            @foreach($jenisOptions as $jenis)
                                <option value="{{ $jenis }}" @selected(($laporan->jenis_bencana ?? '') === $jenis)>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kejadian</label>
                        <select name="nama_bencana" id="nama_bencana" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100" disabled>
                            <option value="">Pilih Nama Kejadian</option>
                            <!-- Options will be populated by JS -->
                        </select>
                        {{-- Hidden input to store old value for JS initialization --}}
                        <input type="hidden" id="old_nama_bencana" value="{{ $laporan->nama_bencana ?? '' }}">
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
                    {!! $detailRow('Titik Kejadian', isset($laporan->lintang, $laporan->bujur) ? $laporan->lintang . ' , ' . $laporan->bujur : null) !!}
                    
                    <h3 class="font-semibold text-gray-800 mt-6 mb-2 border-b pb-1">Kewenangan Infrastruktur</h3>
                    {!! $detailRow('Tipe Kewenangan', ucfirst($laporan->kewenangan?->tipe ?? '-')) !!}
                    
                    @if(($laporan->kewenangan?->tipe ?? '') === 'balai')
                        {!! $detailRow('UNOR', $laporan->kewenangan->unor ?? null) !!}
                        {!! $detailRow('Nama Balai', $laporan->kewenangan->balai?->nama_balai ?? null) !!}
                        {!! $detailRow('Kepala Balai', $laporan->kewenangan->kepala ?? null) !!}
                        {!! $detailRow('Kontak', $laporan->kewenangan->kontak ?? null) !!}
                    @elseif(($laporan->kewenangan?->tipe ?? '') === 'delegasi')
                        {!! $detailRow('DAS dan WS', $laporan->kewenangan->das ?? null) !!}
                        {!! $detailRow('PCH & Intensitas', $laporan->kewenangan->pch ?? null) !!}
                        {!! $detailRow('Ruas Jalan', $laporan->kewenangan->ruas_jalan ?? null) !!}
                        {!! $detailRow('Instansi', $laporan->kewenangan->instansi ?? null) !!}
                        {!! $detailRow('Penanggung Jawab', $laporan->kewenangan->penanggung_jawab ?? null) !!}
                        {!! $detailRow('Telepon', $laporan->kewenangan->telepon ?? null) !!}
                    @endif
                @else
                    {{-- Hidden inputs for Edit Mode Hydration --}}
                    <input type="hidden" id="old_provinsi" value="{{ $laporan->provinsi_id ?? '' }}">
                    <input type="hidden" id="old_kabupaten" value="{{ $laporan->kabupaten_kota_id ?? '' }}">
                    <input type="hidden" id="old_kecamatan" value="{{ $laporan->kecamatan_id ?? '' }}">
                    <input type="hidden" id="old_kelurahan" value="{{ $laporan->kelurahan_id ?? '' }}">
                    
                    <input type="hidden" id="old_tipe_kewenangan" value="{{ $laporan->kewenangan?->tipe ?? '' }}">
                    <input type="hidden" id="old_unor" value="{{ $laporan->kewenangan?->unor ?? '' }}">
                    <input type="hidden" id="old_balai_id" value="{{ $laporan->kewenangan?->balai_id ?? '' }}">

                    {{-- 1. Wilayah Cascading --}}
                    <div class="grid grid-cols-3 gap-5 mb-5">
                        <select name="provinsi_id" id="provinsi" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis ?? [] as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->nama }}</option>
                            @endforeach
                        </select>
                        <select name="kabupaten_kota_id" id="kabupaten" class="rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100" disabled>
                            <option value="">Pilih Kab/Kota</option>
                        </select>
                        <select name="kecamatan_id" id="kecamatan" class="rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-5 mb-5">
                        <select name="kelurahan_id" id="kelurahan" class="rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100" disabled>
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Lokasi</label>
                    <input type="text" name="lokasi" placeholder="Detail Lokasi"
                           value="{{ $laporan->lokasi ?? '' }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-5">

                    <div class="grid grid-cols-2 gap-5 mb-8">
                        <input type="text" name="lintang" placeholder="Latitude (contoh: -6.200000)"
                               value="{{ $laporan->lintang ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <input type="text" name="bujur" placeholder="Longitude (contoh: 106.816666)"
                               value="{{ $laporan->bujur ?? '' }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>

                    {{-- 2. Kewenangan Infrastruktur --}}
                    <div class="p-5 border border-gray-200 rounded-xl bg-gray-50/50 mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kewenangan Infrastruktur</label>
                        <select name="tipe_kewenangan" id="tipe_kewenangan" class="w-full md:w-1/2 rounded-lg border border-gray-300 px-3 py-2 text-sm mb-5">
                            <option value="">Pilih Kewenangan</option>
                            <option value="balai">Balai</option>
                            <option value="delegasi">Delegasi</option>
                        </select>

                        {{-- Tipe 1: Balai --}}
                        <div id="wrapper_kewenangan_balai" class="hidden space-y-5 border-t border-gray-200 pt-5">
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">UNOR</label>
                                    <select name="unor" id="unor_balai" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Pilih UNOR</option>
                                        <option value="SDA">SDA</option>
                                        <option value="Binamarga">Bina Marga</option>
                                        <option value="Ciptakarya">Cipta Karya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Nama Balai</label>
                                    <select name="balai_id" id="balai_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100" disabled>
                                        <option value="">Pilih Balai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Kepala Balai</label>
                                    <input type="text" name="kepala" id="kepala_balai" value="{{ $laporan->kewenangan?->kepala ?? '' }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-600 cursor-not-allowed" placeholder="Otomatis terisi">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Kontak Balai</label>
                                    <input type="text" name="kontak" id="kontak_balai" value="{{ $laporan->kewenangan?->kontak ?? '' }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-600 cursor-not-allowed" placeholder="Otomatis terisi">
                                </div>
                            </div>
                        </div>

                        {{-- Tipe 2: Delegasi --}}
                        <div id="wrapper_kewenangan_delegasi" class="hidden space-y-5 border-t border-gray-200 pt-5">
                            <div class="grid grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">DAS dan WS</label>
                                    <input type="text" name="das" value="{{ $laporan->kewenangan?->das ?? '' }}" placeholder="Tambahkan Nama DAS/WS" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">PCH & Intensitas</label>
                                    <input type="text" name="pch" value="{{ $laporan->kewenangan?->pch ?? '' }}" placeholder="PCH & curah hujan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Ruas Jalan</label>
                                    <input type="text" name="ruas_jalan" value="{{ $laporan->kewenangan?->ruas_jalan ?? '' }}" placeholder="Nama/Nomor ruas jalan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Instansi</label>
                                    <input type="text" name="instansi" value="{{ $laporan->kewenangan?->instansi ?? '' }}" placeholder="Nama Instansi" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Penanggung Jawab</label>
                                    <input type="text" name="penanggung_jawab" value="{{ $laporan->kewenangan?->penanggung_jawab ?? '' }}" placeholder="Nama PIC" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Telepon</label>
                                    <input type="text" name="telepon" value="{{ $laporan->kewenangan?->telepon ?? '' }}" placeholder="Nomor Kontak" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>
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
                            <div class="flex gap-4 items-start items-center">
                                <input type="file" name="fotos[][file]" class="hidden foto-input" accept="image/*,video/*">
                                <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white hover:bg-gray-50 transition">Browse Image/Video</button>
                                <textarea name="fotos[][keterangan]" rows="2" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Keterangan foto">{{ $foto->keterangan ?? '' }}</textarea>
                                <button type="button" onclick="this.closest('div').remove()" class="{{ $loop->first ? 'hidden ' : '' }}shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                            </div>
                        @empty
                            <div class="flex gap-4 items-start items-center">
                                <input type="file" name="fotos[][file]" class="hidden foto-input" accept="image/*,video/*">
                                <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white hover:bg-gray-50 transition">Browse Image/Video</button>
                                <textarea name="fotos[][keterangan]" rows="2" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Keterangan foto"></textarea>
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
                    @forelse (($laporan->infrastrukturTerdampak ?? []) as $item)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Infrastruktur {{ $loop->iteration }}</p>
                            {!! $detailRow('Unit Organisasi', $item->unor ?? null) !!}
                            {!! $detailRow('Kategori Infrastruktur', $item->kategori ?? null) !!}
                            {!! $detailRow('Nama Infrastruktur', $item->nama ?? null) !!}
                            {!! $detailRow('Satuan', $item->satuan ?? null) !!}
                            {!! $detailRow('Jumlah', $item->jumlah ?? null) !!}
                            {!! $detailRow('Detail', $item->detail ?? null) !!}
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="infrastruktur-list" class="space-y-5">
                        @php
                            $existingInfrastruktur = $laporan?->infrastrukturTerdampak ?? collect();
                        @endphp

                        @forelse ($existingInfrastruktur as $item)
                            <div class="infrastruktur-row border border-gray-200 rounded-lg p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Infrastruktur</span>
                                    <button type="button" data-remove-row
                                            class="{{ $loop->first && $existingInfrastruktur->count() === 1 ? 'hidden ' : '' }}shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                                </div>
                                <div class="grid grid-cols-3 gap-5">
                                    <select name="infrastruktur[][unor]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Unit Organisasi</option>
                                        <option value="SDA" @selected(($item->unor ?? '') === 'SDA')>SDA</option>
                                        <option value="Bina Marga" @selected(($item->unor ?? '') === 'Bina Marga')>Bina Marga</option>
                                        <option value="Cipta Karya" @selected(($item->unor ?? '') === 'Cipta Karya')>Cipta Karya</option>
                                    </select>
                                    <input type="text" name="infrastruktur[][kategori]" value="{{ $item->kategori ?? '' }}" placeholder="Kategori Infrastruktur" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <input type="text" name="infrastruktur[][nama]" value="{{ $item->nama ?? '' }}" placeholder="Nama Infrastruktur" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div class="grid grid-cols-3 gap-5">
                                    <select name="infrastruktur[][satuan]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Satuan</option>
                                        <option value="Unit" @selected(($item->satuan ?? '') === 'Unit')>Unit</option>
                                        <option value="Meter" @selected(($item->satuan ?? '') === 'Meter')>Meter</option>
                                        <option value="Ha" @selected(($item->satuan ?? '') === 'Ha')>Ha</option>
                                        <option value="Lokasi" @selected(($item->satuan ?? '') === 'Lokasi')>Lokasi</option>
                                    </select>
                                    <input type="number" name="infrastruktur[][jumlah]" value="{{ $item->jumlah ?? '' }}" placeholder="Jumlah" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <input type="text" name="infrastruktur[][detail]" value="{{ $item->detail ?? '' }}" placeholder="Jalan tergenang/jembatan putus" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto/Video Dokumentasi</label>
                                    <input type="file" name="infrastruktur[][dokumentasi]" class="hidden">
                                    <button type="button" onclick="this.previousElementSibling.click()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white hover:bg-gray-50 transition">Browse Image/Video</button>
                                </div>
                            </div>
                        @empty
                            <div class="infrastruktur-row border border-gray-200 rounded-lg p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Infrastruktur</span>
                                    <button type="button" data-remove-row
                                            class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                                </div>
                                <div class="grid grid-cols-3 gap-5">
                                    <select name="infrastruktur[][unor]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Unit Organisasi</option>
                                        <option value="SDA">SDA</option>
                                        <option value="Bina Marga">Bina Marga</option>
                                        <option value="Cipta Karya">Cipta Karya</option>
                                    </select>
                                    <input type="text" name="infrastruktur[][kategori]" placeholder="Kategori Infrastruktur" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <input type="text" name="infrastruktur[][nama]" placeholder="Nama Infrastruktur" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div class="grid grid-cols-3 gap-5">
                                    <select name="infrastruktur[][satuan]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Satuan</option>
                                        <option value="Unit">Unit</option>
                                        <option value="Meter">Meter</option>
                                        <option value="Ha">Ha</option>
                                        <option value="Lokasi">Lokasi</option>
                                    </select>
                                    <input type="number" name="infrastruktur[][jumlah]" placeholder="Jumlah" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <input type="text" name="infrastruktur[][detail]" placeholder="Jalan tergenang/jembatan putus" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto/Video Dokumentasi</label>
                                    <input type="file" name="infrastruktur[][dokumentasi]" class="hidden">
                                    <button type="button" onclick="this.previousElementSibling.click()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white hover:bg-gray-50 transition">Browse Image/Video</button>
                                </div>
                            </div>
                        @endforelse
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
                    @forelse (($laporan->penangananSementara ?? []) as $p)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Penanganan {{ $loop->iteration }}</p>
                            {!! $detailRow('Tanggal', $p->tanggal?->format('Y-m-d') ?? null) !!}
                            {!! $detailRow('Kewenangan', $p->kewenangan ?? null) !!}
                            {!! $detailRow('Jumlah Personil', $p->jumlah_personil ?? null) !!}
                            {!! $detailRow('Keterangan / Deskripsi', $p->keterangan ?? null) !!}

                            @forelse (($p->foto ?? []) as $foto)
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
                        @php
                            $existingPenanganan = $laporan?->penangananSementara ?? collect();
                        @endphp

                        @forelse ($existingPenanganan as $p)
                            <div class="penanganan-row border border-gray-200 rounded-lg p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Penanganan Sementara</span>
                                    <button type="button" data-remove-row
                                            class="{{ $loop->first && $existingPenanganan->count() === 1 ? 'hidden ' : '' }}shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <input type="date" name="penanganan[][tanggal]" value="{{ $p->tanggal?->format('Y-m-d') ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <select name="penanganan[][kewenangan]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Pilih Kewenangan</option>
                                        <option value="Balai" @selected(($p->kewenangan ?? '') === 'Balai')>Balai</option>
                                        <option value="Pemerintah Daerah" @selected(($p->kewenangan ?? '') === 'Pemerintah Daerah')>Pemerintah Daerah</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-1 gap-5">
                                    <input type="number" name="penanganan[][jumlah_personil]" value="{{ $p->jumlah_personil ?? '' }}" placeholder="Jumlah Personil" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <textarea name="penanganan[][keterangan]" rows="3" placeholder="Deskripsi/Keterangan Penanganan"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ $p->keterangan ?? '' }}</textarea>

                                <div class="penanganan-foto-list space-y-4">
                                    <div class="penanganan-foto-row flex gap-4 items-start">
                                        <input type="file" name="penanganan_foto[][file]" class="hidden">
                                        <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">Browse Image/Video</button>
                                        <div class="w-40 space-y-2">
                                            <input type="text" name="penanganan_foto[][latitude]" placeholder="Latitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            <input type="text" name="penanganan_foto[][longitude]" placeholder="Longitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        </div>
                                        <textarea name="penanganan_foto[][keterangan]" rows="2" placeholder="description" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                                        <button type="button" data-remove-foto-row class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                                    </div>
                                </div>
                                <button type="button" data-add-foto class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50 transition">+ Tambah Foto/Lokasi</button>
                            </div>
                        @empty
                            <div class="penanganan-row border border-gray-200 rounded-lg p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Penanganan Sementara</span>
                                    <button type="button" data-remove-row class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <input type="date" name="penanganan[][tanggal]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <select name="penanganan[][kewenangan]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Pilih Kewenangan</option>
                                        <option value="Balai">Balai</option>
                                        <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-1 gap-5">
                                    <input type="number" name="penanganan[][jumlah_personil]" placeholder="Jumlah Personil" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <textarea name="penanganan[][keterangan]" rows="3" placeholder="Deskripsi/Keterangan Penanganan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>

                                <div class="penanganan-foto-list space-y-4">
                                    <div class="penanganan-foto-row flex gap-4 items-start">
                                        <input type="file" name="penanganan_foto[][file]" class="hidden">
                                        <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">Browse Image/Video</button>
                                        <div class="w-40 space-y-2">
                                            <input type="text" name="penanganan_foto[][latitude]" placeholder="Latitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            <input type="text" name="penanganan_foto[][longitude]" placeholder="Longitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        </div>
                                        <textarea name="penanganan_foto[][keterangan]" rows="2" placeholder="description" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                                        <button type="button" data-remove-foto-row class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                                    </div>
                                </div>
                                <button type="button" data-add-foto class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50 transition">+ Tambah Foto/Lokasi</button>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- ================= Sumberdaya ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Sumberdaya</h2>

                @if($readonly)
                    <p class="text-sm font-semibold text-gray-700 mt-4 mb-1">Alat & Bahan</p>
                    @forelse (($laporan->penangananSementara?->flatMap->alatDanBahan ?? []) as $item)
                        <div class="{{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
                            {!! $detailRow('Kategori / Kelas / Model', trim(($item->kategori ?? '-') . ' / ' . ($item->kelas ?? '-') . ' / ' . ($item->model ?? '-'))) !!}
                            {!! $detailRow('Jumlah', $item->jumlah ?? null) !!}
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div class="flex items-center gap-4 mb-5 text-sm">
                        <button type="button" id="tambah-sumberdaya" class="rounded-lg bg-blue-100 text-blue-700 px-4 py-2 font-medium hover:bg-blue-200 transition">+ Tambah Alat/Bahan</button>
                    </div>

                    <div id="sumberdaya-list" class="space-y-4 mb-4">
                        <div class="sumberdaya-row grid grid-cols-[1fr_1fr_1fr_1fr_auto] gap-4 items-center">
                            <input type="text" name="sumberdaya[][kategori]" placeholder="Kategori" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <input type="text" name="sumberdaya[][kelas]" placeholder="Kelas" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <input type="text" name="sumberdaya[][model]" placeholder="Model" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <input type="number" name="sumberdaya[][jumlah]" placeholder="Jumlah" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <button type="button" data-remove-sumberdaya class="hidden rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">−</button>
                        </div>
                    </div>

                    <script>
                        document.getElementById('tambah-sumberdaya')?.addEventListener('click', function () {
                            const container = document.getElementById('sumberdaya-list');
                            const firstRow = container.querySelector('.sumberdaya-row');
                            const clone = firstRow.cloneNode(true);
                            clone.querySelectorAll('input').forEach(el => el.value = '');
                            clone.querySelector('[data-remove-sumberdaya]').classList.remove('hidden');
                            container.appendChild(clone);
                        });

                        document.addEventListener('click', function (e) {
                            const btn = e.target.closest('[data-remove-sumberdaya]');
                            if (!btn) return;
                            
                            const row = btn.closest('.sumberdaya-row');
                            const container = document.getElementById('sumberdaya-list');
                            
                            if (container.querySelectorAll('.sumberdaya-row').length > 1) {
                                row.remove();
                            } else {
                                row.querySelectorAll('input').forEach(el => el.value = '');
                            }
                        });
                    </script>
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
                    @forelse (($laporan->penangananPermanen ?? []) as $pp)
                        <div class="{{ !$loop->last ? 'mb-5 pb-5 border-b border-gray-100' : '' }}">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Penanganan Permanen {{ $loop->iteration }}</p>
                            {!! $detailRow('Tanggal', $pp->tanggal?->format('d/m/Y') ?? null) !!}
                            {!! $detailRow('Kewenangan', $pp->kewenangan ?? null) !!}
                            {!! $detailRow('Deskripsi Penanganan', $pp->keterangan ?? null) !!}

                            @forelse (($pp->foto ?? []) as $foto)
                                <div class="pl-4 border-l-2 border-gray-100 mt-2">
                                    {!! $detailRow('Foto/Video ' . $loop->iteration, trim(($foto->latitude ?? '') . ', ' . ($foto->longitude ?? '') . ' — ' . ($foto->keterangan ?? '-'))) !!}
                                    @if($foto->foto)
                                        <div class="pl-56 mt-1">
                                            <a href="{{ Storage::url($foto->foto) }}" target="_blank" class="text-sm text-blue-600 hover:underline">Lihat File &rarr;</a>
                                        </div>
                                    @endif
                                </div>
                            @empty
                            @endforelse
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="penanganan-permanen-list" class="space-y-5">
                        @php
                            $existingPermanen = $laporan?->penangananPermanen ?? collect();
                        @endphp

                        @forelse ($existingPermanen as $pp)
                            <div class="penanganan-permanen-row border border-gray-200 rounded-lg p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Penanganan Permanen</span>
                                    <button type="button" data-remove-row
                                            class="{{ $loop->first && $existingPermanen->count() === 1 ? 'hidden ' : '' }}shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                                </div>

                                <div class="grid grid-cols-2 gap-5">
                                    <input type="date" name="penanganan_permanen[][tanggal]" value="{{ $pp->tanggal?->format('Y-m-d') ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <select name="penanganan_permanen[][kewenangan]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Pilih Kewenangan</option>
                                        <option value="Balai" @selected(($pp->kewenangan ?? '') === 'Balai')>Balai</option>
                                        <option value="Pemerintah Daerah" @selected(($pp->kewenangan ?? '') === 'Pemerintah Daerah')>Pemerintah Daerah</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Penanganan</label>
                                    <textarea name="penanganan_permanen[][keterangan]" rows="4"
                                            placeholder="(a. Memobilisasi alat berat berupa ... untuk normalisasi sungai ...)"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ $pp->keterangan ?? '' }}</textarea>
                                </div>

                                <div class="penanganan-permanen-foto-list space-y-4">
                                    {{-- Loop existing photos if editing --}}
                                    @forelse (($pp->foto ?? []) as $foto)
                                        <div class="penanganan-permanen-foto-row flex gap-4 items-start">
                                            <input type="hidden" name="penanganan_permanen_foto[][id]" value="{{ $foto->id }}">
                                            <input type="file" name="penanganan_permanen_foto[][file]" class="hidden" accept="image/*,video/*" onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'Browse Image/Video'">
                                            <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white w-48 overflow-hidden text-ellipsis whitespace-nowrap text-left">
                                                <span>{{ $foto->foto ? basename($foto->foto) : 'Browse Image/Video' }}</span>
                                            </button>
                                            <div class="w-40 space-y-2">
                                                <input type="text" name="penanganan_permanen_foto[][latitude]" value="{{ $foto->latitude ?? '' }}" placeholder="Latitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                <input type="text" name="penanganan_permanen_foto[][longitude]" value="{{ $foto->longitude ?? '' }}" placeholder="Longitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            </div>
                                            <textarea name="penanganan_permanen_foto[][keterangan]" rows="2" placeholder="Keterangan foto" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ $foto->keterangan ?? '' }}</textarea>
                                            <button type="button" data-remove-permanen-foto-row class="shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                                        </div>
                                    @empty
                                        <div class="penanganan-permanen-foto-row flex gap-4 items-start">
                                            <input type="hidden" name="penanganan_permanen_foto[][id]" value="">
                                            <input type="file" name="penanganan_permanen_foto[][file]" class="hidden" accept="image/*,video/*" onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'Browse Image/Video'">
                                            <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white w-48 overflow-hidden text-ellipsis whitespace-nowrap text-left">
                                                <span>Browse Image/Video</span>
                                            </button>
                                            <div class="w-40 space-y-2">
                                                <input type="text" name="penanganan_permanen_foto[][latitude]" placeholder="Latitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                <input type="text" name="penanganan_permanen_foto[][longitude]" placeholder="Longitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            </div>
                                            <textarea name="penanganan_permanen_foto[][keterangan]" rows="2" placeholder="Keterangan foto" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                                            <button type="button" data-remove-permanen-foto-row class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <button type="button" data-add-foto-permanen
                                        class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                                    + Tambah Foto/Video
                                </button>
                            </div>
                        @empty
                            <div class="penanganan-permanen-row border border-gray-200 rounded-lg p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Penanganan Permanen</span>
                                    <button type="button" data-remove-row
                                            class="hidden shrink-0 rounded-lg bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition">−</button>
                                </div>

                                <div class="grid grid-cols-2 gap-5">
                                    <input type="date" name="penanganan_permanen[][tanggal]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <select name="penanganan_permanen[][kewenangan]" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="">Pilih Kewenangan</option>
                                        <option value="Balai">Balai</option>
                                        <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Penanganan</label>
                                    <textarea name="penanganan_permanen[][keterangan]" rows="4"
                                            placeholder="(a. Memobilisasi alat berat berupa ... untuk normalisasi sungai ...)"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                                </div>

                                <div class="penanganan-permanen-foto-list space-y-4">
                                    <div class="penanganan-permanen-foto-row flex gap-4 items-start">
                                        <input type="hidden" name="penanganan_permanen_foto[][id]" value="">
                                        <input type="file" name="penanganan_permanen_foto[][file]" class="hidden" accept="image/*,video/*" onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'Browse Image/Video'">
                                        <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white w-48 overflow-hidden text-ellipsis whitespace-nowrap text-left">
                                            <span>Browse Image/Video</span>
                                        </button>
                                        <div class="w-40 space-y-2">
                                            <input type="text" name="penanganan_permanen_foto[][latitude]" placeholder="Latitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            <input type="text" name="penanganan_permanen_foto[][longitude]" placeholder="Longitude" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        </div>
                                        <textarea name="penanganan_permanen_foto[][keterangan]" rows="2" placeholder="Keterangan foto" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                                        <button type="button" data-remove-permanen-foto-row class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                                    </div>
                                </div>
                                
                                <button type="button" data-add-foto-permanen
                                        class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                                    + Tambah Foto/Video
                                </button>
                            </div>
                        @endforelse
                    </div>

                    {{-- Script Khusus Penanganan Permanen --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Handle Add Photo Row
                            document.addEventListener('click', function (e) {
                                if (e.target.closest('[data-add-foto-permanen]')) {
                                    const row = e.target.closest('.penanganan-permanen-row');
                                    const list = row.querySelector('.penanganan-permanen-foto-list');
                                    const firstRow = list.querySelector('.penanganan-permanen-foto-row');
                                    
                                    const clone = firstRow.cloneNode(true);
                                    
                                    // Clear inputs
                                    clone.querySelectorAll('input, textarea').forEach(el => el.value = '');
                                    // Reset button text
                                    const span = clone.querySelector('button span');
                                    if (span) span.textContent = 'Browse Image/Video';
                                    // Show remove button
                                    clone.querySelector('[data-remove-permanen-foto-row]').classList.remove('hidden');
                                    
                                    list.appendChild(clone);
                                }
                            });

                            // Handle Remove Photo Row
                            document.addEventListener('click', function (e) {
                                const btn = e.target.closest('[data-remove-permanen-foto-row]');
                                if (!btn) return;
                                
                                const list = btn.closest('.penanganan-permanen-foto-list');
                                const rows = list.querySelectorAll('.penanganan-permanen-foto-row');
                                
                                if (rows.length > 1) {
                                    btn.closest('.penanganan-permanen-foto-row').remove();
                                } else {
                                    // If it's the last row, just clear the data instead of deleting the element
                                    const row = btn.closest('.penanganan-permanen-foto-row');
                                    row.querySelectorAll('input, textarea').forEach(el => el.value = '');
                                    const span = row.querySelector('button span');
                                    if (span) span.textContent = 'Browse Image/Video';
                                }
                            });
                        });
                    </script>
                @endif
            </div>

            {{-- ================= Status Terkini ================= --}}
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
                    @forelse (($laporan->dokumenLaporanPimpinan ?? []) as $dok)
                        <div class="{{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
                            {!! $detailRow('Dokumen ' . $loop->iteration, $dok->nama_dokumen ?? '-') !!}
                            {!! $detailRow('Deskripsi', $dok->deskripsi ?? '-') !!}
                            @if($dok->file_path)
                                <div class="pl-56 mt-1">
                                    <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="text-sm text-blue-600 hover:underline">Lihat Dokumen &rarr;</a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endforelse
                @else
                    <div id="dokumen-laporan-list" class="space-y-4 mb-4">
                        @php
                            $existingDokumen = $laporan?->dokumenLaporanPimpinan ?? collect();
                        @endphp

                        @forelse ($existingDokumen as $dok)
                            <div class="dokumen-laporan-row flex items-start gap-4">
                                <input type="hidden" name="dokumen[][id]" value="{{ $dok->id }}">
                                <input type="file" name="dokumen[][file]" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx" 
                                    onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'Browse Dokumen'">
                                <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white w-48 overflow-hidden text-ellipsis whitespace-nowrap text-left hover:bg-gray-50 transition">
                                    <span>{{ $dok->file_path ? basename($dok->file_path) : 'Browse Dokumen' }}</span>
                                </button>
                                
                                <div class="flex-1 space-y-2">
                                    <input type="text" name="dokumen[][nama_dokumen]" value="{{ $dok->nama_dokumen ?? '' }}" placeholder="Nama Dokumen" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <input type="text" name="dokumen[][deskripsi]" value="{{ $dok->deskripsi ?? '' }}" placeholder="Deskripsi Singkat" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>

                                <button type="button" data-remove-dokumen-row 
                                        class="{{ $loop->first && $existingDokumen->count() === 1 ? 'hidden ' : '' }}shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                            </div>
                        @empty
                            <div class="dokumen-laporan-row flex items-start gap-4">
                                <input type="hidden" name="dokumen[][id]" value="">
                                <input type="file" name="dokumen[][file]" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx" 
                                    onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'Browse Dokumen'">
                                <button type="button" onclick="this.previousElementSibling.click()" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white w-48 overflow-hidden text-ellipsis whitespace-nowrap text-left hover:bg-gray-50 transition">
                                    <span>Browse Dokumen</span>
                                </button>
                                
                                <div class="flex-1 space-y-2">
                                    <input type="text" name="dokumen[][nama_dokumen]" placeholder="Nama Dokumen" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <input type="text" name="dokumen[][deskripsi]" placeholder="Deskripsi Singkat" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>

                                <button type="button" data-remove-dokumen-row 
                                        class="hidden shrink-0 rounded-lg bg-red-500 text-white w-9 h-9 flex items-center justify-center hover:bg-red-600 transition">×</button>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" id="btn-tambah-dokumen"
                            class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                        + Tambah Dokumen
                    </button>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Add Row Logic
                            document.getElementById('btn-tambah-dokumen')?.addEventListener('click', function () {
                                const container = document.getElementById('dokumen-laporan-list');
                                const firstRow = container.querySelector('.dokumen-laporan-row');
                                const clone = firstRow.cloneNode(true);
                                
                                // Clear inputs (text, hidden IDs, files)
                                clone.querySelectorAll('input').forEach(el => el.value = '');
                                
                                // Reset preview text back to default
                                const span = clone.querySelector('button span');
                                if (span) span.textContent = 'Browse Dokumen';
                                
                                // Show remove button
                                clone.querySelector('[data-remove-dokumen-row]').classList.remove('hidden');
                                
                                container.appendChild(clone);
                            });

                            // Remove Row Logic
                            document.addEventListener('click', function (e) {
                                const btn = e.target.closest('[data-remove-dokumen-row]');
                                if (!btn) return;
                                
                                const list = document.getElementById('dokumen-laporan-list');
                                const rows = list.querySelectorAll('.dokumen-laporan-row');
                                
                                if (rows.length > 1) {
                                    btn.closest('.dokumen-laporan-row').remove();
                                } else {
                                    // Clear the row if it's the last one left
                                    const row = btn.closest('.dokumen-laporan-row');
                                    row.querySelectorAll('input').forEach(el => el.value = '');
                                    const span = row.querySelector('button span');
                                    if (span) span.textContent = 'Browse Dokumen';
                                }
                            });
                        });
                    </script>
                @endif
            </div>

            {{-- ================= Dilaporkan Oleh (PIC) ================= --}}
            <div>
                <h2 class="font-bold text-gray-900 mb-4">Dilaporkan oleh (PIC)</h2>

                @if($readonly)
                    @php $pic = $laporan->picBencana; @endphp
                    
                    @if($pic)
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            @if($pic->isExternalPic())
                                {!! $detailRow('PIC Eksternal (Lainnya)', $pic->pic_lainnya ?? '-') !!}
                            @else
                                {!! $detailRow('Unit Organisasi', $pic->balai->unor ?? '-') !!}
                                {!! $detailRow('Balai', $pic->balai->nama_balai ?? '-') !!}
                                {!! $detailRow('Nama PIC', $pic->nama_pic ?? '-') !!}
                                {!! $detailRow('No. HP / Kontak', $pic->kontak ?? '-') !!}
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Tidak ada data</p>
                    @endif
                    
                @else
                    @php $pic = $laporan?->picBencana; @endphp
                    
                    <div class="border border-gray-200 rounded-lg p-5 space-y-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Data PIC</span>
                        
                        {{-- Filter Wilayah/Balai --}}
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Unit Organisasi (UNOR)</label>
                                <select id="unor_pic" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">Pilih UNOR</option>
                                    <option value="SDA">SDA</option>
                                    <option value="Binamarga">Bina Marga</option>
                                    <option value="Ciptakarya">Cipta Karya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Balai</label>
                                <select name="pic[balai_id]" id="balai_pic" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100" disabled>
                                    <option value="">Pilih Balai</option>
                                </select>
                                
                                {{-- Hidden inputs for Edit Mode Hydration --}}
                                <input type="hidden" id="old_pic_unor" value="{{ $pic->balai->unor ?? '' }}">
                                <input type="hidden" id="old_pic_balai_id" value="{{ $pic->balai_id ?? '' }}">
                            </div>
                        </div>

                        {{-- Auto-filled inputs based on Balai --}}
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Nama PIC (Kepala Balai)</label>
                                <input type="text" name="pic[nama_pic]" id="nama_pic" value="{{ $pic->nama_pic ?? '' }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-600 cursor-not-allowed" placeholder="Otomatis terisi">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">No. HP / Kontak</label>
                                <input type="text" name="pic[kontak]" id="kontak_pic" value="{{ $pic->kontak ?? '' }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-600 cursor-not-allowed" placeholder="Otomatis terisi">
                            </div>
                        </div>

                        {{-- Fallback PIC Eksternal --}}
                        <div class="pt-3 border-t border-gray-100">
                            <label class="block text-xs text-gray-500 mb-1">PIC Lainnya (Opsional / Eksternal)</label>
                            <input type="text" name="pic[pic_lainnya]" value="{{ $pic->pic_lainnya ?? '' }}" placeholder="Masukkan nama jika PIC bukan dari daftar Balai" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    </div>

                    {{-- Script Khusus Autofill PIC --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const unorPic = document.getElementById('unor_pic');
                            const balaiPic = document.getElementById('balai_pic');
                            const namaPic = document.getElementById('nama_pic');
                            const kontakPic = document.getElementById('kontak_pic');

                            const oldPicUnor = document.getElementById('old_pic_unor')?.value;
                            const oldPicBalaiId = document.getElementById('old_pic_balai_id')?.value;

                            let picBalaiDataMap = {};

                            async function fetchPicBalaisByUnor(unorValue, selectedId = null) {
                                if (!balaiPic) return;
                                balaiPic.innerHTML = '<option value="">Loading...</option>';
                                balaiPic.disabled = true;
                                namaPic.value = '';
                                kontakPic.value = '';
                                picBalaiDataMap = {};

                                if (!unorValue) {
                                    balaiPic.innerHTML = '<option value="">Pilih Balai</option>';
                                    return;
                                }

                                try {
                                    const response = await fetch('/ajax/balai-by-unor/' + unorValue);
                                    const data = await response.json();

                                    balaiPic.innerHTML = '<option value="">Pilih Balai</option>';
                                    data.forEach(item => {
                                        picBalaiDataMap[item.id] = item;
                                        const isSelected = (selectedId && String(selectedId) === String(item.id)) ? 'selected' : '';
                                        balaiPic.innerHTML += `<option value="${item.id}" ${isSelected}>${item.nama_balai}</option>`;
                                    });
                                    balaiPic.disabled = false;

                                    // Trigger change if we selected a specific Balai on load
                                    if (selectedId) balaiPic.dispatchEvent(new Event('change'));
                                } catch (error) {
                                    balaiPic.innerHTML = '<option value="">Gagal memuat data</option>';
                                }
                            }

                            if (unorPic) {
                                unorPic.addEventListener('change', function() {
                                    fetchPicBalaisByUnor(this.value);
                                });

                                balaiPic.addEventListener('change', function() {
                                    const selected = picBalaiDataMap[this.value];
                                    if (selected) {
                                        namaPic.value = selected.kepala ?? '-';
                                        kontakPic.value = selected.kontak ?? '-';
                                    } else {
                                        namaPic.value = '';
                                        kontakPic.value = '';
                                    }
                                });

                                // Edit Mode Hydration
                                if (oldPicUnor) {
                                    unorPic.value = oldPicUnor;
                                    fetchPicBalaisByUnor(oldPicUnor, oldPicBalaiId);
                                }
                            }
                        });
                    </script>
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

        // ==========================================
        // 1. ADD / REMOVE DYNAMIC ROWS LOGIC
        // ==========================================
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


        // ==========================================
        // 2. JENIS BENCANA -> NAMA BENCANA
        // ==========================================
        const jenisBencanaSelect = document.getElementById('jenis_bencana');
        const namaBencanaSelect = document.getElementById('nama_bencana');
        const oldNamaBencana = document.getElementById('old_nama_bencana')?.value;

        if (jenisBencanaSelect && namaBencanaSelect) {
            const bencanaData = {
                "Gagal Teknologi": ["Kegagalan Industri", "Kecelakaan Industri"],
                "Epidemi dan Wabah Penyakit": ["Epidemi", "Wabah Penyakit"],
                "Kekeringan": ["Kekeringan Meteorologis", "Kekeringan Hidrologis", "Kekeringan Pertanian"],
                "Tanah Longsor": ["Longsor", "Gerakan Tanah"],
                "Gempabumi": ["Gempa Tektonik", "Gempa Vulkanik", "Gempabumi Runtuhan"],
                "Banjir": ["Banjir dan Tanah Longsor", "Banjir Genangan", "Banjir Bandang", "Banjir Drainase & Selokan", "Banjir Waduk", "Tanggul Jebol"],
                "Konflik Sosial": ["Teror", "Kerusakan Sosial", "Konflik Sosial"],
                "Cuaca Ekstrim": ["Angin Topan", "Hujan Es", "Siklon Tropis", "Puting Beliung", "Angin Kencang", "Suhu Udara Ekstrem"],
                "Erupsi Gunung Api": ["Banjir Lahar", "Hujan Abu Vulkanik", "Awan Panas Aliran Piroklastik Guguran", "Awan Panas Aliran Piroklastik", "Gas Vulkanik Beracun"],
                "Gelombang Pasang dan Abrasi": ["Gelombang Pasang", "Abrasi"],
                "Kebakaran Hutan dan Lahan": ["Kebakaran Hutan", "Kebakaran Lahan", "Kebakaran Lahan Gambut"],
                "Tsunami": ["Mikrotsunami", "Tsunami Sesimogenik", "Tsunami Nonseismik", "Tsunami Lokal", "Tsunami Regional", "Tsunami Jarak", "Tsunami Meteorologi"]
            };

            function populateNamaBencana(selectedJenis, selectedNama = '') {
                namaBencanaSelect.innerHTML = '<option value="">Pilih Nama Kejadian</option>';
                if (selectedJenis && bencanaData[selectedJenis]) {
                    namaBencanaSelect.disabled = false;
                    bencanaData[selectedJenis].forEach(function(nama) {
                        const option = document.createElement('option');
                        option.value = nama;
                        option.textContent = nama;
                        if (nama === selectedNama) option.selected = true;
                        namaBencanaSelect.appendChild(option);
                    });
                } else if (selectedJenis === "Kebakaran Gedung dan Pemukiman") {
                    namaBencanaSelect.disabled = false;
                    const option = document.createElement('option');
                    option.value = selectedJenis;
                    option.textContent = selectedJenis;
                    option.selected = true;
                    namaBencanaSelect.appendChild(option);
                } else {
                    namaBencanaSelect.disabled = true;
                }
            }

            jenisBencanaSelect.addEventListener('change', function (e) {
                populateNamaBencana(e.target.value);
            });

            if (jenisBencanaSelect.value) {
                populateNamaBencana(jenisBencanaSelect.value, oldNamaBencana);
            }
        }


        // ==========================================
        // 3. LOKASI CASCADING & KEWENANGAN AJAX
        // ==========================================
        
        // Element selectors
        const provinsi = document.getElementById('provinsi');
        const kabupaten = document.getElementById('kabupaten');
        const kecamatan = document.getElementById('kecamatan');
        const kelurahan = document.getElementById('kelurahan');

        const tipeKewenangan = document.getElementById('tipe_kewenangan');
        const wrapBalai = document.getElementById('wrapper_kewenangan_balai');
        const wrapDelegasi = document.getElementById('wrapper_kewenangan_delegasi');
        const unorSelect = document.getElementById('unor_balai');
        const balaiSelect = document.getElementById('balai_id');
        const kepalaInput = document.getElementById('kepala_balai');
        const kontakInput = document.getElementById('kontak_balai');

        let balaiDataMap = {}; // Cache for auto-filling kepala & kontak

        if(provinsi) {
            // A. Wilayah Cascading Events
            provinsi.addEventListener('change', async function () {
                kabupaten.innerHTML = '<option value="">Loading...</option>';
                kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
                kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                kabupaten.disabled = true; kecamatan.disabled = true; kelurahan.disabled = true;

                if (!this.value) {
                    kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    return;
                }

                const response = await fetch('/ajax/kabupaten/' + this.value);

                if (!response.ok) {
                    alert('Gagal mengambil data wilayah. Pastikan Anda memiliki akses.');
                    kabupaten.innerHTML = '<option value="">Gagal memuat data</option>';
                    return;
                }

                const data = await response.json();
                
                kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                data.forEach(item => {
                    kabupaten.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
                });
                kabupaten.disabled = false;
            });

            kabupaten.addEventListener('change', async function () {
                kecamatan.innerHTML = '<option value="">Loading...</option>';
                kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                kecamatan.disabled = true; kelurahan.disabled = true;

                if (!this.value) {
                    kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    return;
                }

                const response = await fetch('/ajax/kecamatan/' + this.value);
                const data = await response.json();

                kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
                data.forEach(item => {
                    kecamatan.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
                });
                kecamatan.disabled = false;
            });

            kecamatan.addEventListener('change', async function () {
                kelurahan.innerHTML = '<option value="">Loading...</option>';
                kelurahan.disabled = true;

                if (!this.value) {
                    kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                    return;
                }

                const response = await fetch('/ajax/kelurahan/' + this.value);
                const data = await response.json();

                kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                data.forEach(item => {
                    kelurahan.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
                });
                kelurahan.disabled = false;
            });
        }

        if(tipeKewenangan) {
            // B. Kewenangan UI Toggler
            tipeKewenangan.addEventListener('change', function () {
                wrapBalai.classList.add('hidden');
                wrapDelegasi.classList.add('hidden');

                if (this.value === 'balai') wrapBalai.classList.remove('hidden');
                if (this.value === 'delegasi') wrapDelegasi.classList.remove('hidden');
            });

            // C. Kewenangan AJAX (UNOR -> Balai)
            async function fetchBalaisByUnor(unorValue, selectedId = null) {
                balaiSelect.innerHTML = '<option value="">Loading...</option>';
                balaiSelect.disabled = true;
                kepalaInput.value = '';
                kontakInput.value = '';
                balaiDataMap = {};

                if (!unorValue) {
                    balaiSelect.innerHTML = '<option value="">Pilih Balai</option>';
                    return;
                }

                const response = await fetch('/ajax/balai-by-unor/' + unorValue);
                const data = await response.json();

                balaiSelect.innerHTML = '<option value="">Pilih Balai</option>';
                data.forEach(item => {
                    balaiDataMap[item.id] = item; 
                    const isSelected = (selectedId && String(selectedId) === String(item.id)) ? 'selected' : '';
                    balaiSelect.innerHTML += `<option value="${item.id}" ${isSelected}>${item.nama_balai}</option>`;
                });
                balaiSelect.disabled = false;

                if (selectedId) balaiSelect.dispatchEvent(new Event('change'));
            }

            unorSelect.addEventListener('change', function() {
                fetchBalaisByUnor(this.value);
            });

            balaiSelect.addEventListener('change', function() {
                const selected = balaiDataMap[this.value];
                if (selected) {
                    kepalaInput.value = selected.kepala ?? '-';
                    kontakInput.value = selected.kontak ?? '-';
                } else {
                    kepalaInput.value = '';
                    kontakInput.value = '';
                }
            });
        }

        // ==========================================
        // 4. EDIT MODE HYDRATION (Run on Load)
        // ==========================================
        const oldProv = document.getElementById('old_provinsi')?.value;
        const oldKab = document.getElementById('old_kabupaten')?.value;
        const oldKec = document.getElementById('old_kecamatan')?.value;
        const oldKel = document.getElementById('old_kelurahan')?.value;
        
        const oldTipeKewenangan = document.getElementById('old_tipe_kewenangan')?.value;
        const oldUnor = document.getElementById('old_unor')?.value;
        const oldBalaiId = document.getElementById('old_balai_id')?.value;

        // A. Hydrate Locations sequentially to respect async loads
        if (oldProv && provinsi) {
            provinsi.value = oldProv;
            fetch('/ajax/kabupaten/' + oldProv)
                .then(res => res.json())
                .then(data => {
                    kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    data.forEach(i => kabupaten.innerHTML += `<option value="${i.id}">${i.nama}</option>`);
                    kabupaten.value = oldKab;
                    kabupaten.disabled = false;

                    if (oldKab) {
                        return fetch('/ajax/kecamatan/' + oldKab).then(res => res.json());
                    }
                })
                .then(data => {
                    if (data) {
                        kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        data.forEach(i => kecamatan.innerHTML += `<option value="${i.id}">${i.nama}</option>`);
                        kecamatan.value = oldKec;
                        kecamatan.disabled = false;

                        if (oldKec) {
                            return fetch('/ajax/kelurahan/' + oldKec).then(res => res.json());
                        }
                    }
                })
                .then(data => {
                    if (data) {
                        kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                        data.forEach(i => kelurahan.innerHTML += `<option value="${i.id}">${i.nama}</option>`);
                        kelurahan.value = oldKel;
                        kelurahan.disabled = false;
                    }
                });
        }

        // B. Hydrate Kewenangan UI
        if (oldTipeKewenangan && tipeKewenangan) {
            tipeKewenangan.value = oldTipeKewenangan;
            tipeKewenangan.dispatchEvent(new Event('change'));

            if (oldTipeKewenangan === 'balai' && oldUnor) {
                unorSelect.value = oldUnor;
                fetchBalaisByUnor(oldUnor, oldBalaiId); 
            }
        }
    });
</script>
@endunless