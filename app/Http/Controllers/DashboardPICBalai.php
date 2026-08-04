<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use App\Models\Balai;
use App\Models\Provinsi;
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
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50',
            
            // NEW: Validasi Provinsi sebagai array, maksimal 2
            'provinsi'   => 'nullable|array|max:2',
            'provinsi.*' => 'string|max:255',
            
            'pics'           => 'nullable|array',
            'pics.*.nama'    => 'required_with:pics|string|max:255',
            'pics.*.kontak'  => 'required_with:pics|string|max:50',
        ]);

        $validatedData['password'] = Hash::make($request->password);
        
        // Gabungkan array provinsi menjadi string (contoh: "Jawa Barat, Jawa Tengah")
        if (isset($validatedData['provinsi'])) {
            $validatedData['provinsi'] = implode(', ', $validatedData['provinsi']);
        }

        $balaiData = Arr::except($validatedData, ['pics']);
        
        $balai = Balai::create($balaiData);

        // NEW: Sync multiple provinces
        if ($request->filled('provinsi') && is_array($request->provinsi)) {
            $provinsiIds = Provinsi::whereIn('nama', $request->provinsi)->pluck('id')->toArray();
            $balai->provinsis()->sync($provinsiIds);
        }

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
            'provinsis' => Provinsi::orderBy('nama')->get()
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
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50', 
            
            // NEW: Validasi Provinsi sebagai array, maksimal 2
            'provinsi'   => 'nullable|array|max:2',
            'provinsi.*' => 'string|max:255',
            
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

        // Gabungkan array provinsi menjadi string
        if (isset($validatedData['provinsi'])) {
            $validatedData['provinsi'] = implode(', ', $validatedData['provinsi']);
        } else {
            $validatedData['provinsi'] = null;
        }

        $balaiData = Arr::except($validatedData, ['pics']);

        $balai->update($balaiData);

        // NEW: Sync multiple provinces
        if ($request->filled('provinsi') && is_array($request->provinsi)) {
            $provinsiIds = Provinsi::whereIn('nama', $request->provinsi)->pluck('id')->toArray();
            $balai->provinsis()->sync($provinsiIds);
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