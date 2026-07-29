<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use App\Models\Balai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class DashboardPICBalai extends Controller{
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
            'username'   => 'required|string|max:255|unique:balais,username,' . $balai->id,
            'password'   => 'nullable|string|min:6',
            'unker'      => 'nullable|string|max:255',
            'unor'       => 'nullable|string|max:255',
            'provinsi'   => 'nullable|string|max:255',
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255', // <-- Restored Kepala Balai
            'kontak'     => 'nullable|string|max:50',  // <-- Restored Kontak Kepala Balai
            
            // Validasi array untuk daftar PIC tambahan
            'pics'           => 'required|array|min:1',
            'pics.*.id'      => 'nullable|integer',
            'pics.*.nama'    => 'required|string|max:255',
            'pics.*.kontak'  => 'required|string|max:50',
        ]);

        // 2. Cek apakah admin mengisi password baru
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // 3. Pisahkan data 'pics' agar tidak error saat update model Balai
        $balaiData = Arr::except($validatedData, ['pics']);

        // 4. Update data Balai ke database (termasuk kepala dan kontak)
        $balai->update($balaiData);

        // 5. Proses Daftar PIC Tambahan secara dinamis
        // Ambil semua ID PIC yang dikirim dari form (buang yang null/kosong)
        $submittedPicIds = collect($request->pics)->pluck('id')->filter()->toArray();

        // Hapus PIC di database yang tidak ada di form (karena dihapus oleh user via tombol remove)
        $balai->pics()->whereNotIn('id', $submittedPicIds)->delete();

        // Loop data dari form untuk Update (jika punya ID) atau Create (jika baru)
        foreach ($request->pics as $picData) {
            $balai->pics()->updateOrCreate(
                ['id' => $picData['id'] ?? null], 
                [
                    'nama' => $picData['nama'],
                    'kontak' => $picData['kontak']
                ]
            );
        }

        // 6. Redirect kembali dengan pesan sukses
        return redirect()->route('data.pic-balai-show', $balai->id) 
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