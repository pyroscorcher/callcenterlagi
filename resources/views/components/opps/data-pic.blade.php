@props([
    'balais'
])

<div class="max-w-6xl mx-auto px-8 py-8">

    {{-- Breadcrumb
    <div class="mb-6 text-white">
        <a href="#" class="hover:underline">Dashboard</a>
        <span class="mx-2 text-white/50">/</span>
        <span class="font-bold">Data PIC Balai</span>
    </div> --}}

    {{-- Main Container Card --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Daftar PIC Balai Bencana</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola data master Balai Bencana dan Person In Charge (PIC) di seluruh wilayah.
                </p>
            </div>
            
            {{-- Tombol Tambah Balai yang sudah dihubungkan ke route('balai.create') --}}
            <a href="{{ route('balai.create') }}" 
            class="flex items-center gap-2 rounded-lg bg-[#161446] px-4 py-2 text-sm font-medium text-white hover:bg-[#110e36] transition shadow-sm">
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
                    <table class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-[#161446] text-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-medium text-center">No</th>
                                <th scope="col" class="px-6 py-4 font-medium">Nama Balai</th>
                                <th scope="col" class="px-6 py-4 font-medium">Unit Kerja</th>
                                <th scope="col" class="px-6 py-4 font-medium">Unit Organisasi</th>
                                <th scope="col" class="px-6 py-4 font-medium">Nama Kepala</th>
                                <th scope="col" class="px-6 py-4 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($balais as $index => $balai)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-700">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $balai->nama_balai ?? '-'}}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $balai->unker ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $balai->unor ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $balai->kepala ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('data.pic-balai-show', $balai->id) }}"
                                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                                Detail
                                            </a>

                                            <form action="{{ route('balai.destroy', $balai->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data Balai ini?');">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700">
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
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 flex flex-col items-center justify-center text-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-gray-500 font-medium">Data Balai Bencana belum tersedia di dalam sistem.</p>
                <p class="text-sm text-gray-400 mt-1">Silakan klik tombol "Tambah Balai" di kanan atas untuk memasukkan data baru.</p>
            </div>
        @endif

    </div>
</div>