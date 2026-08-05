<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;

class DashboardLaporanPelaksanaController extends Controller
{
    public function LPB(Request $request)
    {
        // 1. Laporan yang sedang "ditangani"
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

        // 2. Semua Laporan Masuk (Tanpa filter status)
        $laporanMasyarakat = LaporanMasyarakat::query()
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

        return view('dashboards.penangananbalai', [
            'bencanaTerkini' => $bencanaTerkini,
            'laporanMasyarakat' => $laporanMasyarakat,
        ]);
    }
}