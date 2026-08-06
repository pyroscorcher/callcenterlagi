@props([
    'balais'
])

<div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 sm:py-8 min-w-0">

    {{-- Breadcrumb --}}
    <div class="mb-4 sm:mb-6 text-white px-2 sm:px-0">
        <span class="font-bold">Data PIC Balai</span>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-4 sm:p-8 shadow-sm">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Daftar PIC Balai Bencana</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola data master Balai Bencana dan Person In Charge (PIC) di seluruh wilayah.
                </p>
            </div>
            
            {{-- Tombol Tambah Balai --}}
            <a href="{{ route('balai.create') }}" 
               class="flex items-center justify-center gap-2 rounded-lg bg-[#161446] px-4 py-2 text-sm font-medium text-white hover:bg-[#110e36] transition shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Balai
            </a>
        </div>

        {{-- Tabel Daftar Balai --}}
        @if($balais && $balais->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left whitespace-wrap">
                        <thead class="bg-[#161446] text-white">
                            <tr>
                                {{-- Gunakan px-3 py-3 text-xs untuk mobile, dan sm:px-6 sm:py-4 sm:text-sm untuk desktop --}}
                                <th scope="col" class="px-3 py-3 sm:px-6 sm:py-4 font-medium text-center text-xs sm:text-sm">No</th>
                                <th scope="col" class="px-3 py-3 sm:px-6 sm:py-4 font-medium text-xs sm:text-sm">Nama Balai</th>
                                <th scope="col" class="px-3 py-3 sm:px-6 sm:py-4 font-medium text-xs sm:text-sm">Unit Organisasi</th>
                                <th scope="col" class="px-3 py-3 sm:px-6 sm:py-4 font-medium text-xs sm:text-sm">Nama Kepala</th>
                                <th scope="col" class="px-3 py-3 sm:px-6 sm:py-4 font-medium text-center text-xs sm:text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($balais as $index => $balai)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Padding dan ukuran font dikurangi untuk mobile --}}
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center text-gray-700 text-xs sm:text-sm">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 font-medium text-gray-900 text-xs sm:text-sm">
                                        {{ $balai->nama_balai ?? '-'}}
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-700 text-xs sm:text-sm">
                                        {{ $balai->unor ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-700 text-xs sm:text-sm">
                                        {{ $balai->kepala ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                            {{-- Tombol juga dibuat lebih kecil (px-2 py-1 text-xs) di mobile --}}
                                            <a href="{{ route('data.pic-balai-show', $balai->id) }}"
                                               class="inline-flex items-center rounded-md bg-blue-600 px-2 py-1.5 sm:px-4 sm:py-2 text-[11px] sm:text-sm font-medium text-white transition hover:bg-blue-700">
                                                Detail
                                            </a>

                                            <form action="{{ route('balai.destroy', $balai->id) }}"
                                                  method="POST"
                                                  class="delete-form">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-md bg-red-600 px-2 py-1.5 sm:px-4 sm:py-2 text-[11px] sm:text-sm font-medium text-white transition hover:bg-red-700">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            {{-- Empty State jika data balai kosong --}}
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 sm:p-10 flex flex-col items-center justify-center text-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-gray-500 font-medium">Data Balai Bencana belum tersedia di dalam sistem.</p>
                <p class="text-sm text-gray-400 mt-1">Silakan klik tombol "Tambah Balai" untuk memasukkan data baru.</p>
            </div>
        @endif

    </div>
</div>