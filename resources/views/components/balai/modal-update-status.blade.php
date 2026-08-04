{{--
    Komponen: <x-balai.modal-update-status :laporan="$laporan" />

    Cara pakai: taruh komponen ini di halaman detail laporan, lalu buka lewat tombol
    dengan atribut onclick="document.getElementById('modal-update-status').classList.remove('hidden')"

    Prop:
    - laporan : model laporan (butuh $laporan->id, $laporan->status, $laporan->detail_status)
--}}

@props([
    'laporan',
])

<div id="modal-update-status" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('modal-update-status').classList.add('hidden')"></div>

    {{-- Modal box --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h2 class="font-bold text-gray-900 mb-4">Ganti Status Laporan</h2>

        {{-- Sesuaikan route/method kalau nama route update-status kamu beda --}}
        <form action="{{ route('balai.laporan-penanganan-balai.update-status', $laporan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <p class="text-sm font-medium text-gray-800 mb-2">Status</p>
                <div class="flex items-center gap-6">
                    @foreach (['ditangani' => 'Ditangani', 'ditutup' => 'Ditutup', 'ditolak' => 'Ditolak'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-800 cursor-pointer">
                            <input type="radio" name="status" value="{{ $value }}"
                                   class="w-4 h-4 text-[#161446] focus:ring-[#161446]"
                                   {{ old('status', $laporan->status) === $value ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label for="detail_status" class="block text-sm font-medium text-gray-800 mb-2">Detail Status</label>
                <textarea name="detail_status" id="detail_status" rows="4" placeholder="Ketik disini..."
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#161446]">{{ old('detail_status', $laporan->detail_status) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        onclick="document.getElementById('modal-update-status').classList.add('hidden')"
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-lg bg-[#161446] px-5 py-2 text-sm font-medium text-white hover:bg-[#110e36]">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>