<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use App\Models\Balai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use App\Models\Pic;

class BalaiController extends Controller
{

    public function balaiDashboard(Request $request)
    {
    $bencanaTerkini = LaporanMasyarakat::query()
        ->where('status', 'ditangani')
        ->when($request->search, function ($query, $search) {
            $query->where('lokasi', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhere('jenis_bencana', 'like', "%{$search}%")
                ->orWhere('nama_bencana', 'like', "%{$search}%")
                ->orWhere('pelapor', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(15, ['*'], 'bencana_terkini_page')
        ->withQueryString();

    $laporanMasyarakat = LaporanMasyarakat::query()
        ->where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', '')
                ->orWhere('status', 'ditolak')
                ->orWhere('status', 'ditutup');
        })
        ->when($request->search, function ($query, $search) {
            $query->where('lokasi', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhere('jenis_bencana', 'like', "%{$search}%")
                ->orWhere('nama_bencana', 'like', "%{$search}%")
                ->orWhere('pelapor', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(15, ['*'], 'laporan_masyarakat_page')
        ->withQueryString();

    return view('dashboards.balai-dashboard', [
        'bencanaTerkini' => $bencanaTerkini,
        'laporanMasyarakat' => $laporanMasyarakat,
    ]);
    }

    public function laporanPenanganan(Request $request)
    {
    $bencanaTerkini = LaporanMasyarakat::query()
        ->where('status', 'ditangani')
        ->when($request->search, function ($query, $search) {
            $query->where('lokasi', 'like', "%{$search}%")
                ->orWhere('jenis_bencana', 'like', "%{$search}%")
                ->orWhere('nama_bencana', 'like', "%{$search}%")
                ->orWhere('pelapor', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(15, ['*'], 'bencana_terkini_page')
        ->withQueryString();

    $laporanMasyarakat = LaporanMasyarakat::query()
        ->where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', 'ditolak')
                ->orWhere('status', 'ditutup');
        })
        ->when($request->search, function ($query, $search) {
            $query->where('lokasi', 'like', "%{$search}%")
                ->orWhere('jenis_bencana', 'like', "%{$search}%")
                ->orWhere('nama_bencana', 'like', "%{$search}%")
                ->orWhere('pelapor', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(15, ['*'], 'laporan_masyarakat_page')
        ->withQueryString();

    return view('dashboards.penanganan-balai', [
        'bencanaTerkini' => $bencanaTerkini,
        'laporanMasyarakat' => $laporanMasyarakat,
    ]);
    }

public function laporanPenangananCreate()
{
    return view('dashboards.form-laporan-bencana', ['laporan' => null]);
}

    public function laporanPenangananShow($id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);

        return view('dashboards.laporan-masyarakat-show', compact('laporan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ditangani,ditutup,ditolak',
            'detail_status' => 'nullable|string',
        ]);

        $laporan = LaporanMasyarakat::findOrFail($id);
        $laporan->update([
            'status' => $request->status,
            'detail_status' => $request->detail_status,
        ]);

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function laporanPenangananDestroy($id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);
        $laporan->delete();

        return redirect()->route('balai.dashboard')->with('success', 'Laporan berhasil dihapus.');
    }

    public function dataPicBalaiShow()
    {
        $balai = Auth::user()->balai;

        return view('dashboards.data-pic-balai-show', compact('balai'));
    }
    public function editProfile()
    {
        $balai = Auth::user()->balai()->with('pics')->first();
        $provinsis = Provinsi::orderBy('nama')->get();

        return view('dashboards.data-pic-balai-edit', compact('balai', 'provinsis'));
    }
    public function updateProfile(Request $request)
    {
    $balai = Auth::guard('balai')->user();

    $request->validate([
        'nama_balai' => 'required|string|max:255',
        'username'   => 'required|string|max:255|unique:balais,username,' . $balai->id,
        'password'   => 'nullable|min:6',

        'unor'       => 'required|string',
        'provinsi'   => 'required|string',
        'pulau'      => 'required|string',
        'kepala'     => 'nullable|string|max:255',
        'kontak'     => 'nullable|string|max:30',
        'pics' => 'required|array|min:1',
        'pics.*.nama' => 'required|string|max:255',
        'pics.*.kontak' => 'required|string|max:30',
    ]);

        $balai->nama_balai = $request->nama_balai;
        $balai->username   = $request->username;
        $balai->unor       = $request->unor;
        $balai->provinsi   = $request->provinsi;
        $balai->pulau      = $request->pulau;
        $balai->kepala     = $request->kepala;
        $balai->kontak     = $request->kontak;

    if ($request->filled('password')) {
        $balai->password = Hash::make($request->password);
    }

    $balai->save();

    $submittedIds = [];

    foreach ($request->pics as $picData) {

        // Jika PIC lama
        if (!empty($picData['id'])) {

            $pic = $balai->pics()->find($picData['id']);

            if ($pic) {

                $pic->update([
                    'nama' => $picData['nama'],
                    'kontak' => $picData['kontak'],
                ]);

                $submittedIds[] = $pic->id;
            }

        } else {

            // Jika PIC baru
            $pic = $balai->pics()->create([
                'nama' => $picData['nama'],
                'kontak' => $picData['kontak'],
            ]);

            $submittedIds[] = $pic->id;
        }
    }
    
    $balai->pics()
        ->whereNotIn('id', $submittedIds)
        ->delete();

    return redirect()
        ->route('balai.data-pic-balai.show', $balai->id)
        ->with('success', 'Data balai berhasil diperbarui.');
}


}