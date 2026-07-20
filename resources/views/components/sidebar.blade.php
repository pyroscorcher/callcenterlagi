@props([
    'logoUrl' => null, // pass an image path, e.g. asset('images/logo.png')
])

<aside class="w-[330px] min-h-screen bg-[#161446] flex flex-col">

    {{-- Logo section — replaceable --}}
    <div class="flex items-center gap-3 px-6 py-6">
        @if ($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo" class="w-fit h-fit">
        @elseif (isset($logo))
            {{ $logo }}
        @else
            {{-- Fallback placeholder logo, swap via the $logoUrl prop or the $logo slot --}}
            <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="48" height="48" rx="4" fill="#161446"/>
                <path d="M10 8h12a12 12 0 0 1 0 24H10V8z" fill="#F7B733"/>
                <path d="M10 32h12a8 8 0 0 0 8-8H10v8z" fill="#3B39C4"/>
            </svg>
        @endif
    </div>

    {{-- Nav --}}
    <nav class="px-4 mt-2 space-y-1">

        <div class="flex items-center gap-2 px-3 py-2 text-white/90">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-semibold">Laporan</span>
        </div>

        <a href="{{ route('laporan.masuk-bencana') }}"
           class="block px-3 py-2 rounded-lg text-white font-bold text-sm
                  {{ request()->routeIs('laporan.masuk-bencana') ? 'bg-white/10' : 'hover:bg-white/5' }}">
            Laporan Masuk Bencana
        </a>

        <a href="{{ route('laporan.penanganan-balai') }}"
           class="block px-3 py-2 rounded-lg text-white/90 text-sm
                  {{ request()->routeIs('laporan.penanganan-balai') ? 'bg-white/10 font-bold text-white' : 'hover:bg-white/5' }}">
            Laporan Penanganan Balai
        </a>

        <a href="{{ route('data.pic-balai') }}"
           class="block px-3 py-2 rounded-lg text-white/90 text-sm
                  {{ request()->routeIs('data.pic-balai') ? 'bg-white/10 font-bold text-white' : 'hover:bg-white/5' }}">
            Data PIC Balai
        </a>
    </nav>
</aside>