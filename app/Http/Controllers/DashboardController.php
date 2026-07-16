<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function laporanMasukBencana(Request $request)
    {
        // TODO: once App\Models\LaporanBencana exists (waiting on ERD from the other team),
        // replace the mock array below with something like:
        //
        // $laporans = LaporanBencana::query()
        //     ->when($request->search, fn ($q) => $q->where('lokasi_bencana', 'like', "%{$request->search}%"))
        //     ->latest('waktu_pelaporan')
        //     ->paginate(15);
        //
        // The Blade table component (<x-laporan-table>) already expects these attribute
        // names, so no view changes should be needed — just swap the data source here.

        $laporans = collect(range(1, 7))->map(function ($i) {
            return (object) [
                'id' => $i,
                'waktu_pelaporan' => '26 Juni 2026 pukul 07.00',
                'waktu_kejadian' => '26 Juni 2026 pukul 06.00',
                'jenis_bencana' => 'Tanah Longsor',
                'jenis_kejadian_bencana' => 'Longsor',
                'lokasi_bencana' => 'KABUPATEN TAPANULI TENGAH',
                'status' => 'Tidak Terdapat Penanganan',
                'pelapor' => 'Diana',
            ];
        });

        return view('dashboard', [
            'laporans' => $laporans,
        ]);
    }
}