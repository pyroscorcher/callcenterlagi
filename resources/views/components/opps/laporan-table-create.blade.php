@props([
    'provinsis',
])

<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <a href="{{ route('laporan.masuk-bencana') }}" class="hover:underline">Laporan Masuk Bencana</a>
        <span class="mx-2 text-white/50">/</span>
        <span class="font-bold">Tambah Laporan</span>
    </div>

    {{-- Card Form --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg font-bold text-gray-900">Tambah Laporan Bencana</h1>
        </div>

        <form class="buat-laporan" action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">

                {{-- Nama Pelapor --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="pelapor" class="text-gray-700 font-medium">Nama Pelapor</label>
                    <div>
                        <input type="text" name="pelapor" id="pelapor"
                               value="{{ old('pelapor') }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('pelapor')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- No WhatsApp --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="telepon" class="text-gray-700 font-medium">No WhatsApp</label>
                    <div>
                        <input type="text" name="telepon" id="telepon"
                               value="{{ old('telepon') }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('telepon')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Jenis Bencana --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="jenis_bencana" class="text-gray-700 font-medium">Jenis Bencana</label>
                    <div>
                        <select name="jenis_bencana" id="jenis_bencana"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">-- Pilih Jenis Bencana --</option>
                        </select>
                        @error('jenis_bencana')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Nama Kejadian --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center mt-4">
                    <label for="nama_bencana" class="text-gray-700 font-medium">Nama Kejadian</label>
                    <div>
                        <select name="nama_bencana" id="nama_bencana"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">-- Pilih Nama Kejadian --</option>
                        </select>
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
                               value="{{ old('waktu_kejadian') }}"
                               placeholder="YYYY-MM-DD HH:MM"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('waktu_kejadian')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Wilayah Waktu --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="wilayah_waktu" class="text-gray-700 font-medium">Wilayah Waktu</label>
                    <div>
                        <select name="wilayah_waktu" id="wilayah_waktu"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">-- Pilih Wilayah Waktu --</option>
                            @foreach(['WIB', 'WITA', 'WIT'] as $wilayah)
                                <option value="{{ $wilayah }}" @selected(old('wilayah_waktu') == $wilayah)>{{ $wilayah }}</option>
                            @endforeach
                        </select>
                        @error('wilayah_waktu')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Lokasi Kejadian --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="lokasi" class="text-gray-700 font-medium">Lokasi Kejadian</label>
                    <div>
                        <input type="text" name="lokasi" id="lokasi"
                               value="{{ old('lokasi') }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('lokasi')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Provinsi --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-start">
                    <label for="provinsi" class="text-gray-700 font-medium mt-3">Provinsi</label>
                    <div>
                        <select id="provinsi" name="provinsi_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}" @selected(old('provinsi_id') == $provinsi->id)>
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
                        </select>
                        @error('kelurahan_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Titik Kejadian (Lintang & Bujur) --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label class="text-gray-700 font-medium">Titik Kejadian</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="text" name="lintang" id="lintang"
                                   value="{{ old('lintang') }}"
                                   placeholder="Lintang"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                            @error('lintang')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <input type="text" name="bujur" id="bujur"
                                   value="{{ old('bujur') }}"
                                   placeholder="Bujur"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                            @error('bujur')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Link Google Maps --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="gmaps_link" class="text-gray-700 font-medium">Link Google Maps</label>
                    <div>
                        <input type="url" name="gmaps_link" id="gmaps_link"
                               value="{{ old('gmaps_link') }}"
                               placeholder="https://maps.google.com/..."
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('gmaps_link')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Dampak Bencana --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="dampak_bencana" class="text-gray-700 font-medium">Dampak Bencana</label>
                    <div>
                        <input type="text" name="dampak_bencana" id="dampak_bencana"
                               value="{{ old('dampak_bencana') }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('dampak_bencana')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Infrastruktur Terdampak --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-center">
                    <label for="infrastruktur_terdampak" class="text-gray-700 font-medium">Infrastruktur Terdampak</label>
                    <div>
                        <input type="text" name="infrastruktur_terdampak" id="infrastruktur_terdampak"
                               value="{{ old('infrastruktur_terdampak') }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]" />
                        @error('infrastruktur_terdampak')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Manajemen Foto Bencana --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 pt-4 border-t border-gray-200">
                    <span class="text-gray-700 font-medium">Foto Bencana</span>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto (Opsional, bisa pilih lebih dari satu)</label>
                        <input type="file" name="fotos[]" multiple accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#161446] file:text-white hover:file:bg-[#110e36] cursor-pointer" />
                        @error('fotos.*')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kronologi Bencana --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-start pt-4 border-t border-gray-200">
                    <label for="deskripsi" class="text-gray-700 font-medium pt-2">Kronologi Bencana</label>
                    <div>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">{{ old('deskripsi') }}</textarea>
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
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]">{{ old('kebutuhan_mendesak') }}</textarea>
                        @error('kebutuhan_mendesak')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Footer Action Buttons --}}
            <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                <a href="{{ route('laporan.masuk-bencana') }}"
                   class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition">
                    Batal
                </a>

                <button type="submit"
                        class="rounded-lg bg-[#161446] px-6 py-2.5 text-white font-medium hover:bg-[#110e36] transition shadow-sm">
                    Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const provinsi = document.getElementById('provinsi');
        const kabupaten = document.getElementById('kabupaten');
        const kecamatan = document.getElementById('kecamatan');
        const kelurahan = document.getElementById('kelurahan');

        // --- Wilayah Cascading Dropdowns ---

        provinsi.addEventListener('change', async function () {
            kabupaten.innerHTML = '<option value="">Loading...</option>';
            kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';

            if (!this.value) {
                kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                return;
            }

            const response = await fetch('/ajax/kabupaten/' + this.value);
            const data = await response.json();
            kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            data.forEach(item => {
                kabupaten.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
        });

        kabupaten.addEventListener('change', async function () {
            kecamatan.innerHTML = '<option value="">Loading...</option>';
            kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';

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
        });

        kecamatan.addEventListener('change', async function () {
            kelurahan.innerHTML = '<option value="">Loading...</option>';

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
        });

        // --- Jenis Bencana / Nama Bencana Dropdown Logic ---

        const bencanaRules = {
            "Kebakaran Gedung dan Pemukiman": ["Kebakaran Gedung dan Pemukiman"],
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

        const jenisSelect = document.getElementById('jenis_bencana');
        const namaSelect = document.getElementById('nama_bencana');

        const selectedJenis = @json(old('jenis_bencana', ''));
        const selectedNama = @json(old('nama_bencana', ''));

        for (const jenis in bencanaRules) {
            let option = document.createElement('option');
            option.value = jenis;
            option.textContent = jenis;
            if (jenis.trim().toLowerCase() === selectedJenis.trim().toLowerCase()) {
                option.selected = true;
            }
            jenisSelect.appendChild(option);
        }

        function populateNama(jenisValue, namaValueToSelect = '') {
            namaSelect.innerHTML = '<option value="">-- Pilih Nama Kejadian --</option>';
            let matchedKey = Object.keys(bencanaRules).find(k => k.trim().toLowerCase() === jenisValue.trim().toLowerCase());
            if (matchedKey && bencanaRules[matchedKey]) {
                bencanaRules[matchedKey].forEach(function(nama) {
                    let option = document.createElement('option');
                    option.value = nama;
                    option.textContent = nama;
                    if (nama.trim().toLowerCase() === namaValueToSelect.trim().toLowerCase()) {
                        option.selected = true;
                    }
                    namaSelect.appendChild(option);
                });
            }
        }

        if (selectedJenis) {
            populateNama(selectedJenis, selectedNama);
        }
        jenisSelect.addEventListener('change', function() {
            populateNama(this.value);
        });
    });
</script>