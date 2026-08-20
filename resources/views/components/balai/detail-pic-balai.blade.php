@props([
    'balai',
])

<div class="max-w-6xl mx-auto">

    {{-- Card Container --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">

        {{-- Header Responsif --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Detail Informasi Balai</h1>

            <a href="{{ route('balai.data-pic-balai.edit') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#161446] px-4 py-2 text-sm font-medium text-white hover:bg-[#110e36] transition shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data
            </a>
        </div>

        {{-- Detail List Container --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6 space-y-6 sm:space-y-8">

            {{-- Bagian Identitas & Organisasi --}}
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4">Identitas & Organisasi</h2>
                <dl class="space-y-1 sm:space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] md:grid-cols-[200px_1fr] gap-1 sm:gap-4 py-2 border-b border-gray-100 items-start sm:items-center">
                        <dt class="text-gray-500 sm:text-gray-600 text-xs sm:text-sm font-medium">Nama Balai</dt>
                        <dd class="text-gray-900 text-sm sm:text-base font-bold break-words">{{ $balai->nama_balai ?? '-' }}</dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] md:grid-cols-[200px_1fr] gap-1 sm:gap-4 py-2 border-b border-gray-100 items-start sm:items-center">
                        <dt class="text-gray-500 sm:text-gray-600 text-xs sm:text-sm font-medium">Unit Organisasi</dt>
                        <dd class="text-gray-900 text-sm sm:text-base break-words">{{ $balai->unor ?? '-' }}</dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] md:grid-cols-[200px_1fr] gap-1 sm:gap-4 py-2 border-b border-gray-100 items-start sm:items-center">
                        <dt class="text-gray-500 sm:text-gray-600 text-xs sm:text-sm font-medium">Kepala Balai</dt>
                        <dd class="text-gray-900 text-sm sm:text-base break-words">{{ $balai->kepala ?? '-' }}</dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] md:grid-cols-[200px_1fr] gap-1 sm:gap-4 py-2 items-start sm:items-center">
                        <dt class="text-gray-500 sm:text-gray-600 text-xs sm:text-sm font-medium">Kontak Kepala Balai</dt>
                        <dd class="text-gray-900 text-sm sm:text-base break-words">{{ $balai->kontak ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <hr class="border-gray-200">

            {{-- Bagian Wilayah --}}
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4">Wilayah Operasional</h2>
                <dl class="space-y-1 sm:space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] md:grid-cols-[200px_1fr] gap-1 sm:gap-4 py-2 border-b border-gray-100 items-start sm:items-center">
                        <dt class="text-gray-500 sm:text-gray-600 text-xs sm:text-sm font-medium">Provinsi</dt>
                        <dd class="text-gray-900 text-sm sm:text-base break-words">{{ $balai->provinsi ?? '-' }}</dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] md:grid-cols-[200px_1fr] gap-1 sm:gap-4 py-2 items-start sm:items-center">
                        <dt class="text-gray-500 sm:text-gray-600 text-xs sm:text-sm font-medium">Pulau</dt>
                        <dd class="text-gray-900 text-sm sm:text-base break-words">{{ $balai->pulau ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <hr class="border-gray-200">

            {{-- Bagian Daftar Person In Charge (PIC) --}}
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4">Daftar Person In Charge (PIC)</h2>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-xs sm:text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-[10px] sm:text-xs text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-3 sm:px-6 py-3 font-medium w-12 text-center">No</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 font-medium whitespace-nowrap">Nama PIC</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 font-medium whitespace-nowrap">Username</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 font-medium whitespace-nowrap">Kontak WhatsApp</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 font-medium w-28 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if(isset($balai->pics) && $balai->pics->count() > 0)
                                @foreach($balai->pics as $index => $pic)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">{{ $index + 1 }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 font-bold text-gray-900 whitespace-nowrap">{{ $pic->name }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-500 whitespace-nowrap">{{ $pic->username }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">{{ $pic->kontak }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                            @if($pic->kontak)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pic->kontak) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-green-500 hover:bg-green-600 text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
                                                   title="Chat {{ $pic->name }} via WhatsApp">
                                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.125-.339-.154-1.229-.452-2.344-1.455-1.026-.924-1.718-2.067-1.918-2.408-.2-.341-.021-.527.15-.696.155-.153.342-.4.512-.6.171-.2.228-.34.341-.568.114-.227.057-.426-.028-.596-.085-.17-1.192-2.87-1.632-3.929-.429-1.033-.865-.893-1.189-.91l-.81-.018c-.284 0-.746.107-1.137.531-.391.424-1.493 1.458-1.493 3.555 0 2.097 1.528 4.126 1.741 4.41.213.283 2.977 4.544 7.214 6.375 1.009.435 1.796.696 2.408.891 1.013.323 1.936.277 2.66.168.81-.122 2.492-1.018 2.842-2.001.35-1.002.35-1.848.245-2.001-.105-.152-.391-.243-.733-.414z"/>
                                                    </svg>
                                                    WhatsApp
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-xs sm:text-sm text-gray-500 italic bg-white">
                                        Belum ada PIC yang ditambahkan untuk Balai ini.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>