@props([
    'laporans' => [],
])

<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full bg-white">
        <thead>
            <tr class="bg-[#B9C6F3] text-left">
                <th class="px-6 py-4 font-bold text-gray-900">Waktu Pelaporan</th>
                <th class="px-6 py-4 font-bold text-gray-900">Waktu Kejadian</th>
                <th class="px-6 py-4 font-bold text-gray-900">Jenis Bencana</th>
                <th class="px-6 py-4 font-bold text-gray-900">Jenis Kejadian Bencana</th>
                <th class="px-6 py-4 font-bold text-gray-900">Lokasi Bencana</th>
                <th class="px-6 py-4 font-bold text-gray-900">Detail Status</th>
                <th class="px-6 py-4 font-bold text-gray-900">Pelapor</th>
                <th class="px-6 py-4 font-bold text-gray-900">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $index => $laporan)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} border-t border-gray-100">
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->waktu_pelaporan }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->waktu_kejadian }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->jenis_bencana }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->jenis_kejadian_bencana }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->lokasi_bencana }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->status }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $laporan->pelapor }}</td>
                    <td class="px-6 py-4">
                        {{-- <a href="{{ route('laporan.show', $laporan->id ?? '#') }}"
                           class="text-[#3B39C4] hover:underline font-medium">
                            Detail
                        </a> --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                        Belum ada laporan bencana.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (method_exists($laporans, 'links'))
    <div class="mt-4">
        {{ $laporans->links() }}
    </div>
@endif