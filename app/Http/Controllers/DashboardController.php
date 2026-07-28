<?php
// Also known as EverythingController

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Provinsi;
use App\Models\Kabupatenkota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Support\Facades\Storage;
use App\Models\Balai;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function laporanMasukBencana(Request $request)
    {
        $laporans = LaporanMasyarakat::query()
            ->when($request->search, function ($query, $search) {
                $query->where('lokasi', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('jenis_bencana', 'like', "%{$search}%")
                    ->orWhere('nama_bencana', 'like', "%{$search}%")
                    ->orWhere('pelapor', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboards.dashboard', [
            'laporans' => $laporans,
        ]);
    }

    public function edit($id)
    {
        $laporan = LaporanMasyarakat::with([
            'fotos',
            'provinsi',
            'kabupatenKota',
            'kecamatan',
            'kelurahan',
            'balais', // <-- 1. Added 'balais' relationship here
        ])->findOrFail($id);

        $provinsis = Provinsi::orderBy('nama')->get();

        $kabupatenkotas = $laporan->provinsi_id 
            ? KabupatenKota::where('provinsi_id', $laporan->provinsi_id)->orderBy('nama')->get() 
            : collect();

        $kecamatans = $laporan->kabupaten_kota_id 
            ? Kecamatan::where('kabupaten_kota_id', $laporan->kabupaten_kota_id)->orderBy('nama')->get() 
            : collect();

        $kelurahans = $laporan->kecamatan_id 
            ? Kelurahan::where('kecamatan_id', $laporan->kecamatan_id)->orderBy('nama')->get() 
            : collect();

        $balais = $laporan->provinsi_id 
            ? Balai::whereHas('provinsis', function($q) use ($laporan) {
                $q->where('provinsi_id', $laporan->provinsi_id);
            })->get()
            : collect(); // <-- Safely returns empty collection if province isn't set yet
            
        // 2. Get an array of currently assigned Balai IDs for the Blade component
        $assignedBalais = $laporan->balais->pluck('id')->toArray();

        return view('edit', [
            'laporan' => $laporan,
            'provinsis' => $provinsis,
            'kabupatenkotas' => $kabupatenkotas,
            'kecamatans' => $kecamatans,
            'kelurahans' => $kelurahans,
            'balais' => $balais,
            'assignedBalais' => $assignedBalais, // <-- Pass this to your view
        ]);
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);

        // 1. Validasi Input
        $request->validate([
            'jenis_bencana' => 'required|string|max:255',
            'nama_bencana' => 'required|string|max:255',
            'waktu_kejadian' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'lokasi' => 'required|string',
            'provinsi_id' => 'nullable|exists:provinsis,id', // Make sure provincial id is validated
            'balais' => 'nullable|array',                     // <-- Validate balais array
            'balais.*' => 'exists:balais,id',                 // <-- Ensure each selected balai exists
            'kabupaten_kota_id' => 'nullable|exists:kabupaten_kotas,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'lintang' => 'nullable|string',
            'bujur' => 'nullable|string',
            'dampak_bencana' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'kebutuhan_mendesak' => 'nullable|string',
            'hapus_foto' => 'nullable|array',
            'hapus_foto.*' => 'exists:fotos,id',
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Update Informasi Teks Laporan (Exclude balais and files from direct table update)
        $laporan->update($request->except(['fotos', 'hapus_foto', 'balais']));

        // 2.1 Sync the multiple Balais via the many-to-many relationship
        if ($request->has('balais')) {
            $laporan->balais()->sync($request->balais);
        } else {
            $laporan->balais()->detach(); // If all checkboxes are unchecked
        }

        // 3. Proses Penghapusan Foto yang dicentang oleh admin
        if ($request->has('hapus_foto')) {
            $fotosDihapus = Foto::whereIn('id', $request->hapus_foto)->get();
            foreach ($fotosDihapus as $foto) {
                // Hapus file fisik dari storage disk public
                if (Storage::disk('public')->exists($foto->file_path)) {
                    Storage::disk('public')->delete($foto->file_path);
                }
                // Hapus record dari database
                $foto->delete();
            }
        }

        // 4. Proses Penambahan Foto Baru (Jika ada yang di-upload)
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $file) {
                $path = $file->store('laporan_fotos', 'public');

                $laporan->fotos()->create([
                    'file_path' => $path
                ]);
            }
        }

        return redirect()->route('laporan.show', $laporan->id)
                        ->with('success', 'Laporan berhasil diperbarui!');
    }
    public function getKabupaten($provinsi)
    {
        return KabupatenKota::where('provinsi_id', $provinsi)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function getKecamatan($kabupaten)
    {
        return Kecamatan::where('kabupaten_kota_id', $kabupaten)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function getKelurahan($kecamatan)
    {
        return Kelurahan::where('kecamatan_id', $kecamatan)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function getBalaiByProvinsi($provinsi_id) {
        // Ganti 'wilayah_balais' menjadi 'provinsis'
        $balais = Balai::whereHas('provinsis', function($query) use ($provinsi_id) {
            $query->where('provinsis.id', $provinsi_id);
        })->get(['id', 'nama_balai']); 

        return response()->json($balais);
    }

    public function show(LaporanMasyarakat $laporan)
    {
        return view('layouts.show', [
            'laporan' => $laporan,
        ]);
    }

    public function editLokasi($id)
    {
        // Mengambil data spesifik berdasarkan ID
        $laporan = LaporanMasyarakat::findOrFail($id);
        
        // Asumsi file pembungkusnya ada di resources/views/laporan/edit-lokasi.blade.php
        // yang memanggil component @props di atas
        return view('edit-lokasi', compact('laporan'));
    }

    public function updateLokasi(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);

        // 1. Validasi nilai koordinat
        $request->validate([
            'lintang' => 'required|string|max:100',
            'bujur' => 'required|string|max:100',
        ]);

        // 2. Update koordinat Lintang dan Bujur ke dalam tabel laporan_masyarakats
        $laporan->update([
            'lintang' => $request->lintang,
            'bujur' => $request->bujur,
        ]);

        // 3. Mengarahkan kembali ke halaman detail laporan dengan notifikasi sukses
        return redirect()->route('laporan.show', $laporan->id)
                        ->with('success', 'Koordinat titik lokasi berhasil diperbarui!');
    }

    public function destroyLaporan(LaporanMasyarakat $laporan)
    {
        // If a photo was attached, clean up the stored file too.
        if ($laporan->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return redirect()
            ->route('laporan.masuk-bencana')
            ->with('status', 'Laporan berhasil dihapus.');
    }
}