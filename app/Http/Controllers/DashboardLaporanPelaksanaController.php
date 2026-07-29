<?php

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

class DashboardLaporanPelaksanaController extends Controller{
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
}