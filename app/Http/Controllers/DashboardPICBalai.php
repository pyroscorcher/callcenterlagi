<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use App\Models\Balai;
use Illuminate\Support\Facades\Hash;

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
        $validatedData = $request->validate([
            'nama_balai' => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:balais,username,' . $balai->id,
            'password'   => 'nullable|string|min:6',
            'unker'      => 'nullable|string|max:255',
            'unor'       => 'nullable|string|max:255',
            'provinsi'   => 'nullable|string|max:255',
            'pulau'      => 'nullable|string|max:255',
            
            // Validate array of PICs
            'pics'           => 'required|array|min:1',
            'pics.*.id'      => 'nullable|integer',
            'pics.*.nama'    => 'required|string|max:255',
            'pics.*.kontak'  => 'required|string|max:50',
        ]);

        // 1. Password check
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // 2. Update Balai data
        $balai->update($validatedData);

        // 3. Process PICs dynamically
        // Get all IDs that were submitted (ignore nulls)
        $submittedPicIds = collect($request->pics)->pluck('id')->filter()->toArray();

        // Delete any PICs in the database that were removed from the UI
        $balai->pics()->whereNotIn('id', $submittedPicIds)->delete();

        // Loop through the submitted array and Create or Update each row
        foreach ($request->pics as $picData) {
            $balai->pics()->updateOrCreate(
                ['id' => $picData['id'] ?? null], // If ID exists, update it. If not, create a new row.
                [
                    'nama' => $picData['nama'],
                    'kontak' => $picData['kontak']
                ]
            );
        }

        return redirect()->route('data.pic-balai-show', $balai) 
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