<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;

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

        return view('dashboard', [
            'laporans' => $laporans,
        ]);
    }

    public function show(LaporanMasyarakat $laporan)
    {
        return view('show', [
            'laporan' => $laporan,
        ]);
    }

    public function destroy(LaporanMasyarakat $laporan)
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

        return view('laporan.penanganan-balai', [
            'laporans' => $laporans,
        ]);
    }
 
    public function dataPicBalai()
    {
        // TODO: swap for real data once the ERD/model for this exists.
        return view('data-pic-balai', ['items' => []]);
    } 
}