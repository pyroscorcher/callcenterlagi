@props([
    'laporans' => [],
])

<div class="max-w-6xl mx-auto px-4 py-4 md:px-8 md:py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <span class="font-bold">Laporan Masuk Bencana</span>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-4 md:p-8 shadow-sm">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-lg md:text-xl font-bold text-gray-900">Daftar Laporan Masuk</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola dan pantau seluruh laporan kejadian bencana dari masyarakat.
                </p>
            </div>

            {{-- TOMBOL TAMBAH LAPORAN BARU --}}
            <a href="{{ route('laporan.create') }}" 
               class="w-full md:w-auto justify-center inline-flex items-center gap-2 rounded-lg bg-[#161446] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#110e36] transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Laporan
            </a>
        </div>

        {{-- Tabel Daftar Laporan --}}
        @if($laporans && $laporans->count() > 0)

            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm whitespace-wrap">
                        <thead class="bg-[#161446] text-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-medium">Waktu Pelaporan</th>
                                <th scope="col" class="px-6 py-4 font-medium">Waktu Kejadian</th>
                                <th scope="col" class="px-6 py-4 font-medium">Jenis Bencana</th>
                                <th scope="col" class="px-6 py-4 font-medium">Nama Bencana</th>
                                <th scope="col" class="px-6 py-4 font-medium">Lokasi Bencana</th>
                                <th scope="col" class="px-6 py-4 font-medium">Pelapor</th>
                                <th scope="col" class="px-6 py-4 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="laporanTableBody">
                            @foreach ($laporans as $index => $laporan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->created_at?->translatedFormat('d F Y \p\u\k\u\l H.i') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->waktu_kejadian }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->jenis_bencana }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->nama_bencana }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->lokasi }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $laporan->pelapor }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('laporan.show', $laporan->id) }}"
                                               class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                                Detail
                                            </a>

                                            <form action="{{ route('laporan.destroy', $laporan->id) }}"
                                                  method="POST"
                                                  class="delete-form">
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

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4">
                @foreach($laporans as $laporan)

                <div class="bg-white rounded-xl shadow p-4 border laporan-card">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ $laporan->nama_bencana }}
                            </h3>

                            <p class="text-sm text-gray-500">
                                {{ $laporan->jenis_bencana }}
                            </p>
                        </div>

                        <span class="text-xs text-gray-500">
                            {{ $laporan->created_at?->format('d/m/Y') }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div>
                            <span class="font-medium">Pelapor</span><br>
                            {{ $laporan->pelapor }}
                        </div>

                        <div>
                            <span class="font-medium">Lokasi</span><br>
                            {{ $laporan->lokasi }}
                        </div>

                        <div>
                            <span class="font-medium">Waktu Kejadian</span><br>
                            {{ $laporan->waktu_kejadian }}
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <a
                            href="{{ route('laporan.show', $laporan->id) }}"
                            class="flex-1 rounded-lg bg-blue-600 py-2 text-center text-white">
                            Detail
                        </a>

                        <form
                            action="{{ route('laporan.destroy', $laporan->id) }}"
                            method="POST"
                            class="delete-form flex-1">

                            @csrf
                            @method('DELETE')

                            <button
                                class="w-full rounded-lg bg-red-600 py-2 text-white">
                                Hapus
                            </button>

                        </form>
                    </div>

                </div>

                @endforeach
            </div>

        @else
            {{-- Empty State jika belum ada laporan --}}
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 md:p-10 flex flex-col items-center justify-center text-center">
                <svg class="w-10 h-10 md:w-12 md:h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 font-medium">Belum ada laporan bencana yang masuk.</p>
            </div>
        @endif

        {{-- Pagination --}}
        @if (method_exists($laporans, 'links'))
            <div class="mt-6">
                {{ $laporans->links() }}
            </div>
        @endif

    </div>
</div>