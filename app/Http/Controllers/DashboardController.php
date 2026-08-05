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
use App\Services\WhatsappNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function create()
    {
        $provinsis = Provinsi::orderBy('nama')->get();
    
        return view('layouts.laporanmasukbencana', [
            'component' => "opps.laporan-table-create",
            'provinsis' => $provinsis,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelapor'                 => 'required|string|max:255',
            'telepon'                 => 'required|string|max:255',
            'jenis_bencana'           => 'required|string|max:255',
            'nama_bencana'            => 'required|string|max:255',
            'dampak_bencana'          => 'required|string|max:255',
            'waktu_kejadian'          => 'required|string|max:255',
            'wilayah_waktu'           => 'required|string|in:WIB,WITA,WIT',
            'lokasi'                  => 'required|string|max:255',
            'provinsi_id'             => 'required|exists:provinsis,id',
            'kabupaten_kota_id'       => 'required|exists:kabupaten_kotas,id',
            'kecamatan_id'            => 'required|exists:kecamatans,id',
            'kelurahan_id'            => 'required|exists:kelurahans,id',
            'lintang'                 => 'nullable|numeric',
            'bujur'                   => 'nullable|numeric',
            'deskripsi'               => 'required|string',
            'infrastruktur_terdampak' => 'required|string|max:255',
            'kebutuhan_mendesak'      => 'nullable|string|max:255',
            'fotos'                   => 'nullable|array',
            'fotos.*'                 => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB each
        ]);
    
        // status / detail_status / validasi / verifikasi intentionally left out of
        // validation — they're not part of this form. Set a sensible default here
        // if your DB column doesn't already default it.
        $validated['status'] = 'pending';
    
        $laporan = LaporanMasyarakat::create($validated);
    
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('laporan-fotos', 'public');
    
                Foto::create([
                    'laporan_masyarakat_id' => $laporan->id,
                    'file_path'  => $path,
                ]);
            }
        }
    
        return redirect()->route('laporan.masuk-bencana')
                        ->with('success', 'Laporan berhasil ditambahkan!');
    }

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

        return view('dashboards.dashboard', [
            'laporans' => $laporans,
        ]);
    }

    public function show(LaporanMasyarakat $laporan)
    {
        return view('layouts.laporanmasukbencana', [
            'component'=> "opps.laporan-table-show",
            'laporan' => $laporan,
        ]);
    }

    public function edit($id)
    {
        $laporan = LaporanMasyarakat::with([
            'fotos',
            'provinsi',
            'kabupatenKota',
            'kecamatan',
            'kelurahan',
            'balais',
        ])->findOrFail($id);

        $provinsis = Provinsi::orderBy('nama')->get();

        $kabupatenkotas = $laporan->provinsi_id 
            ? KabupatenKota::where('provinsi_id', $laporan->provinsi_id)->orderBy('nama')->get() 
            : collect();

        $kecamatans = $laporan->kabupaten_kota_id 
            ? Kecamatan::where('kabupaten_kota_id', $laporan->kabupaten_kota_id)->orderBy('nama')->get() 
            : collect();

        $kelurahans = $laporan->kecamatan_id 
            ? Kelurahan::where('kecamatan_id', $laporan->kecamatan_id)->orderBy('nama')->get() 
            : collect();

        $balais = $laporan->provinsi_id 
            ? Balai::whereHas('provinsis', function($q) use ($laporan) {
                $q->where('provinsi_id', $laporan->provinsi_id);
            })->get()
            : collect();
            
        $assignedBalais = $laporan->balais->pluck('id')->toArray();
        
        $recommendedBalaiIds = [];
            if ($laporan->kelurahan_id) {
                $recommendedBalaiIds = DB::table('wilayah_balai')
                    ->where('kelurahan_id', $laporan->kelurahan_id)
                    ->pluck('balai_id')
                    ->toArray();
            }
        return view('layouts.laporanmasukbencana', [
            'component'=> "opps.laporan-table-edit",            
            'laporan' => $laporan,
            'provinsis' => $provinsis,
            'kabupatenkotas' => $kabupatenkotas,
            'kecamatans' => $kecamatans,
            'kelurahans' => $kelurahans,
            'balais' => $balais,
            'assignedBalais' => $assignedBalais,
            'recommendedBalaiIds' => $recommendedBalaiIds,
        ]);
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);

        $request->validate([
            'jenis_bencana' => 'required|string|max:255',
            'nama_bencana' => 'required|string|max:255',
            'waktu_kejadian' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'lokasi' => 'required|string',
            'provinsi_id' => 'nullable|exists:provinsis,id', 
            'balais' => 'nullable|array',                     
            'balais.*' => 'exists:balais,id',                 
            'kabupaten_kota_id' => 'nullable|exists:kabupaten_kotas,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'lintang' => 'nullable|string',
            'bujur' => 'nullable|string',
            'dampak_bencana' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'kebutuhan_mendesak' => 'nullable|string',
            'hapus_foto' => 'nullable|array',
            'hapus_foto.*' => 'exists:fotos,id',
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $laporan->update($request->except(['fotos', 'hapus_foto', 'balais']));

        if ($request->has('balais')) {
            $laporan->balais()->sync($request->balais);
        } else {
            $laporan->balais()->detach(); 
        }

        if ($request->has('hapus_foto')) {
            $fotosDihapus = Foto::whereIn('id', $request->hapus_foto)->get();
            foreach ($fotosDihapus as $foto) {
                if (Storage::disk('public')->exists($foto->file_path)) {
                    Storage::disk('public')->delete($foto->file_path);
                }
                $foto->delete();
            }
        }

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $file) {
                $path = $file->store('laporan_fotos', 'public');

                $laporan->fotos()->create([
                    'file_path' => $path
                ]);
            }
        }

        return redirect()->route('laporan.show', $laporan->id)
                        ->with('success', 'Laporan berhasil diperbarui!');
    }

    public function getKabupaten($provinsi)
    {
        return KabupatenKota::where('provinsi_id', $provinsi)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function getKecamatan($kabupaten)
    {
        return Kecamatan::where('kabupaten_kota_id', $kabupaten)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function getKelurahan($kecamatan)
    {
        return Kelurahan::where('kecamatan_id', $kecamatan)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function getBalaiByProvinsi(Request $request, $provinsi_id)
    {
        $balais = Balai::whereIn('id', function($query) use ($provinsi_id) {
            $query->select('balai_id')
                ->from('wilayah_balai')
                ->where('provinsi_id', $provinsi_id);
        })->get();

        $kelurahan_id = $request->query('kelurahan_id');
        
        if ($kelurahan_id) {
            $recommendedIds = DB::table('wilayah_balai')
                ->where('kelurahan_id', $kelurahan_id)
                ->pluck('balai_id')
                ->toArray();
                
            $balais->map(function($balai) use ($recommendedIds) {
                $balai->is_recommended = in_array($balai->id, $recommendedIds);
                return $balai;
            });
        } else {
            $balais->map(function($balai) {
                $balai->is_recommended = false;
                return $balai;
            });
        }
        
        return response()->json($balais);
    }

    public function editLokasi($id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);
        
        return view('layouts.laporanmasukbencana', [
            'component'=> "opps.laporan-table-leaflet",
            'laporan' => $laporan,
        ]);
    }

    public function updateLokasi(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);

        $request->validate([
            'lintang' => 'required|string|max:100',
            'bujur' => 'required|string|max:100',
        ]);

        $laporan->update([
            'lintang' => $request->lintang,
            'bujur' => $request->bujur,
        ]);

        return redirect()->route('laporan.show', $laporan->id)
                        ->with('success', 'Koordinat titik lokasi berhasil diperbarui!');
    }

    public function destroyLaporan(LaporanMasyarakat $laporan)
    {
        if ($laporan->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return redirect()
            ->route('laporan.masuk-bencana')
            ->with('status', 'Laporan berhasil dihapus.');
    }

    public function kirimPicNotifikasi(LaporanMasyarakat $laporan, WhatsappNotifier $notifier)
    {
        $laporan->load('balais.pics');

        if ($laporan->balais->isEmpty()) {
            return response()->json(['message' => 'Laporan ini belum ditugaskan ke Balai manapun.'], 422);
        }

        $kontakList = $laporan->balais->flatMap->pics->pluck('kontak')->filter()->unique()->values();

        if ($kontakList->isEmpty()) {
            return response()->json(['message' => 'Tidak ada kontak PIC yang terdaftar untuk Balai terkait.'], 422);
        }

        $daftarBalai = $laporan->balais->values()
            ->map(fn ($balai, $i) => ($i + 1) . ". {$balai->nama_balai}")
            ->implode("\n");

        $sitabaLink = route('laporan.show', $laporan->id);

        $message = <<<TEXT
    Kepada Balai Besar Wilayah Sungai / Balai Jalan / Balai Bangunan
    {$daftarBalai}
    Kami dari Call Center Bencana Kementerian Pekerjaan Umum menginformasikan adanya aduan dari masyarakat.
    Nama Masyarakat : {$laporan->pelapor}
    WA : {$laporan->telepon}
    yang di sampaikan melalui WA Center mengenai kejadian {$laporan->jenis_bencana} | {$laporan->nama_bencana}
    Lokasi Detail : {$laporan->lokasi}
    Dampak Bencana: {$laporan->dampak_bencana}
    Kebutuhan mendesak : {$laporan->kebutuhan_mendesak}
    Terkait dengan kejadian tersebut, mohon berkenan Kepala Balai dapat mengingatkan PPK untuk segera menindak lanjuti serta disampaikan kembali laporan tersebut pada Sitaba
    {$sitabaLink}
    Salam Hormat,
    Call Center Bencana Kementerian Pekerjaan Umum
    WhatsApp Center : 0815-1000-0158
    TEXT;

        $blastId = $notifier->sendBlast($kontakList->all(), $message);

        if ($blastId === null) {
            return response()->json(['message' => 'Gagal mengirim pesan ke bot WhatsApp.'], 502);
        }

        return response()->json([
            'message' => 'Pesan sedang dikirim ke ' . $kontakList->count() . ' PIC.',
            'blast_id' => $blastId,
            'total_pic' => $kontakList->count(),
        ]);
    }

    // NEW: Method to toggle verifikasi via AJAX
    public function toggleVerifikasi(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);
        
        $request->validate([
            'verifikasi' => 'required|boolean'
        ]);

        $laporan->update([
            'verifikasi' => $request->verifikasi
        ]);

        return response()->json([
            'message' => 'Status verifikasi laporan berhasil diperbarui.',
            'verifikasi' => $laporan->verifikasi
        ]);
    }
}