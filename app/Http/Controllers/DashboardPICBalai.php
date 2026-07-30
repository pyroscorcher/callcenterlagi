<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use App\Models\Balai;
use App\Models\Provinsi; // <-- Pastikan model Provinsi di-import
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class DashboardPICBalai extends Controller
{
    public function databalai()
    {
        $balais = Balai::all();

        return view('dashboards.datapicbalai', [
            'title' => 'Data PIC Balai - SITABA',
            'componentName' => 'opps.data-pic', 
            'balais' => $balais                
        ]);
    }

    public function createBalai()
    {
        return view('layouts.laporanpicbalai', [
            'title' => 'Tambah Data Balai - SITABA',
            'componentName' => 'opps.data-pic-create',
            'provinsis' => Provinsi::orderBy('nama')->get()
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
            
            // Validasi array PIC (dibuat nullable saat create)
            'pics'           => 'nullable|array',
            'pics.*.nama'    => 'required_with:pics|string|max:255',
            'pics.*.kontak'  => 'required_with:pics|string|max:50',
        ]);

        $validatedData['password'] = Hash::make($request->password);
        
        // Pisahkan data 'pics'
        $balaiData = Arr::except($validatedData, ['pics']);
        
        // Buat Balai Baru
        $balai = Balai::create($balaiData);

        if ($request->filled('provinsi')) {
            $provinsiRecord = Provinsi::where('nama', $request->provinsi)->first();
            if ($provinsiRecord) {
                $balai->provinsis()->sync([$provinsiRecord->id]);
            }
        }

        // ==========================================
        // PROSES PENYIMPANAN PICS (Hanya jika ada)
        // ==========================================
        if ($request->has('pics') && is_array($request->pics)) {
            foreach ($request->pics as $picData) {
                $balai->pics()->create([
                    'nama' => $picData['nama'],
                    'kontak' => $picData['kontak']
                ]);
            }
        }

        return redirect()->route('data.pic-balai') 
                         ->with('success', 'Data Balai Bencana berhasil ditambahkan!');
    }

    public function balaiShow(Balai $balai)
    {
        // Eager load pics agar tidak terjadi N+1 query
        $balai->load('pics');

        return view('layouts.laporanpicbalai', [
            'title' => 'Detail PIC Balai - SITABA',
            'componentName' => 'opps.data-pic-show',
            'balai' => $balai                       
        ]);
    }

    public function editBalai(Balai $balai)
    {
        return view('layouts.laporanpicbalai', [
            'title' => 'Edit Data Balai - SITABA',
            'componentName' => 'opps.data-pic-edit',
            'balai' => $balai,
            'provinsis' => Provinsi::orderBy('nama')->get() // <-- Bawa data provinsi untuk dropdown
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
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50', 
            
            // Validasi array PIC
            'pics'           => 'required|array|min:1',
            'pics.*.id'      => 'nullable|integer',
            'pics.*.nama'    => 'required|string|max:255',
            'pics.*.kontak'  => 'required|string|max:50',
        ]);

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        $balaiData = Arr::except($validatedData, ['pics']);

        // Update Balai
        $balai->update($balaiData);

        // ==========================================
        // AUTOMATIC PROVINSI LINKING
        // ==========================================
        if ($request->filled('provinsi')) {
            $provinsiRecord = Provinsi::where('nama', $request->provinsi)->first();
            if ($provinsiRecord) {
                $balai->provinsis()->sync([$provinsiRecord->id]);
            } else {
                $balai->provinsis()->detach(); 
            }
        } else {
            $balai->provinsis()->detach();
        }

        $submittedPicIds = collect($request->pics)->pluck('id')->filter()->toArray();
        $balai->pics()->whereNotIn('id', $submittedPicIds)->delete();

        foreach ($request->pics as $picData) {
            $balai->pics()->updateOrCreate(
                ['id' => $picData['id'] ?? null], 
                [
                    'nama' => $picData['nama'],
                    'kontak' => $picData['kontak']
                ]
            );
        }

        return redirect()->route('data.pic-balai-show', $balai->id) 
                         ->with('success', 'Data Balai Bencana berhasil diperbarui!');
    }

    public function destroyBalai(Balai $balai)
    {
        $balai->delete();
        return redirect()->route('data.pic-balai')->with('status', 'Data Balai berhasil dihapus.');
    }
}