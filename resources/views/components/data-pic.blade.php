@props([
    'items' => [],
])

<div class="bg-[#F4F5F9] rounded-2xl p-8 text-gray-600">
    {{--
        TODO: this component is waiting on the data model / ERD for
        Data PIC Balai. This is also the data the "Kirim Pesan Kepada PIC"
        button on the Laporan detail page needs, so building this out
        unblocks that too. Once it exists, pass real data in via the $items
        prop from the controller (same pattern as <x-laporan-table>
        receiving :laporans="$laporans"), and swap this placeholder text
        for a real table/list.
    --}}
    @forelse ($items as $item)
        {{-- placeholder row --}}
        <div class="border-b border-gray-200 py-3">{{ $item }}</div>
    @empty
        Halaman ini masih menunggu model data PIC Balai.
    @endforelse
</div>