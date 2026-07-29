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

class DashboardPelaksanaBalai extends Controller{
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