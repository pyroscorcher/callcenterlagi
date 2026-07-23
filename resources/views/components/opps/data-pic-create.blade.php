<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <a href="#" class="hover:underline text-gray-400">Dashboard</a>
        <span class="mx-2 text-gray-500">/</span>
        <a href="javascript:history.back()" class="hover:underline text-gray-400">Data PIC Balai</a>
        <span class="mx-2 text-gray-500">/</span>
        <span class="font-bold text-white">Tambah Balai Baru</span>
    </div>

    {{-- Card Container Form --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Formulir Tambah Balai Bencana</h1>
            <p class="text-sm text-gray-600 mt-1">Masukkan informasi akun, organisasi, wilayah, serta kontak penanggung jawab balai.</p>
        </div>

        {{-- Form POST ke Route Store Anda --}}
        <form action="{{ route('balai.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
                
                {{-- Bagian Akun & Organisasi --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Akun & Organisasi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Balai</label>
                            <input type="text" name="nama_balai" required placeholder="Contoh: BBWS Citarum"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username Login</label>
                            <input type="text" name="username" required placeholder="Contoh: balai_citarum"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required placeholder="********"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja (Unker)</label>
                            <input type="text" name="unker" placeholder="Masukkan Unker"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Organisasi (Unor)</label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {{-- Opsi 1 --}}
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer hover:border-[#161446] transition">
                                    <input type="radio" name="unor" value="SDA" required
                                        class="w-4 h-4 text-[#161446] border-gray-300 focus:ring-[#161446]" />
                                    <span class="text-sm font-medium text-gray-800">Sumber Daya Air (SDA)</span>
                                </label>

                                {{-- Opsi 2 --}}
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer hover:border-[#161446] transition">
                                    <input type="radio" name="unor" value="Binamarga" 
                                        class="w-4 h-4 text-[#161446] border-gray-300 focus:ring-[#161446]" />
                                    <span class="text-sm font-medium text-gray-800">Bina Marga</span>
                                </label>

                                {{-- Opsi 3 --}}
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer hover:border-[#161446] transition">
                                    <input type="radio" name="unor" value="Ciptakarya" 
                                        class="w-4 h-4 text-[#161446] border-gray-300 focus:ring-[#161446]" />
                                    <span class="text-sm font-medium text-gray-800">Cipta Karya</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Bagian Wilayah & PIC --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Wilayah & Kontak Penanggung Jawab</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" placeholder="Contoh: Jawa Barat"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pulau</label>
                            <input type="text" name="pulau" placeholder="Contoh: Jawa"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kepala Balai / Nama PIC</label>
                            <input type="text" name="kepala" placeholder="Nama lengkap beserta gelar"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak WhatsApp</label>
                            <input type="text" name="kontak" placeholder="Contoh: 081234567890"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:ring-2 focus:ring-[#161446] focus:outline-none" />
                        </div>

                    </div>
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
                    Simpan Data Balai
                </button>
            </div>

        </form>
    </div>
</div>