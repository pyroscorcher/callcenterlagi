@props([
    'laporan',
    'provinsis',
    'kabupatenkotas',
    'kecamatans',
    'kelurahans',
    'balais',         
    'assignedBalais', 
    'recommendedBalaiIds' => [], // NEW: Array of Balai IDs assigned to the current Kelurahan
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

        <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">

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

                {{-- Lokasi Kejadian --}}
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
                <div class="grid grid-cols-[220px_1fr] gap-4 items-start">
                    <label for="provinsi" class="text-gray-700 font-medium mt-3">Provinsi</label>
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

                {{-- Manajemen Foto Bencana --}}
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

                        <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Foto Baru (Bisa pilih lebih dari satu)</label>
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

                {{-- Balai Penugasan (MULTIPLE CHECKBOXES) --}}
                <div class="grid grid-cols-[220px_1fr] gap-4 items-start">
                    <label class="text-gray-700 font-medium mt-3">Balai Penugasan</label>
                    <div>
                        {{-- Custom Dropdown Wrapper --}}
                        <div class="relative w-full" id="balai_dropdown_wrapper">
                            
                            {{-- Dropdown Trigger Button --}}
                            <button type="button" id="balai_dropdown_trigger" class="w-full flex items-center justify-between bg-white border border-gray-400 rounded-lg px-4 py-2 text-left text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#161446] focus:border-[#161446]">
                                <span id="balai_dropdown_text" class="truncate block w-[90%]">Pilih Balai Penugasan...</span>
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            {{-- Dropdown Content (Hidden by default) --}}
                            <div id="balai_dropdown_content" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden">
                                <div id="balai_container" class="max-h-72 overflow-y-auto p-2 space-y-1">
                                    
                                    @if(isset($balais) && $balais->count() > 0)
                                        @php
                                            // PHP SORT: Put recommended (Kelurahan-specific) Balais at the top on initial page load
                                            $sortedBalais = collect($balais)->sortByDesc(function($balai) use ($recommendedBalaiIds) {
                                                return in_array($balai->id, $recommendedBalaiIds) ? 1 : 0;
                                            });
                                        @endphp
                                        
                                        @foreach($sortedBalais as $balai)
                                            @php
                                                $isRec = in_array($balai->id, $recommendedBalaiIds);
                                            @endphp
                                            
                                            <label class="flex items-start space-x-3 text-sm cursor-pointer p-2 rounded-lg border transition-colors {{ $isRec ? 'bg-green-50/70 border-green-200 hover:bg-green-100/70' : 'hover:bg-gray-50 border-transparent text-gray-700' }}">
                                                <input type="checkbox" name="balais[]" value="{{ $balai->id }}" data-name="{{ $balai->nama_balai ?? $balai->nama }}" 
                                                       @checked(in_array($balai->id, old('balais', $assignedBalais ?? [])))
                                                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <div class="flex flex-col">
                                                    <span class="{{ $isRec ? 'font-semibold text-green-800' : '' }}">{{ $balai->nama_balai ?? $balai->nama }}</span>
                                                    @if($isRec)
                                                        <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded uppercase tracking-wider mt-0.5 w-fit">Rekomendasi Area Kelurahan</span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-gray-500 italic p-2 text-center">Pilih provinsi untuk melihat daftar Balai.</p>
                                    @endif
                                    
                                </div>
                            </div>
                            
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-1">Anda dapat memilih lebih dari satu Balai. Balai yang disorot memiliki stasiun di kelurahan terkait.</p>
                        @error('balais')
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
    document.addEventListener("DOMContentLoaded", function() {
        const provinsi = document.getElementById('provinsi');
        const balaiContainer = document.getElementById('balai_container');
        const kabupaten = document.getElementById('kabupaten');
        const kecamatan = document.getElementById('kecamatan');
        const kelurahan = document.getElementById('kelurahan');

        // Dropdown specific elements
        const dropdownTrigger = document.getElementById('balai_dropdown_trigger');
        const dropdownContent = document.getElementById('balai_dropdown_content');
        const dropdownText = document.getElementById('balai_dropdown_text');

        // --- 1. Dropdown UI Logic ---

        dropdownTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownContent.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!dropdownTrigger.contains(e.target) && !dropdownContent.contains(e.target)) {
                dropdownContent.classList.add('hidden');
            }
        });

        function updateBalaiText() {
            const checkedBoxes = balaiContainer.querySelectorAll('input[type="checkbox"]:checked');
            if (checkedBoxes.length === 0) {
                dropdownText.textContent = "Pilih Balai Penugasan...";
                return;
            }
            const names = Array.from(checkedBoxes).map(cb => cb.getAttribute('data-name'));
            dropdownText.textContent = names.join(', ');
        }

        balaiContainer.addEventListener('change', function(e) {
            if (e.target && e.target.type === 'checkbox') {
                updateBalaiText();
            }
        });

        updateBalaiText(); // Run once on load


        // --- 2. AJAX Fetch Logic ---

        // Reusable function to fetch and re-render Balais based on Provinsi and Kelurahan
        async function fetchBalais(provId, kelId = null) {
            // Save currently checked IDs so they don't get erased during re-render
            const checkedValues = Array.from(balaiContainer.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value);

            balaiContainer.innerHTML = '<p class="text-sm text-gray-500 italic p-2 text-center">Loading data Balai...</p>';
            dropdownText.textContent = "Loading...";

            let url = '/ajax/balai/' + provId;
            if (kelId) {
                url += '?kelurahan_id=' + kelId;
            }

            try {
                const response = await fetch(url);
                const dataBalai = await response.json();

                // Sort: recommended (Kelurahan specific) to the top
                dataBalai.sort((a, b) => (b.is_recommended ? 1 : 0) - (a.is_recommended ? 1 : 0));

                balaiContainer.innerHTML = '';
                if (dataBalai.length === 0) {
                    balaiContainer.innerHTML = '<p class="text-sm text-gray-500 italic p-2 text-center">Tidak ada Balai yang ditugaskan untuk wilayah ini.</p>';
                } else {
                    dataBalai.forEach(item => {
                        const isRec = item.is_recommended;
                        // Conditional UI rendering based on recommendation flag
                        const bgClass = isRec ? 'bg-green-50/70 border-green-200 hover:bg-green-100/70' : 'hover:bg-gray-50 border-transparent text-gray-700';
                        const textClass = isRec ? 'font-semibold text-green-800' : '';
                        const badge = isRec ? '<span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded uppercase tracking-wider mt-0.5 w-fit">Rekomendasi Area Kelurahan</span>' : '';
                        
                        const isChecked = checkedValues.includes(String(item.id)) ? 'checked' : '';
                        const balaiName = item.nama_balai || item.nama;

                        balaiContainer.innerHTML += `
                            <label class="flex items-start space-x-3 text-sm cursor-pointer p-2 rounded-lg border transition-colors ${bgClass}">
                                <input type="checkbox" name="balais[]" value="${item.id}" data-name="${balaiName}" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" ${isChecked}>
                                <div class="flex flex-col">
                                    <span class="${textClass}">${balaiName}</span>
                                    ${badge}
                                </div>
                            </label>
                        `;
                    });
                }
                updateBalaiText();
            } catch (error) {
                console.error("Gagal mengambil data Balai:", error);
                balaiContainer.innerHTML = '<p class="text-sm text-red-500 italic p-2 text-center">Terjadi kesalahan saat memuat data Balai.</p>';
                dropdownText.textContent = "Error memuat data";
            }
        }

        // When Provinsi changes
        provinsi.addEventListener('change', async function () {
            kabupaten.innerHTML = '<option value="">Loading...</option>';
            kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';

            if (!this.value) {
                balaiContainer.innerHTML = '<p class="text-sm text-gray-500 italic p-2 text-center">Pilih provinsi untuk melihat daftar Balai.</p>';
                dropdownText.textContent = "Pilih Balai Penugasan...";
                kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                return;
            }

            // Fetch Kabupaten separately
            fetch('/ajax/kabupaten/' + this.value)
                .then(res => res.json())
                .then(data => {
                    kabupaten.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    data.forEach(item => {
                        kabupaten.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
                    });
                });

            // Fetch Balais (No Kelurahan selected yet, just general Provinsi)
            fetchBalais(this.value, null);
        });

        // When Kabupaten changes
        kabupaten.addEventListener('change', async function () {
            if (!this.value) {
                kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
                kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                return;
            }
            kecamatan.innerHTML = '<option>Loading...</option>';
            kelurahan.innerHTML = '<option>Pilih Kelurahan</option>';
            const response = await fetch('/ajax/kecamatan/' + this.value);
            const data = await response.json();
            kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
            data.forEach(item => {
                kecamatan.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
        });

        // When Kecamatan changes
        kecamatan.addEventListener('change', async function () {
            if (!this.value) {
                kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                // If they undo their selection back to Kec, reset balais back to Provinsi level
                if (provinsi.value) fetchBalais(provinsi.value, null);
                return;
            }
            kelurahan.innerHTML = '<option>Loading...</option>';
            const response = await fetch('/ajax/kelurahan/' + this.value);
            const data = await response.json();
            kelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
            data.forEach(item => {
                kelurahan.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
        });
        
        // NEW: When Kelurahan changes, update the Balai Dropdown to show Recommended ones!
        kelurahan.addEventListener('change', function() {
            if (provinsi.value) {
                fetchBalais(provinsi.value, this.value); // Sends Kelurahan ID to AJAX
            }
        });

        // --- 3. Bencana Definitions (Unchanged) ---
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

        const selectedJenis = @json(old('jenis_bencana', $laporan->jenis_bencana ?? ''));
        const selectedNama = @json(old('nama_bencana', $laporan->nama_bencana ?? ''));

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