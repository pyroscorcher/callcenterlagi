<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use App\Models\Balai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Provinsi;
use App\Models\LaporanBalai;
use App\Models\KewenanganInfrastruktur;

class BalaiController extends Controller
{
    public function balaiDashboard(Request $request)
    {
        $balai = Auth::user()->balai;
        
        $provinsiBalai = $balai && $balai->provinsi 
            ? array_map('trim', explode(',', $balai->provinsi)) 
            : [];

        $bencanaTerkini = LaporanMasyarakat::query()
            ->whereHas('provinsi', function ($q) use ($provinsiBalai) {
                $q->whereIn('nama', $provinsiBalai);
            })
            ->where('status', 'ditangani')
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('lokasi', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('jenis_bencana', 'like', "%{$search}%")
                        ->orWhere('nama_bencana', 'like', "%{$search}%")
                        ->orWhere('pelapor', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15, ['*'], 'bencana_terkini_page')
            ->withQueryString();

        $laporanMasyarakat = LaporanMasyarakat::query()
            ->whereHas('provinsi', function ($q) use ($provinsiBalai) {
                $q->whereIn('nama', $provinsiBalai);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('lokasi', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('jenis_bencana', 'like', "%{$search}%")
                        ->orWhere('nama_bencana', 'like', "%{$search}%")
                        ->orWhere('pelapor', 'like', "%{$search}%");
                });
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
        $balai = Auth::user()->balai;
        $provinsiBalai = $balai && $balai->provinsi 
            ? array_map('trim', explode(',', $balai->provinsi)) 
            : [];

        $bencanaTerkini = LaporanMasyarakat::query()
            ->whereHas('provinsi', function ($q) use ($provinsiBalai) {
                $q->whereIn('nama', $provinsiBalai);
            })
            ->where('status', 'ditangani')
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('lokasi', 'like', "%{$search}%")
                        ->orWhere('jenis_bencana', 'like', "%{$search}%")
                        ->orWhere('nama_bencana', 'like', "%{$search}%")
                        ->orWhere('pelapor', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15, ['*'], 'bencana_terkini_page')
            ->withQueryString();

        $laporanMasyarakat = LaporanMasyarakat::query()
            ->whereHas('provinsi', function ($q) use ($provinsiBalai) {
                $q->whereIn('nama', $provinsiBalai);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('lokasi', 'like', "%{$search}%")
                        ->orWhere('jenis_bencana', 'like', "%{$search}%")
                        ->orWhere('nama_bencana', 'like', "%{$search}%")
                        ->orWhere('pelapor', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15, ['*'], 'laporan_masyarakat_page')
            ->withQueryString();

        return view('dashboards.balai-dashboard', [
            'bencanaTerkini' => $bencanaTerkini,
            'laporanMasyarakat' => $laporanMasyarakat,
        ]);
    }

    public function laporanPenangananCreate()
    {
        $provinsis = Provinsi::orderBy('nama')->get();
 
        return view('dashboards.form-laporan-bencana', [
            'mode' => 'create',
            'laporan' => null,
            'laporanBalai' => null,
            'provinsis' => $provinsis,
        ]);
    }

    public function laporanPenangananStore(Request $request)
    {
        $validated = $request->validate([
            'jenis_bencana'           => 'nullable|string',
            'nama_bencana'            => 'nullable|string',
            'waktu_kejadian'          => 'nullable|string',
            'wilayah_waktu'           => 'nullable|in:WIB,WITA,WIT',
            'lokasi'                  => 'nullable|string',
            'provinsi_id'             => 'nullable|integer|exists:provinsis,id',
            'kabupaten_kota_id'       => 'nullable|integer|exists:kabupaten_kotas,id',
            'kecamatan_id'            => 'nullable|integer|exists:kecamatans,id',
            'kelurahan_id'            => 'nullable|integer|exists:kelurahans,id',
            'lintang'                 => 'nullable|numeric',
            'bujur'                   => 'nullable|numeric',
            'deskripsi'               => 'nullable|string',
            'dampak_bencana'          => 'nullable|string',
            'infrastruktur_terdampak' => 'nullable|string',
            'kebutuhan_mendesak'      => 'nullable|string',
            
            // Kewenangan
            'tipe_kewenangan'         => 'nullable|string|in:balai,delegasi',
            'unor'                    => 'nullable|string',
            'balai_id'                => 'nullable|integer',
            'kepala'                  => 'nullable|string',
            'kontak'                  => 'nullable|string',
            'das'                     => 'nullable|string',
            'pch'                     => 'nullable|string',
            'ruas_jalan'              => 'nullable|string',
            'instansi'                => 'nullable|string',
            'penanggung_jawab'        => 'nullable|string',
            'telepon'                 => 'nullable|string',

            'status_terkini'          => 'nullable|string',
            'tanggal_respon'          => 'nullable|date',
            'catatan'                 => 'nullable|string',

            // Array Data
            'fotos'                     => 'nullable|array',
            'pic'                       => 'nullable|array',
            'infrastruktur'             => 'nullable|array',
            'penanganan_sementara'      => 'nullable|array',
            'penanganan_sementara_foto' => 'nullable|array',
            'sumberdaya'                => 'nullable|array',
            'penanganan_permanen'       => 'nullable|array',
            'penanganan_permanen_foto'  => 'nullable|array',
            'dokumen'                   => 'nullable|array',
        ]);

        $laporanData = collect($validated)->only([
            'jenis_bencana', 'nama_bencana', 'waktu_kejadian', 'wilayah_waktu', 
            'lokasi', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id', 
            'lintang', 'bujur', 'deskripsi', 'dampak_bencana', 'infrastruktur_terdampak', 'kebutuhan_mendesak'
        ])->toArray();
        
        $laporanData['status'] = 'ditangani';
        $laporan = LaporanMasyarakat::create($laporanData);

        // Save Fotos Laporan
        if ($request->has('fotos') && isset($request->fotos['keterangan'])) {
            $count = count($request->fotos['keterangan']);
            for ($i = 0; $i < $count; $i++) {
                $fotoPath = null;
                if ($request->hasFile("fotos.file.$i")) {
                    $fotoPath = $request->file("fotos.file.$i")->store('bencana_fotos', 'public');
                }
                if ($fotoPath || !empty($request->fotos['keterangan'][$i])) {
                    $laporan->fotos()->create([
                        'file_path'  => $fotoPath, // <--- FIXED
                        'keterangan' => $request->fotos['keterangan'][$i],
                    ]);
                }
            }
        }

        $laporanBalai = LaporanBalai::create([
            'laporan_masyarakat_id' => $laporan->id,
            'balai_id'              => Auth::user()->balai->id,
            'created_by'            => Auth::id(),
            'status_terkini'        => $request->status_terkini ?? null,
            'tanggal_respon'        => $request->tanggal_respon ?? now(),
            'catatan'               => $request->catatan ?? null,
        ]);

        // Kewenangan Infrastruktur
        if ($request->tipe_kewenangan === 'balai') {
            $laporanBalai->kewenangan()->create([
                'tipe' => 'balai',
                'unor' => $request->unor,
                'balai_id' => $request->balai_id,
                'kepala' => $request->kepala,
                'kontak' => $request->kontak,
            ]);
        } elseif ($request->tipe_kewenangan === 'delegasi') {
            $laporanBalai->kewenangan()->create([
                'tipe' => 'delegasi',
                'das' => $request->das,
                'pch' => $request->pch,
                'ruas_jalan' => $request->ruas_jalan,
                'instansi' => $request->instansi,
                'penanggung_jawab' => $request->penanggung_jawab,
                'telepon' => $request->telepon,
            ]);
        }

        $this->syncDynamicRelations($laporanBalai, $request);

        $balai = Auth::user()->balai;
        $laporanBalai->logs()->create([
            'action'       => 'created',
            'user_id'      => Auth::id(),
            'nama_balai'   => $balai->nama_balai ?? 'Unknown Balai',
            'kepala_balai' => $balai->kepala ?? 'Unknown Kepala',
        ]);

        return redirect()
            ->route('balai.laporan-penanganan-balai')
            ->with('success', 'Laporan beserta detail penanganan berhasil disimpan.');
    }

    public function laporanPenangananEdit($id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);
 
        $laporanBalai = LaporanBalai::where('laporan_masyarakat_id', $laporan->id)
            ->where('balai_id', Auth::user()->balai->id)
            ->with([
                'kewenangan.balai',
                'infrastrukturTerdampak',
                'penangananSementara.foto',
                'penangananSementara.alatDanBahan',
                'penangananPermanen.foto',
                'dokumenLaporanPimpinan',
                'picBencanas.balai',
                'logs',
            ])
            ->first();
 
        $provinsis = Provinsi::orderBy('nama')->get();
 
        return view('dashboards.form-laporan-bencana', [
            'mode' => 'edit',
            'laporan' => $laporan,
            'laporanBalai' => $laporanBalai,
            'provinsis' => $provinsis,
        ]);
    }

    public function laporanPenangananUpdate(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);

        $validated = $request->validate([
            'jenis_bencana'           => 'nullable|string',
            'nama_bencana'            => 'nullable|string',
            'waktu_kejadian'          => 'nullable|string',
            'wilayah_waktu'           => 'nullable|in:WIB,WITA,WIT',
            'lokasi'                  => 'nullable|string',
            'provinsi_id'             => 'nullable|integer|exists:provinsis,id',
            'kabupaten_kota_id'       => 'nullable|integer|exists:kabupaten_kotas,id',
            'kecamatan_id'            => 'nullable|integer|exists:kecamatans,id',
            'kelurahan_id'            => 'nullable|integer|exists:kelurahans,id',
            'lintang'                 => 'nullable|numeric',
            'bujur'                   => 'nullable|numeric',
            'deskripsi'               => 'nullable|string',
            'dampak_bencana'          => 'nullable|string',
            'infrastruktur_terdampak' => 'nullable|string',
            'kebutuhan_mendesak'      => 'nullable|string',
            
            'tipe_kewenangan'         => 'nullable|string|in:balai,delegasi',
            'unor'                    => 'nullable|string',
            'balai_id'                => 'nullable|integer',
            'kepala'                  => 'nullable|string',
            'kontak'                  => 'nullable|string',
            'das'                     => 'nullable|string',
            'pch'                     => 'nullable|string',
            'ruas_jalan'              => 'nullable|string',
            'instansi'                => 'nullable|string',
            'penanggung_jawab'        => 'nullable|string',
            'telepon'                 => 'nullable|string',

            'status_terkini'          => 'nullable|string',
            'tanggal_respon'          => 'nullable|date',
            'catatan'                 => 'nullable|string',

            'fotos'                     => 'nullable|array',
            'pic'                       => 'nullable|array',
            'infrastruktur'             => 'nullable|array',
            'penanganan_sementara'      => 'nullable|array',
            'penanganan_sementara_foto' => 'nullable|array',
            'sumberdaya'                => 'nullable|array',
            'penanganan_permanen'       => 'nullable|array',
            'penanganan_permanen_foto'  => 'nullable|array',
            'dokumen'                   => 'nullable|array',
        ]);

        $laporanData = collect($validated)->only([
            'jenis_bencana', 'nama_bencana', 'waktu_kejadian', 'wilayah_waktu', 
            'lokasi', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id', 
            'lintang', 'bujur', 'deskripsi', 'dampak_bencana', 'infrastruktur_terdampak', 'kebutuhan_mendesak'
        ])->toArray();
        $laporan->update($laporanData);

        // Update Fotos Laporan (Preserve old files via ID)
        if ($request->has('fotos') && isset($request->fotos['keterangan'])) {
            $keptFotoIds = [];
            $count = count($request->fotos['keterangan']);
            for ($i = 0; $i < $count; $i++) {
                $fotoId = $request->fotos['id'][$i] ?? null;
                $fotoPath = null;
                
                if ($request->hasFile("fotos.file.$i")) {
                    $fotoPath = $request->file("fotos.file.$i")->store('bencana_fotos', 'public');
                }

                if ($fotoId) {
                    $existing = \App\Models\Foto::find($fotoId);
                    if ($existing) {
                        $existing->update([
                            'keterangan' => $request->fotos['keterangan'][$i],
                            'file_path' => $fotoPath ?? $existing->file_path
                        ]);
                        $keptFotoIds[] = $existing->id;
                        continue;
                    }
                }

                if ($fotoPath || !empty($request->fotos['keterangan'][$i])) {
                    $laporan->fotos()->create([
                        'file_path'  => $fotoPath, // <--- FIXED
                        'keterangan' => $request->fotos['keterangan'][$i],
                    ]);
                }
            }
            $laporan->fotos()->whereNotIn('id', $keptFotoIds)->delete();
        } else {
            $laporan->fotos()->delete();
        }

        $laporanBalai = LaporanBalai::firstOrCreate(
            [
                'laporan_masyarakat_id' => $laporan->id,
                'balai_id'              => Auth::user()->balai->id,
            ],
            [
                'created_by' => Auth::id(),
            ]
        );

        $laporanBalai->update([
            'status_terkini' => $request->status_terkini ?? $laporanBalai->status_terkini,
            'tanggal_respon' => $request->tanggal_respon ?? $laporanBalai->tanggal_respon,
            'catatan'        => $request->catatan ?? $laporanBalai->catatan,
        ]);

        $laporanBalai->kewenangan()->delete();
        if ($request->tipe_kewenangan === 'balai') {
            $laporanBalai->kewenangan()->create([
                'tipe' => 'balai',
                'unor' => $request->unor,
                'balai_id' => $request->balai_id,
                'kepala' => $request->kepala,
                'kontak' => $request->kontak,
            ]);
        } elseif ($request->tipe_kewenangan === 'delegasi') {
            $laporanBalai->kewenangan()->create([
                'tipe' => 'delegasi',
                'das' => $request->das,
                'pch' => $request->pch,
                'ruas_jalan' => $request->ruas_jalan,
                'instansi' => $request->instansi,
                'penanggung_jawab' => $request->penanggung_jawab,
                'telepon' => $request->telepon,
            ]);
        }

        // Clean dependent rows to reinsert
        $laporanBalai->picBencanas()->delete();
        $laporanBalai->infrastrukturTerdampak()->delete();
        $laporanBalai->penangananSementara()->delete();
        $laporanBalai->penangananPermanen()->delete();

        $this->syncDynamicRelations($laporanBalai, $request);

        $balai = Auth::user()->balai;
        $laporanBalai->logs()->create([
            'action'       => 'updated',
            'user_id'      => Auth::id(),
            'nama_balai'   => $balai->nama_balai ?? 'Unknown Balai',
            'kepala_balai' => $balai->kepala ?? 'Unknown Kepala',
        ]);

        return redirect()
            ->route('balai.laporan-penanganan-balai')
            ->with('success', 'Laporan beserta seluruh detail penanganan berhasil diperbarui.');
    }

    /**
     * Shared logic for dynamically processing column-oriented table grids from the blade template.
     */
    private function syncDynamicRelations(LaporanBalai $laporanBalai, Request $request)
    {
        // 1. PIC
        if ($request->has('pic') && isset($request->pic['balai_id'])) {
            $count = count($request->pic['balai_id']);
            for ($i = 0; $i < $count; $i++) {
                if (!empty($request->pic['pic_lainnya'][$i])) {
                    $laporanBalai->picBencanas()->create(['pic_lainnya' => $request->pic['pic_lainnya'][$i]]);
                } elseif (!empty($request->pic['balai_id'][$i])) {
                    $laporanBalai->picBencanas()->create([
                        'balai_id' => $request->pic['balai_id'][$i],
                        'nama_pic' => $request->pic['nama_pic'][$i] ?? null,
                        'kontak'   => $request->pic['kontak'][$i] ?? null,
                    ]);
                }
            }
        }

        // 2. Infrastruktur Terdampak
        if ($request->has('infrastruktur') && isset($request->infrastruktur['unor'])) {
            $count = count($request->infrastruktur['unor']);
            for ($i = 0; $i < $count; $i++) {
                if (!empty($request->infrastruktur['unor'][$i]) || !empty($request->infrastruktur['kategori'][$i])) {
                    $docPath = null;
                    if ($request->hasFile("infrastruktur.dokumentasi.$i")) {
                        $docPath = $request->file("infrastruktur.dokumentasi.$i")->store('infrastruktur', 'public');
                    }
                    $laporanBalai->infrastrukturTerdampak()->create([
                        'unor'        => $request->infrastruktur['unor'][$i] ?? null,
                        'kategori'    => $request->infrastruktur['kategori'][$i] ?? null,
                        'nama'        => $request->infrastruktur['nama'][$i] ?? null,
                        'satuan'      => $request->infrastruktur['satuan'][$i] ?? null,
                        'jumlah'      => $request->infrastruktur['jumlah'][$i] ?? null,
                        'detail'      => $request->infrastruktur['detail'][$i] ?? null,
                        'dokumentasi' => $docPath,
                    ]);
                }
            }
        }

        // 3. Penanganan Sementara
        if ($request->has('penanganan_sementara') && isset($request->penanganan_sementara['kewenangan'])) {
            $count = count($request->penanganan_sementara['kewenangan']);
            for ($i = 0; $i < $count; $i++) {
                if (!empty($request->penanganan_sementara['kewenangan'][$i]) || !empty($request->penanganan_sementara['tanggal'][$i])) {
                    $ps = $laporanBalai->penangananSementara()->create([
                        'tanggal'         => $request->penanganan_sementara['tanggal'][$i] ?? null,
                        'kewenangan'      => $request->penanganan_sementara['kewenangan'][$i] ?? null,
                        'jumlah_personil' => $request->penanganan_sementara['jumlah_personil'][$i] ?? null,
                        'keterangan'      => $request->penanganan_sementara['keterangan'][$i] ?? null,
                    ]);

                    // Attach flattened elements to the first row (HTML limitation mitigation without indexing arrays)
                    if ($i === 0 && $request->has('penanganan_sementara_foto') && isset($request->penanganan_sementara_foto['keterangan'])) {
                        $fc = count($request->penanganan_sementara_foto['keterangan']);
                        for ($j = 0; $j < $fc; $j++) {
                            $fPath = null;
                            if ($request->hasFile("penanganan_sementara_foto.file.$j")) {
                                $fPath = $request->file("penanganan_sementara_foto.file.$j")->store('penanganan_sementara', 'public');
                            }
                            if ($fPath || !empty($request->penanganan_sementara_foto['keterangan'][$j])) {
                                $ps->foto()->create([
                                    'foto'       => $fPath,
                                    'latitude'   => $request->penanganan_sementara_foto['latitude'][$j] ?? null,
                                    'longitude'  => $request->penanganan_sementara_foto['longitude'][$j] ?? null,
                                    'keterangan' => $request->penanganan_sementara_foto['keterangan'][$j] ?? null,
                                ]);
                            }
                        }
                    }

                    if ($i === 0 && $request->has('sumberdaya') && isset($request->sumberdaya['kategori'])) {
                        $sc = count($request->sumberdaya['kategori']);
                        for ($s = 0; $s < $sc; $s++) {
                            if (!empty($request->sumberdaya['kategori'][$s])) {
                                $ps->alatDanBahan()->create([
                                    'kategori' => $request->sumberdaya['kategori'][$s],
                                    'kelas'    => $request->sumberdaya['kelas'][$s] ?? null,
                                    'model'    => $request->sumberdaya['model'][$s] ?? null,
                                    'jumlah'   => $request->sumberdaya['jumlah'][$s] ?? 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // 4. Penanganan Permanen
        if ($request->has('penanganan_permanen') && isset($request->penanganan_permanen['kewenangan'])) {
            $count = count($request->penanganan_permanen['kewenangan']);
            for ($i = 0; $i < $count; $i++) {
                if (!empty($request->penanganan_permanen['kewenangan'][$i]) || !empty($request->penanganan_permanen['tanggal'][$i])) {
                    $pp = $laporanBalai->penangananPermanen()->create([
                        'tanggal'    => $request->penanganan_permanen['tanggal'][$i] ?? null,
                        'kewenangan' => $request->penanganan_permanen['kewenangan'][$i] ?? null,
                        'keterangan' => $request->penanganan_permanen['keterangan'][$i] ?? null,
                    ]);

                    if ($i === 0 && $request->has('penanganan_permanen_foto') && isset($request->penanganan_permanen_foto['keterangan'])) {
                        $fc = count($request->penanganan_permanen_foto['keterangan']);
                        for ($j = 0; $j < $fc; $j++) {
                            $fPath = null;
                            if ($request->hasFile("penanganan_permanen_foto.file.$j")) {
                                $fPath = $request->file("penanganan_permanen_foto.file.$j")->store('penanganan_permanen', 'public');
                            }
                            
                            if ($fPath || !empty($request->penanganan_permanen_foto['keterangan'][$j])) {
                                $pp->foto()->create([
                                    'foto'       => $fPath,
                                    'latitude'   => $request->penanganan_permanen_foto['latitude'][$j] ?? null,
                                    'longitude'  => $request->penanganan_permanen_foto['longitude'][$j] ?? null,
                                    'keterangan' => $request->penanganan_permanen_foto['keterangan'][$j] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // 5. Dokumen Laporan Pimpinan (File retention enabled using specific table IDs)
        if ($request->has('dokumen') && isset($request->dokumen['nama_dokumen'])) {
            $keptDokumenIds = [];
            $count = count($request->dokumen['nama_dokumen']);
            
            for ($i = 0; $i < $count; $i++) {
                if (empty($request->dokumen['nama_dokumen'][$i]) && !$request->hasFile("dokumen.file.$i")) {
                    continue;
                }
                
                $dokId = $request->dokumen['id'][$i] ?? null;
                $filePath = null;

                if ($request->hasFile("dokumen.file.$i")) {
                    $filePath = $request->file("dokumen.file.$i")->store('dokumen_pimpinan', 'public');
                }

                if ($dokId) {
                    $existingDok = \App\Models\DokumenLaporanPimpinan::find($dokId);
                    if ($existingDok) {
                        $existingDok->update([
                            'nama_dokumen' => $request->dokumen['nama_dokumen'][$i],
                            'deskripsi'    => $request->dokumen['deskripsi'][$i],
                            'file_path'    => $filePath ?? $existingDok->file_path,
                        ]);
                        $keptDokumenIds[] = $existingDok->id;
                        continue;
                    }
                }

                $newDok = $laporanBalai->dokumenLaporanPimpinan()->create([
                    'nama_dokumen' => $request->dokumen['nama_dokumen'][$i],
                    'deskripsi'    => $request->dokumen['deskripsi'][$i],
                    'file_path'    => $filePath,
                ]);
                $keptDokumenIds[] = $newDok->id;
            }
            $laporanBalai->dokumenLaporanPimpinan()->whereNotIn('id', $keptDokumenIds)->delete();
        } else {
            $laporanBalai->dokumenLaporanPimpinan()->delete();
        }
    }

    public function laporanPenangananShow($id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);
 
        if ($laporan->status === 'ditangani') {
            $laporanBalai = LaporanBalai::where('laporan_masyarakat_id', $laporan->id)
                ->where('balai_id', Auth::user()->balai->id)
                ->with([
                    'kewenangan.balai',
                    'infrastrukturTerdampak',
                    'penangananSementara.foto',
                    'penangananSementara.alatDanBahan',
                    'penangananPermanen.foto',
                    'dokumenLaporanPimpinan',
                    'picBencanas.balai',
                    'logs',
                ])
                ->first();
 
            return view('dashboards.form-laporan-bencana', [
                'mode' => 'detail',
                'laporan' => $laporan,
                'laporanBalai' => $laporanBalai,
            ]);
        }
 
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

        if ($request->status === 'ditangani') {
            return redirect()
                ->route('balai.laporan-penanganan-balai.edit', $laporan->id)
                ->with('success', 'Status laporan diubah menjadi ditangani.');
        }

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

        return view('dashboards.data-pic-balai-edit', [
            'balai' => $balai,
            'provinsis' => $provinsis,
            'authUserId' => Auth::id(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $balai = Auth::user()->balai;

        if (! $balai) {
            abort(404, 'Balai tidak ditemukan untuk akun ini.');
        }

        $validated = $request->validate([
            'nama_balai' => 'required|string|max:255',
            'unor'       => 'required|string',
            'provinsi'   => 'required|array|max:2',
            'provinsi.*' => 'string|max:255',
            'pulau'      => 'required|string',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:30',

            'pics'            => 'required|array|min:1',
            'pics.*.id'       => 'nullable|integer',
            'pics.*.nama'     => 'required|string|max:255',
            'pics.*.username' => 'required|string|max:255',
            'pics.*.password' => 'nullable|string|min:6',
            'pics.*.kontak'   => 'required|string|max:30',
        ]);

        $usernamesSeen = [];
        foreach ($request->pics as $index => $picData) {
            $username = $picData['username'];
            $picId    = $picData['id'] ?? null;

            if (isset($usernamesSeen[$username])) {
                throw ValidationException::withMessages([
                    "pics.{$index}.username" => 'Username PIC tidak boleh sama satu sama lain.',
                ]);
            }
            $usernamesSeen[$username] = true;

            $conflict = User::where('username', $username)
                ->when($picId, fn ($q) => $q->where('id', '!=', $picId))
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    "pics.{$index}.username" => "Username \"{$username}\" sudah digunakan.",
                ]);
            }

            if (empty($picId) && empty($picData['password'] ?? null)) {
                throw ValidationException::withMessages([
                    "pics.{$index}.password" => 'Password wajib diisi untuk PIC baru.',
                ]);
            }
        }

        $submittedIds = collect($request->pics)->pluck('id')->filter()->map(fn ($id) => (int) $id)->toArray();
        if (! in_array(Auth::id(), $submittedIds, true)) {
            throw ValidationException::withMessages([
                'pics' => 'Anda tidak dapat menghapus akun Anda sendiri dari daftar PIC.',
            ]);
        }

        $balai->update([
            'nama_balai' => $validated['nama_balai'],
            'unor'       => $validated['unor'],
            'provinsi'   => implode(', ', $validated['provinsi']),
            'pulau'      => $validated['pulau'],
            'kepala'     => $validated['kepala'] ?? null,
            'kontak'     => $validated['kontak'] ?? null,
        ]);

        User::where('balai_id', $balai->id)
            ->where('role', 'pic')
            ->whereNotIn('id', $submittedIds ?: [0])
            ->delete();

        foreach ($request->pics as $picData) {
            $attributes = [
                'name'     => $picData['nama'],
                'username' => $picData['username'],
                'kontak'   => $picData['kontak'],
                'role'     => 'pic',
                'balai_id' => $balai->id,
            ];

            if (!empty($picData['password'])) {
                $attributes['password'] = Hash::make($picData['password']);
            }

            User::updateOrCreate(
                ['id' => $picData['id'] ?? null],
                $attributes
            );
        }

        return redirect()
            ->route('balai.data-pic-balai.show')
            ->with('success', 'Data balai berhasil diperbarui.');
    }
}