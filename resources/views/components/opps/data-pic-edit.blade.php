@props([
    'balai'
])

<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <a href="{{ route('data.pic-balai') }}" class="hover:underline text-gray-400">Data PIC Balai</a>
        <span class="mx-2 text-gray-500">/</span>
        <a href="{{ route('data.pic-balai-show', $balai->id) }}" class="hover:underline text-gray-400">Detail Balai</a>        
        <span class="mx-2 text-gray-500">/</span>
        <span class="font-bold text-white">Edit Balai</span>
    </div>

    {{-- Card Container Form --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Formulir Edit Data Balai</h1>
            <p class="text-sm text-gray-600 mt-1">Lakukan perubahan pada informasi akun, organisasi, wilayah, serta kontak penanggung jawab balai.</p>
        </div>

        {{-- Form POST ke Route Update dengan @method('PUT') --}}
        <form action="{{ route('balai.update', $balai->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
                
                {{-- Bagian Akun & Organisasi --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Akun & Organisasi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Balai</label>
                            <input type="text" name="nama_balai" value="{{ old('nama_balai', $balai->nama_balai) }}" required 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                            @error('nama_balai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username Login</label>
                            <input type="text" name="username" value="{{ old('username', $balai->username) }}" required 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                            @error('username') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah password)</span></label>
                            <input type="password" name="password" placeholder="Isi hanya jika ingin mengubah password lama..."
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja (Unker)</label>
                            <input type="text" name="unker" value="{{ old('unker', $balai->unker) }}" 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

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

                {{-- Bagian Wilayah & Daftar PIC --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Wilayah & Kontak Penanggung Jawab (PIC)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" value="{{ old('provinsi', $balai->provinsi) }}" 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pulau</label>
                            <input type="text" name="pulau" value="{{ old('pulau', $balai->pulau) }}" 
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>
                    </div>

                    {{-- Dynamic PIC Container --}}
                    <div id="pic-container" class="space-y-4">
                        @php
                            // Check if old input exists (validation fail), otherwise load from db, otherwise default 1 empty row
                            $pics = old('pics', $balai->pics->toArray());
                            if(empty($pics)) { $pics = [['id' => '', 'nama' => '', 'kontak' => '']]; }
                        @endphp

                        @foreach($pics as $index => $pic)
                            <div class="pic-row grid grid-cols-[1fr_1fr_auto] gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-200 relative">
                                <input type="hidden" name="pics[{{ $index }}][id]" value="{{ $pic['id'] ?? '' }}">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                                    <input type="text" name="pics[{{ $index }}][nama]" value="{{ $pic['nama'] ?? '' }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak WhatsApp</label>
                                    <input type="text" name="pics[{{ $index }}][kontak]" value="{{ $pic['kontak'] ?? '' }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                                </div>
                                <div>
                                    <button type="button" class="remove-pic text-red-500 bg-red-50 hover:bg-red-100 p-2.5 rounded-lg border border-red-200 transition">
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
    
    // Counter to ensure unique array indexes
    let picIndex = {{ count($pics) }};

    // Add new row
    addButton.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'pic-row grid grid-cols-[1fr_1fr_auto] gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-200 relative';
        row.innerHTML = `
            <input type="hidden" name="pics[${picIndex}][id]" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                <input type="text" name="pics[${picIndex}][nama]" required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak WhatsApp</label>
                <input type="text" name="pics[${picIndex}][kontak]" required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
            </div>
            <div>
                <button type="button" class="remove-pic text-red-500 bg-red-50 hover:bg-red-100 p-2.5 rounded-lg border border-red-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        picIndex++;
    });

    // Remove row (Event Delegation)
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-pic')) {
            const rows = container.querySelectorAll('.pic-row');
            if(rows.length > 1) {
                e.target.closest('.pic-row').remove();
            } else {
                alert('Minimal harus ada 1 PIC.');
            }
        }
    });
});
</script>