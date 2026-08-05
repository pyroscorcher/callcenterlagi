@props([
    'type' => 'bencana-terkini',
    'laporans' => [],
])

@if ($laporans && $laporans->count() > 0)
    {{-- Removed redundant card wrapper, left only the scroll wrapper --}}
    <div class="w-full overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-[#161446] text-white">
                <tr>
                    <th scope="col" class="px-6 py-4 font-medium">Waktu Pelaporan</th>
                    <th scope="col" class="px-6 py-4 font-medium">Waktu Kejadian</th>
                    <th scope="col" class="px-6 py-4 font-medium">Jenis Bencana</th>
                    <th scope="col" class="px-6 py-4 font-medium">Nama Bencana</th>
                    <th scope="col" class="px-6 py-4 font-medium">Lokasi Bencana</th>
                    <th scope="col" class="px-6 py-4 font-medium">Status</th>
                    <th scope="col" class="px-6 py-4 font-medium">Pelapor</th>
                    <th scope="col" class="px-6 py-4 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="laporanTableBody-{{ $type }}">
                @foreach ($laporans as $laporan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-800">
                            {{ $laporan->created_at?->translatedFormat('d F Y \p\u\k\u\l H.i') }}
                        </td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->waktu_kejadian }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->jenis_bencana }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->nama_bencana }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->lokasi }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->status ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->pelapor }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('balai.laporan-penanganan-balai.show', $laporan->id) }}"
                                   class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                    Status
                                </a>

                                @if ($type === 'bencana-terkini')
                                    {{-- TODO: sambungkan ke route edit kalau sudah siap --}}
                                    <a href="#"
                                       class="inline-flex items-center rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-yellow-600">
                                        Edit
                                    </a>
                                @endif

                                <form action="{{ route('balai.laporan-penanganan-balai.destroy', $laporan->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
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
@else
    {{-- Empty State jika belum ada laporan --}}
    <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-10 flex flex-col items-center justify-center text-center">
        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-gray-500 font-medium">Belum ada laporan bencana yang masuk.</p>
    </div>
@endif