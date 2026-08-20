@props([
    'logoUrl' => null,
])

{{-- =========================================================
    OVERLAY SIDEBAR MOBILE
========================================================= --}}
<div
    id="sidebarbalai-overlay"
    class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"
></div>


{{-- =========================================================
    SIDEBAR
========================================================= --}}
<aside
    id="sidebarbalai"
    class="fixed top-0 left-0 z-50 w-[330px] h-screen
           bg-[#161446] flex flex-col
           transition-transform duration-300
           -translate-x-full
           md:translate-x-0
           md:static
           md:min-h-screen"
>


    {{-- =====================================================
        HEADER SIDEBAR
        Logo + Tombol Close Mobile
    ====================================================== --}}
    <div class="flex items-center justify-between px-6 py-6">

        {{-- Logo --}}
        <div class="flex items-center gap-3">

            @if ($logoUrl)

                <img
                    src="{{ $logoUrl }}"
                    alt="Logo"
                    class="w-fit h-fit"
                >

            @elseif (isset($logo))

                {{ $logo }}

            @else

                {{-- Fallback Logo --}}
                <svg
                    class="w-12 h-12"
                    viewBox="0 0 48 48"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        width="48"
                        height="48"
                        rx="4"
                        fill="#161446"
                    />

                    <path
                        d="M10 8h12a12 12 0 0 1 0 24H10V8z"
                        fill="#F7B733"
                    />

                    <path
                        d="M10 32h12a8 8 0 0 0 8-8H10v8z"
                        fill="#3B39C4"
                    />
                </svg>

            @endif

        </div>


        {{-- =================================================
            TOMBOL CLOSE MOBILE
        ================================================== --}}
        <button
            id="close-sidebarbalai-btn"
            type="button"
            class="md:hidden text-white hover:text-gray-300
                   focus:outline-none"
        >

            <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>

        </button>

    </div>


    {{-- =========================================================
        NAVIGATION
    ========================================================== --}}
    <nav class="px-4 mt-2 space-y-1">


        {{-- =====================================================
            SECTION LAPORAN
        ====================================================== --}}
        <div class="flex items-center gap-2 px-3 py-2 text-white/90">

            <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"
                />
            </svg>

            <span class="font-semibold">
                Laporan
            </span>

        </div>


        {{-- =====================================================
            LAPORAN PENANGANAN BALAI
        ====================================================== --}}
        <a
            href="{{ route('balai.dashboard') }}"
            class="block px-3 py-2 rounded-lg text-white/90 text-sm
                   {{ request()->routeIs('balai.dashboard')
                        ? 'bg-white/10 font-bold text-white'
                        : 'hover:bg-white/5' }}"
        >
            Laporan Penanganan Balai
        </a>


        {{-- =====================================================
            DATA PIC BALAI
        ====================================================== --}}
        <a
            href="{{ route('balai.data-pic-balai.show') }}"
            class="block px-3 py-2 rounded-lg text-white/90 text-sm
                   {{ request()->routeIs('balai.data-pic-balai.*')
                        ? 'bg-white/10 font-bold text-white'
                        : 'hover:bg-white/5' }}"
        >
            Data PIC Balai
        </a>


        {{-- =====================================================
            LOGOUT
        ====================================================== --}}
        <form
            method="POST"
            action="{{ route('logout') }}"
            class="logoutbalai"
        >
            @csrf
            <button
                type="submit"
                class="flex items-center gap-2 w-full px-3 py-2
                       rounded-lg text-white/90 text-sm
                       hover:bg-white/5"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>
                Logout
            </button>

        </form>

    </nav>

</aside>


{{-- =========================================================
    SWEETALERT LOGOUT CONFIRMATION
========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const logoutForms = document.querySelectorAll('.logoutbalai');

    logoutForms.forEach(function (form) {

        form.addEventListener('submit', function (e) {

            e.preventDefault();

            Swal.fire({
                title: 'Logout?',
                text: 'Apakah Anda yakin ingin keluar dari SITABA?',
                icon: 'warning',

                showCancelButton: true,

                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',

                confirmButtonText: 'Logout',
                cancelButtonText: 'Batal',

                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {

                if (result.isConfirmed) {
                    form.submit();
                }

            });

        });

    });

});
</script>