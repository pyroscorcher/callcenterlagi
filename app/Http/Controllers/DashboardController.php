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

    public function LPB(Request $request)
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

        return view('dashboards.penangananbalai', [
            'laporans' => $laporans,
        ]);
    }

        public function LPBShow(LaporanMasyarakat $laporan)
    {
        return view('laporanpenangananbalai-show', [
            'laporan' => $laporan,
        ]);
    }

    public function databalai()
    {
        $balais = Balai::all();

        return view('dashboards.datapicbalai', [
            'title' => 'Data PIC Balai - SITABA',
            'componentName' => 'opps.data-pic', // Nama komponen Blade
            'balais' => $balais                 // Data yang dibawa ke komponen
        ]);
    }

    public function createBalai()
    {
        $balais = Balai::all();
        return view('layouts.datapicbalai-show', [
            'title' => 'Tambah Data Balai - SITABA',
            'componentName' => 'opps.data-pic-create',
            'balais' => $balais // Komponen form tambah
        ]);
    }

        public function storeBalai(Request $request)
    {
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

        $validatedData['password'] = Hash::make($request->password);

        Balai::create($validatedData);

        return redirect()->route('data.pic-balai') // Ganti dengan rute halaman daftar balai Anda
                        ->with('success', 'Data Balai Bencana berhasil ditambahkan!');
    }

    public function balaiShow(Balai $balai)
    {
        return view('layouts.datapicbalai-show', [
            'title' => 'Detail PIC Balai - SITABA',
            'componentName' => 'opps.data-pic-show', // Nama komponen Blade detail
            'balai' => $balai                       // Data spesifik balai
        ]);
    }

    public function editBalai(Balai $balai)
    {
        return view('layouts.datapicbalai-show', [
            'title' => 'Edit Data Balai - SITABA',
            'componentName' => 'opps.data-pic-edit', // Komponen edit yang akan kita buat
            'balai' => $balai                        // Bawa data spesifik balai
        ]);
    }

    public function updateBalai(Request $request, Balai $balai)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'nama_balai' => 'required|string|max:255',
            // Kecualikan ID balai saat ini agar username bisa tetap sama
            'username'   => 'required|string|max:255|unique:balais,username,' . $balai->id,
            // Password dibuat nullable (opsional saat edit)
            'password'   => 'nullable|string|min:6',
            'unker'      => 'nullable|string|max:255',
            'unor'       => 'nullable|string|max:255',
            'provinsi'   => 'nullable|string|max:255',
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50',
        ]);

        // 2. Cek apakah admin mengisi password baru
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            // Jika kosong, hapus dari array agar password lama tidak tertimpa string kosong
            unset($validatedData['password']);
        }

        // 3. Update data ke database
        $balai->update($validatedData);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('data.pic-balai-show') // Sesuaikan dengan route list balai Anda
                        ->with('success', 'Data Balai Bencana berhasil diperbarui!');
    }

    public function destroyBalai(Balai $balai)
    {
        $balai->delete();

        return redirect()
            ->route('data.pic-balai')
            ->with('status', 'Data Balai berhasil dihapus.');
    }
}