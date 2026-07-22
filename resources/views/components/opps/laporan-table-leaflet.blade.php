@props([
    'laporan',
])

<div class="max-w-6xl mx-auto px-8 py-8">
    {{-- Breadcrumb --}}
    <div class="mb-6 text-white">
        <a href="{{ route('laporan.masuk-bencana') }}" class="hover:underline">Laporan Masuk Bencana</a>
        <span class="mx-2 text-white/50">/</span>
        <a href="{{ route('laporan.show', $laporan->id) }}" class="hover:underline">Detail Laporan</a>
        <span class="mx-2 text-white/50">/</span>
        <span class="font-bold">Edit Titik Lokasi</span>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-[#F4F5F9] rounded-2xl p-8 shadow-sm">
        <div class="mb-6">
            <h1 class="text-lg font-bold text-gray-900">Edit Koordinat Titik Lokasi</h1>
            <p class="text-sm text-gray-600 mt-1">
                Alamat Pelaporan: <span class="font-medium text-gray-800">{{ $laporan->lokasi ?: '-' }}</span>
            </p>
        </div>

        <form action="{{ route('laporan.update-lokasi', $laporan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Kolom Input Kiri --}}
                <div class="space-y-5 lg:col-span-1">
                    <div>
                        <label for="lintang" class="block text-sm font-medium text-gray-700 mb-1">Garis Lintang (Latitude)</label>
                        <input 
                            type="text" 
                            name="lintang" 
                            id="lintang" 
                            value="{{ old('lintang', $laporan->lintang) }}" 
                            placeholder="Contoh: -6.200000"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]"
                        />
                        @error('lintang')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="bujur" class="block text-sm font-medium text-gray-700 mb-1">Garis Bujur (Longitude)</label>
                        <input 
                            type="text" 
                            name="bujur" 
                            id="bujur" 
                            value="{{ old('bujur', $laporan->bujur) }}" 
                            placeholder="Contoh: 106.816666"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:ring-[#161446] focus:border-[#161446]"
                        />
                        @error('bujur')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl p-4 text-xs leading-relaxed shadow-sm">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p> Anda dapat mengisi koordinat secara manual atau <strong>menggeser pin biru di peta</strong> secara interaktif untuk mendapatkan akurasi lokasi yang tepat. </p>
                        </div>
                    </div>
                </div>

                {{-- Kolom Peta Kanan (Visualisasi & Interaktif) --}}
                <div class="lg:col-span-2">
                    <div class="block text-sm font-medium text-gray-700 mb-1">Peta Penentuan Lokasi (Leaflet / OpenStreetMap)</div>
                    {{-- Container Peta Interaktif Leaflet --}}
                    {{-- Z-index di-set kecil agar tidak menimpa elemen dropdown/navigasi website Anda --}}
                    <div id="map" class="w-full h-80 lg:h-96 bg-gray-200 rounded-xl border border-gray-300 shadow-sm relative overflow-hidden z-0"></div>
                </div>
            </div>

            {{-- Footer Form Actions --}}
            <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                <a href="{{ route('laporan.show', $laporan->id) }}"
                   class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-gray-800 font-medium hover:bg-gray-50 transition">
                    Batal
                </a>

                <button type="submit"
                        class="rounded-lg bg-[#161446] px-6 py-2.5 text-white font-medium hover:bg-[#110e36] transition shadow-sm">
                    Perbarui Koordinat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Memuat File CSS & Javascript dari Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

{{-- Script Logika Peta Interaktif --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latInput = document.getElementById('lintang');
        const lngInput = document.getElementById('bujur');

        // Gunakan nilai yang ada di form, atau gunakan pusat Indonesia (Jakarta) sebagai fallback
        let initialLat = parseFloat(latInput.value) || -6.200000;
        let initialLng = parseFloat(lngInput.value) || 106.816666;

        // 1. Inisialisasi Peta
        const map = L.map('map').setView([initialLat, initialLng], 13);

        // 2. Tambahkan layer OpenStreetMap (Gratis)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // 3. Tambahkan Marker (Draggable / Bisa ditarik-tarik)
        const marker = L.marker([initialLat, initialLng], {
            draggable: true,
            title: 'Geser titik ini'
        }).addTo(map);

        // 4. Update input field saat marker selesai digeser
        marker.on('dragend', function (event) {
            const position = marker.getLatLng();
            
            // Masukkan koordinat baru ke input HTML
            latInput.value = position.lat.toFixed(6);
            lngInput.value = position.lng.toFixed(6);

            // Opsional: pusatkan peta mengikuti marker yang dilepas
            map.panTo(new L.LatLng(position.lat, position.lng));
        });

        // 5. Sinkronisasi balik: jika petugas mengetik manual, marker ikut pindah
        function updateMapFromInputs() {
            let lat = parseFloat(latInput.value);
            let lng = parseFloat(lngInput.value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, map.getZoom()); // Bergeser mulus ke lokasi ketikan
            }
        }

        latInput.addEventListener('input', updateMapFromInputs);
        lngInput.addEventListener('input', updateMapFromInputs);
    });
</script>