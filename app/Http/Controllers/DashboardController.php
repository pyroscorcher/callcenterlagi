<?php
// Also known as EverythingController

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use App\Models\Foto;
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
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);
        
        // Sesuaikan dengan nama folder view Anda (contoh: laporan.edit)
        return view('edit', compact('laporan'));
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

        // 2. Update Informasi Teks Laporan
        $laporan->update($request->except(['fotos', 'hapus_foto']));

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

    public function laporanPenangananBalai(Request $request)
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

        return view('laporanpenangananbalai', [
            'laporans' => $laporans,
        ]);
    }

        public function lpbshow(LaporanMasyarakat $laporan)
    {
        return view('laporanpenangananbalai-show', [
            'laporan' => $laporan,
        ]);
    }

    public function databalai()
    {
        $balais = Balai::all();

        // Memanggil master layout tunggal, tapi menyuruhnya memuat komponen 'opps.data-pic'
        return view('dashboards.datapicbalai', [
            'title' => 'Data PIC Balai - SITABA',
            'componentName' => 'opps.data-pic', // Nama komponen Blade
            'balais' => $balais                 // Data yang dibawa ke komponen
        ]);
    }

    public function createBalai()
    {
        $balais = Balai::all();
        // Memanggil master layout dinamis untuk menampilkan form tambah data
        return view('layouts.datapicbalai-show', [
            'title' => 'Tambah Data Balai - SITABA',
            'componentName' => 'opps.data-pic-create',
            'balais' => $balais // Komponen form tambah
        ]);
    }

        public function storeBalai(Request $request)
    {
        // 1. Validasi input dari form
        $validatedData = $request->validate([
            'nama_balai' => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:balais,username',
            'password'   => 'required|string|min:6',
            'unker'      => 'nullable|string|max:255',
            'unor'       => 'nullable|string|max:255',
            'provinsi'   => 'nullable|string|max:255',
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50',
        ]);

        // 2. Hash password sebelum disimpan (mengikuti pengaturan casts() di model Balai Anda)
        $validatedData['password'] = Hash::make($request->password);

        // 3. Simpan data baru ke dalam database
        Balai::create($validatedData);

        // 4. Redirect kembali dengan pesan sukses (sesuaikan rute tujuannya)
        return redirect()->route('data.pic-balai') // Ganti dengan rute halaman daftar balai Anda
                        ->with('success', 'Data Balai Bencana berhasil ditambahkan!');
    }

    public function balaiShow(Balai $balai)
    {
        // Memanggil master layout yang sama, tapi mengubah komponennya menjadi 'opps.balai-detail'
        return view('layouts.datapicbalai-show', [
            'title' => 'Detail PIC Balai - SITABA',
            'componentName' => 'opps.data-pic-show', // Nama komponen Blade detail
            'balai' => $balai                       // Data spesifik balai
        ]);
    }

    public function destroyBalai(Balai $balai)
    {
        $balai->delete();

        return redirect()
            ->route('data.pic-balai')
            ->with('status', 'Data Balai berhasil dihapus.');
    }
}