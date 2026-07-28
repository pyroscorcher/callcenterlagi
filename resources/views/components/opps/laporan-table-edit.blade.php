@props([
    'laporan',
    'provinsis',
    'kabupatenkotas',
    'kecamatans',
    'kelurahans',
])

<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <a href="{{ route('laporan.masuk-bencana') }}" class="hover:underline">Laporan Masuk Bencana</a>
        <span class="mx-2 text-white/50">/</span>
        <a href="{{ route('laporan.show', $laporan->id) }}" class="hover:underline">Detail Laporan</a>
        <span class="mx-2 text-white/50">/</span>
        <span class="font-bold">Edit Laporan</span>
    </div>

    {{-- Card Form --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg font-bold text-gray-900">Edit Laporan Bencana</h1>
        </div>

        {{-- Pastikan enctype bernilai multipart/form-data karena ada upload file/foto --}}
        <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                
                {{-- Jenis Bencana --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="jenis_bencana" class="text-gray-700 font-medium">Jenis Bencana</label>
                    <div>
                        <input type="text" name="jenis_bencana" id="jenis_bencana" 
                               value="{{ old('jenis_bencana', $laporan->jenis_bencana) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('jenis_bencana')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Nama Kejadian --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="nama_bencana" class="text-gray-700 font-medium">Nama Kejadian</label>
                    <div>
                        <input type="text" name="nama_bencana" id="nama_bencana" 
                               value="{{ old('nama_bencana', $laporan->nama_bencana) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('nama_bencana')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal / Waktu Kejadian --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="waktu_kejadian" class="text-gray-700 font-medium">Tanggal Kejadian</label>
                    <div>
                        <input type="text" name="waktu_kejadian" id="waktu_kejadian" 
                               value="{{ old('waktu_kejadian', $laporan->waktu_kejadian) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('waktu_kejadian')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- No WhatsApp --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="telepon" class="text-gray-700 font-medium">No WhatsApp</label>
                    <div>
                        <input type="text" name="telepon" id="telepon" 
                               value="{{ old('telepon', $laporan->telepon) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('telepon')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Lokasi Kejadian (Telah diperbaiki error HTML-nya) --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="lokasi" class="text-gray-700 font-medium">Lokasi Kejadian</label>
                    <div>
                        <input type="text" name="lokasi" id="lokasi" 
                               value="{{ old('lokasi', $laporan->lokasi) }}" 
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('lokasi')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Provinsi --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="provinsi" class="text-gray-700 font-medium">Provinsi</label>
                    <div>
                        <select id="provinsi" name="provinsi_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}" @selected($laporan->provinsi_id == $provinsi->id)>
                                    {{ $provinsi->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('provinsi_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kabupaten/Kota --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="kabupaten" class="text-gray-700 font-medium">Kabupaten/Kota</label>
                    <div>
                        <select id="kabupaten" name="kabupaten_kota_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">Pilih Kabupaten/Kota</option>
                            @if(isset($kabupatenkotas))
                                @foreach($kabupatenkotas as $kabupaten)
                                    <option value="{{ $kabupaten->id }}" @selected($laporan->kabupaten_kota_id == $kabupaten->id)>
                                        {{ $kabupaten->nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('kabupaten_kota_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kecamatan --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="kecamatan" class="text-gray-700 font-medium">Kecamatan</label>
                    <div>
                        <select id="kecamatan" name="kecamatan_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">Pilih Kecamatan</option>
                            @if(isset($kecamatans))
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->id }}" @selected($laporan->kecamatan_id == $kecamatan->id)>
                                        {{ $kecamatan->nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('kecamatan_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kelurahan --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="kelurahan" class="text-gray-700 font-medium">Kelurahan</label>
                    <div>
                        <select id="kelurahan" name="kelurahan_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">Pilih Kelurahan</option>
                            @if(isset($kelurahans))
                                @foreach($kelurahans as $kelurahan)
                                    <option value="{{ $kelurahan->id }}" @selected($laporan->kelurahan_id == $kelurahan->id)>
                                        {{ $kelurahan->nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('kelurahan_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Titik Kejadian (Lintang & Bujur) --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <dt class="text-gray-700 font-medium">Titik Kejadian</dt>
                    <dd class="text-gray-900 flex items-center justify-between">
                        <span>{{ $laporan->lintang ?? '-' }} , {{ $laporan->bujur ?? '-' }}</span>
                        <a href="{{ route('laporan.edit-lokasi', $laporan->id) }}" 
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Edit Titik Lokasi
                        </a>
                    </dd>
                </div>
                
                {{-- Dampak Bencana --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="dampak_bencana" class="text-gray-700 font-medium">Dampak Bencana</label>
                    <div>
                        <input type="text" name="dampak_bencana" id="dampak_bencana" 
                               value="{{ old('dampak_bencana', $laporan->dampak_bencana) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('dampak_bencana')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Manajemen Foto Bencana (Multiple Foto) --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 pt-4 border-t border-gray-200">
                    <span class="text-gray-700 font-medium">Foto Bencana Saat Ini</span>
                    <div>
                        @if ($laporan->fotos && $laporan->fotos->count() > 0)
                            <div class="flex flex-wrap gap-4 mb-4">
                                @foreach ($laporan->fotos as $foto)
                                    <div class="w-48 rounded-xl border border-gray-200 bg-white p-3 relative group">
                                        <img src="{{ Storage::disk('public')->url($foto->file_path) }}" 
                                             alt="Foto Bencana" 
                                             class="w-full h-32 object-cover rounded-lg mb-2" />
                                        
                                        {{-- Checkbox untuk menghapus foto tertentu --}}
                                        <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer mt-1 font-medium">
                                            <input type="checkbox" name="hapus_foto[]" value="{{ $foto->id }}" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                            Hapus foto ini
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-3">Belum ada foto yang diunggah.</p>
                        @endif

                        {{-- Input Tambah Foto Baru --}}
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Foto Baru (Bisa pilih lebih dari satu)</label>
                        <input type="file" name="fotos[]" multiple accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#161446] file:text-white hover:file:bg-[#110e36] cursor-pointer" />
                        @error('fotos.*')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kronologi Bencana (Deskripsi) --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-start pt-4 border-t border-gray-200">
                    <label for="deskripsi" class="text-gray-700 font-medium pt-2">Kronologi Bencana</label>
                    <div>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kebutuhan Mendesak --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-start">
                    <label for="kebutuhan_mendesak" class="text-gray-700 font-medium pt-2">Kebutuhan Mendesak</label>
                    <div>
                        <textarea name="kebutuhan_mendesak" id="kebutuhan_mendesak" rows="3"
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">{{ old('kebutuhan_mendesak', $laporan->kebutuhan_mendesak) }}</textarea>
                        @error('kebutuhan_mendesak')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Footer Action Buttons --}}
            <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                <a href="{{ route('laporan.show', $laporan->id) }}"
                   class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition">
                    Batal
                </a>

                <button type="submit"
                        class="rounded-lg bg-[#161446] px-6 py-2.5 text-white font-medium hover:bg-[#110e36] transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const provinsi = document.getElementById('provinsi');
    const kabupaten = document.getElementById('kabupaten');
    const kecamatan = document.getElementById('kecamatan');
    const kelurahan = document.getElementById('kelurahan');

    provinsi.addEventListener('change', async function () {
        kabupaten.innerHTML = '<option>Loading...</option>';
        kecamatan.innerHTML = '<option>Pilih Kecamatan</option>';
        kelurahan.innerHTML = '<option>Pilih Kelurahan</option>';

        const response = await fetch('/ajax/kabupaten/' + this.value);
        const data = await response.json();

        kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        data.forEach(item => {
            kabupaten.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
        });
    });

    kabupaten.addEventListener('change', async function () {
        kecamatan.innerHTML = '<option>Loading...</option>';
        kelurahan.innerHTML = '<option>Pilih Kelurahan</option>';

        const response = await fetch('/ajax/kecamatan/' + this.value);
        const data = await response.json();

        kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
        data.forEach(item => {
            kecamatan.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
        });
    });

    kecamatan.addEventListener('change', async function () {
        kelurahan.innerHTML = '<option>Loading...</option>';

        const response = await fetch('/ajax/kelurahan/' + this.value);
        const data = await response.json();

        kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
        data.forEach(item => {
            kelurahan.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
        });
    });
</script>