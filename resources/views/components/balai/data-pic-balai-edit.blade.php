@props([
    'balai' => null,
    'provinsis' => [],
    'authUserId' => null,
])

<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Card Container Form --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Formulir Edit Data Balai</h1>
            <p class="text-sm text-gray-600 mt-1">Lakukan perubahan pada informasi organisasi, wilayah, kontak penanggung jawab, serta akun PIC balai.</p>
        </div>

        {{-- Form POST ke Route Update dengan @method('PUT') --}}
        <form action="{{ route('balai.data-pic-balai.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
                
                {{-- Bagian Organisasi --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Organisasi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Balai</label>
                            <input type="text" name="nama_balai" value="{{ old('nama_balai', $balai->nama_balai) }}" required 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                            @error('nama_balai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Balai no longer has its own login — accounts live per-PIC below. --}}

                        {{-- Radio Button Dinamis Unor --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Organisasi (Unor)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer hover:border-[#161446] transition">
                                    <input type="radio" name="unor" value="SDA" required {{ old('unor', $balai->unor) == 'SDA' ? 'checked' : '' }}
                                           class="w-4 h-4 text-[#161446] border-gray-300 focus:ring-[#161446]" />
                                    <span class="text-sm font-medium text-gray-800">Sumber Daya Air (SDA)</span>
                                </label>

                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer hover:border-[#161446] transition">
                                    <input type="radio" name="unor" value="Binamarga" {{ old('unor', $balai->unor) == 'Binamarga' ? 'checked' : '' }}
                                           class="w-4 h-4 text-[#161446] border-gray-300 focus:ring-[#161446]" />
                                    <span class="text-sm font-medium text-gray-800">Bina Marga</span>
                                </label>

                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer hover:border-[#161446] transition">
                                    <input type="radio" name="unor" value="Ciptakarya" {{ old('unor', $balai->unor) == 'Ciptakarya' ? 'checked' : '' }}
                                           class="w-4 h-4 text-[#161446] border-gray-300 focus:ring-[#161446]" />
                                    <span class="text-sm font-medium text-gray-800">Cipta Karya</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Bagian Wilayah & Kepala Balai --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Wilayah & Kepala Balai</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi (Maks 2)</label>

                            @php
                                $selectedProvinsis = old('provinsi', explode(', ', $balai->provinsi ?? ''));
                                $selectedProvinsis = array_filter(array_map('trim', $selectedProvinsis));
                            @endphp

                            <div class="relative w-full" id="provinsi_dropdown_wrapper">
                                <button type="button" id="provinsi_dropdown_trigger" class="w-full flex items-center justify-between bg-white border border-gray-300 rounded-lg px-4 py-2 text-left text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#161446]">
                                    <span id="provinsi_dropdown_text" class="truncate block w-[90%]">Pilih Provinsi...</span>
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div id="provinsi_dropdown_content" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden">
                                    <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                        @foreach($provinsis as $prov)
                                            <label class="flex items-center space-x-3 text-sm cursor-pointer p-2 rounded-lg hover:bg-gray-50 border border-transparent">
                                                <input type="checkbox" name="provinsi[]" value="{{ $prov->nama }}" data-name="{{ $prov->nama }}"
                                                       @checked(in_array($prov->nama, $selectedProvinsis))
                                                       class="provinsi-checkbox mt-0.5 w-4 h-4 rounded border-gray-300 text-[#161446] focus:ring-[#161446]">
                                                <span class="text-gray-700">{{ $prov->nama }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @error('provinsi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pulau</label>
                            <select name="pulau" required
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none">
                                <option value="">-- Pilih Pulau --</option>
                                
                                @php
                                    $daftarPulau = [
                                        'Sumatera',
                                        'Jawa',
                                        'Bali',
                                        'Nusa Tenggara',
                                        'Kalimantan',
                                        'Sulawesi',
                                        'Maluku',
                                        'Papua'
                                    ];
                                @endphp

                                @foreach($daftarPulau as $namaPulau)
                                    <option value="{{ $namaPulau }}" @selected(old('pulau', $balai->pulau ?? '') == $namaPulau)>
                                        {{ $namaPulau }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pulau') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kepala Balai</label>
                            <input type="text" name="kepala" value="{{ old('kepala', $balai->kepala) }}" 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Kepala Balai (WhatsApp)</label>
                            <input type="text" name="kontak" value="{{ old('kontak', $balai->kontak) }}" 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Bagian Daftar PIC — each PIC is a full login account (users table, role=pic) --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Daftar Person In Charge (PIC)</h2>
                    <p class="text-xs text-gray-500 mb-4">Kosongkan kolom password jika tidak ingin mengubah password PIC yang sudah ada. Akun Anda sendiri tidak dapat dihapus dari daftar ini.</p>
                    
                    <div id="pic-container" class="space-y-4">
                        @php
                            $pics = old('pics', $balai->pics->map(function ($pic) {
                                return [
                                    'id' => $pic->id,
                                    'nama' => $pic->name,
                                    'username' => $pic->username,
                                    'kontak' => $pic->kontak,
                                ];
                            })->toArray());
                            if (empty($pics)) {
                                $pics = [['id' => '', 'nama' => '', 'username' => '', 'kontak' => '']];
                            }
                        @endphp

                        @foreach($pics as $index => $pic)
                            @php $isSelf = !empty($pic['id']) && (int) $pic['id'] === (int) $authUserId; @endphp
                            <div class="pic-row grid grid-cols-1 md:grid-cols-[1fr_1fr_1fr_1fr_auto] gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-200 relative">
                                <input type="hidden" name="pics[{{ $index }}][id]" value="{{ $pic['id'] ?? '' }}">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama PIC
                                        @if($isSelf)
                                            <span class="text-[10px] font-bold text-[#161446] bg-[#161446]/10 px-1.5 py-0.5 rounded uppercase tracking-wider ml-1">Anda</span>
                                        @endif
                                    </label>
                                    <input type="text" name="pics[{{ $index }}][nama]" value="{{ $pic['nama'] ?? '' }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <input type="text" name="pics[{{ $index }}][username]" value="{{ $pic['username'] ?? '' }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" name="pics[{{ $index }}][password]" placeholder="{{ !empty($pic['id']) ? 'Kosongkan jika tidak diubah' : 'Wajib untuk PIC baru' }}"
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak WhatsApp</label>
                                    <input type="text" name="pics[{{ $index }}][kontak]" value="{{ $pic['kontak'] ?? '' }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                                </div>
                                <div>
                                    <button type="button" class="remove-pic text-red-500 bg-red-50 hover:bg-red-100 p-2.5 rounded-lg border border-red-200 transition disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-red-50"
                                            data-self="{{ $isSelf ? '1' : '0' }}"
                                            @disabled($isSelf)
                                            title="{{ $isSelf ? 'Anda tidak dapat menghapus akun Anda sendiri' : 'Hapus PIC ini' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-pic" class="mt-4 text-[#161446] bg-[#161446]/10 hover:bg-[#161446]/20 px-4 py-2 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah PIC Lainnya
                    </button>
                    @error('pics') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                </div>

            </div>

            {{-- Footer Action Buttons --}}
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="javascript:history.back()"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition shadow-sm">
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
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('pic-container');
    const addButton = document.getElementById('add-pic');
    
    let picIndex = {{ count($pics) }};

    addButton.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'pic-row grid grid-cols-1 md:grid-cols-[1fr_1fr_1fr_1fr_auto] gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-200 relative';
        row.innerHTML = `
            <input type="hidden" name="pics[${picIndex}][id]" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                <input type="text" name="pics[${picIndex}][nama]" required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="pics[${picIndex}][username]" required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="pics[${picIndex}][password]" placeholder="Wajib untuk PIC baru"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak WhatsApp</label>
                <input type="text" name="pics[${picIndex}][kontak]" required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
            </div>
            <div>
                <button type="button" class="remove-pic text-red-500 bg-red-50 hover:bg-red-100 p-2.5 rounded-lg border border-red-200 transition" data-self="0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        picIndex++;
    });

    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-pic');
        if (!btn || btn.disabled) return;

        const rows = container.querySelectorAll('.pic-row');
        if (rows.length > 1) {
            btn.closest('.pic-row').remove();
        } else {
            alert('Minimal harus ada 1 PIC.');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const provTrigger = document.getElementById('provinsi_dropdown_trigger');
    const provContent = document.getElementById('provinsi_dropdown_content');
    const provText = document.getElementById('provinsi_dropdown_text');
    const provCheckboxes = document.querySelectorAll('.provinsi-checkbox');

    provTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        provContent.classList.toggle('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!provTrigger.contains(e.target) && !provContent.contains(e.target)) {
            provContent.classList.add('hidden');
        }
    });

    function updateProvinsiText() {
        const checkedBoxes = Array.from(document.querySelectorAll('.provinsi-checkbox:checked'));
        if (checkedBoxes.length === 0) {
            provText.textContent = "Pilih Provinsi...";
            return;
        }
        const names = checkedBoxes.map(cb => cb.getAttribute('data-name'));
        provText.textContent = names.join(', ');
    }

    provCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.provinsi-checkbox:checked').length;
            if (checkedCount > 2) {
                this.checked = false;
                alert('Maksimal hanya dapat memilih 2 Provinsi untuk satu Balai.');
            }
            updateProvinsiText();
        });
    });

    updateProvinsiText();
});
</script>