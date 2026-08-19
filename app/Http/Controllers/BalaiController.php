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
        $fotosKeterangan = $request->input('fotos.keterangan', []);
        $fotosFiles = $request->file('fotos.file', []);
        
        // Normalize to arrays safely
        if (!is_array($fotosKeterangan)) $fotosKeterangan = (array) $fotosKeterangan;
        if (!is_array($fotosFiles)) $fotosFiles = (array) $fotosFiles;

        // Use the max count to ensure we loop through everything, even if some inputs are missing
        $count = max(count($fotosKeterangan), count($fotosFiles));

        for ($i = 0; $i < $count; $i++) {
            $fotoPath = null;
            
            if ($request->hasFile("fotos.file.$i")) {
                $fotoPath = $request->file("fotos.file.$i")->store('bencana_fotos', 'public');
            }
            
            $keterangan = $fotosKeterangan[$i] ?? null;

            // Only create if a file was uploaded OR a keterangan was typed
            if ($fotoPath || !empty($keterangan)) {
                $laporan->fotos()->create([
                    'file_path'  => $fotoPath,
                    'keterangan' => $keterangan,
                ]);
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
        $fotosKeterangan = $request->input('fotos.keterangan', []);
        $fotosFiles = $request->file('fotos.file', []);
        $fotosIds = $request->input('fotos.id', []);
        
        if (!is_array($fotosKeterangan)) $fotosKeterangan = (array) $fotosKeterangan;
        if (!is_array($fotosFiles)) $fotosFiles = (array) $fotosFiles;
        if (!is_array($fotosIds)) $fotosIds = (array) $fotosIds;

        $count = max(count($fotosKeterangan), count($fotosFiles), count($fotosIds));
        $keptFotoIds = [];

        for ($i = 0; $i < $count; $i++) {
            $fotoId = $fotosIds[$i] ?? null;
            $fotoPath = null;
            
            if ($request->hasFile("fotos.file.$i")) {
                $fotoPath = $request->file("fotos.file.$i")->store('bencana_fotos', 'public');
            }
            
            $keterangan = $fotosKeterangan[$i] ?? null;

            // Update existing photo record
            if ($fotoId) {
                $existing = \App\Models\Foto::find($fotoId);
                if ($existing) {
                    $existing->update([
                        'keterangan' => $keterangan,
                        'file_path' => $fotoPath ?? $existing->file_path
                    ]);
                    $keptFotoIds[] = $existing->id;
                    continue; // Move to next iteration
                }
            }

            // Create new photo record
            if ($fotoPath || !empty($keterangan)) {
                $newFoto = $laporan->fotos()->create([
                    'file_path'  => $fotoPath,
                    'keterangan' => $keterangan,
                ]);
                $keptFotoIds[] = $newFoto->id;
            }
        }

        // Clean up deleted photos
        // (Laravel's whereNotIn ignores empty arrays, so we must handle it explicitly)
        if (empty($keptFotoIds)) {
            $laporan->fotos()->delete();
        } else {
            $laporan->fotos()->whereNotIn('id', $keptFotoIds)->delete();
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
        $keptPicIds = [];
        if ($request->has('pic') && isset($request->pic['balai_id'])) {
            $count = count($request->pic['balai_id']);
            for ($i = 0; $i < $count; $i++) {
                $id = $request->pic['id'][$i] ?? null;
                $data = [
                    'pic_lainnya' => $request->pic['pic_lainnya'][$i] ?? null,
                    'balai_id'    => $request->pic['balai_id'][$i] ?? null,
                    'nama_pic'    => $request->pic['nama_pic'][$i] ?? null,
                    'kontak'      => $request->pic['kontak'][$i] ?? null,
                ];

                if (empty($data['pic_lainnya']) && empty($data['balai_id'])) continue;

                $pic = $laporanBalai->picBencanas()->updateOrCreate(['id' => $id], $data);
                $keptPicIds[] = $pic->id;
            }
        }
        empty($keptPicIds) ? $laporanBalai->picBencanas()->delete() : $laporanBalai->picBencanas()->whereNotIn('id', $keptPicIds)->delete();

        // 2. Infrastruktur Terdampak
        $keptInfraIds = [];
        if ($request->has('infrastruktur') && isset($request->infrastruktur['unor'])) {
            $count = count($request->infrastruktur['unor']);
            for ($i = 0; $i < $count; $i++) {
                if (empty($request->infrastruktur['unor'][$i]) && empty($request->infrastruktur['kategori'][$i])) continue;

                $id = $request->infrastruktur['id'][$i] ?? null;
                $docPath = null;
                
                // Read from the flattened file array
                if ($request->hasFile("infrastruktur_dokumentasi.$i")) {
                    $docPath = $request->file("infrastruktur_dokumentasi.$i")->store('infrastruktur', 'public');
                }

                if ($id) {
                    $existing = $laporanBalai->infrastrukturTerdampak()->find($id);
                    if ($existing) {
                        $existing->update([
                            'unor'        => $request->infrastruktur['unor'][$i] ?? null,
                            'kategori'    => $request->infrastruktur['kategori'][$i] ?? null,
                            'nama'        => $request->infrastruktur['nama'][$i] ?? null,
                            'satuan'      => $request->infrastruktur['satuan'][$i] ?? null,
                            'jumlah'      => $request->infrastruktur['jumlah'][$i] ?? null,
                            'detail'      => $request->infrastruktur['detail'][$i] ?? null,
                            'dokumentasi' => $docPath ?? $existing->dokumentasi,
                        ]);
                        $keptInfraIds[] = $existing->id;
                        continue;
                    }
                }

                $new = $laporanBalai->infrastrukturTerdampak()->create([
                    'unor'        => $request->infrastruktur['unor'][$i] ?? null,
                    'kategori'    => $request->infrastruktur['kategori'][$i] ?? null,
                    'nama'        => $request->infrastruktur['nama'][$i] ?? null,
                    'satuan'      => $request->infrastruktur['satuan'][$i] ?? null,
                    'jumlah'      => $request->infrastruktur['jumlah'][$i] ?? null,
                    'detail'      => $request->infrastruktur['detail'][$i] ?? null,
                    'dokumentasi' => $docPath,
                ]);
                $keptInfraIds[] = $new->id;
            }
        }
        empty($keptInfraIds) ? $laporanBalai->infrastrukturTerdampak()->delete() : $laporanBalai->infrastrukturTerdampak()->whereNotIn('id', $keptInfraIds)->delete();

        // 3. Sumberdaya (Alat & Bahan) - Fixed Point B (Now attached directly to LaporanBalai)
        $keptSdIds = [];
        if ($request->has('sumberdaya') && isset($request->sumberdaya['kategori'])) {
            $count = count($request->sumberdaya['kategori']);
            for ($i = 0; $i < $count; $i++) {
                if (empty($request->sumberdaya['kategori'][$i])) continue;

                $sd = $laporanBalai->alatDanBahan()->updateOrCreate(
                    ['id' => $request->sumberdaya['id'][$i] ?? null],
                    [
                        'kategori' => $request->sumberdaya['kategori'][$i],
                        'kelas'    => $request->sumberdaya['kelas'][$i] ?? null,
                        'model'    => $request->sumberdaya['model'][$i] ?? null,
                        'jumlah'   => $request->sumberdaya['jumlah'][$i] ?? 0,
                    ]
                );
                $keptSdIds[] = $sd->id;
            }
        }
        empty($keptSdIds) ? $laporanBalai->alatDanBahan()->delete() : $laporanBalai->alatDanBahan()->whereNotIn('id', $keptSdIds)->delete();

        // 4. Penanganan Sementara + Grouped Photos (Fixed Point C)
        $this->syncPenanganan(
            $laporanBalai, 
            $laporanBalai->penangananSementara(), 
            $request->input('penanganan_sementara', []), 
            $request->input('penanganan_sementara_foto', []), 
            $request->file('penanganan_sementara_foto_file', []),
            'penanganan_sementara'
        );

        // 5. Penanganan Permanen + Grouped Photos
        $this->syncPenanganan(
            $laporanBalai, 
            $laporanBalai->penangananPermanen(), 
            $request->input('penanganan_permanen', []), 
            $request->input('penanganan_permanen_foto', []), 
            $request->file('penanganan_permanen_foto_file', []),
            'penanganan_permanen'
        );

        // 6. Dokumen Pimpinan (Your existing working code)
        // [Paste your existing $request->has('dokumen') block here...]
    }

    /**
     * Shared Helper for syncing Penanganan Sementara/Permanen + Grouping nested photos via row_key
     */
    private function syncPenanganan($laporanBalai, $relation, $inputParams, $inputFotos, $fileFotos, $folderPath)
    {
        $keptIds = [];
        if (isset($inputParams['kewenangan'])) {
            
            // Map flat photos array into grouped clusters by row_key
            $fotosByRowKey = collect($inputFotos['row_key'] ?? [])->map(function($rKey, $idx) use ($inputFotos, $fileFotos) {
                return [
                    'id'         => $inputFotos['id'][$idx] ?? null,
                    'row_key'    => $rKey,
                    'latitude'   => $inputFotos['latitude'][$idx] ?? null,
                    'longitude'  => $inputFotos['longitude'][$idx] ?? null,
                    'keterangan' => $inputFotos['keterangan'][$idx] ?? null,
                    'file'       => $fileFotos[$idx] ?? null,
                ];
            })->groupBy('row_key');

            $count = count($inputParams['kewenangan']);
            for ($i = 0; $i < $count; $i++) {
                if (empty($inputParams['kewenangan'][$i]) && empty($inputParams['tanggal'][$i])) continue;

                // Upsert Parent Row
                $model = $relation->updateOrCreate(
                    ['id' => $inputParams['id'][$i] ?? null],
                    [
                        'tanggal'         => $inputParams['tanggal'][$i] ?? null,
                        'kewenangan'      => $inputParams['kewenangan'][$i] ?? null,
                        'jumlah_personil' => $inputParams['jumlah_personil'][$i] ?? null, // Will map safely even if absent on Permanen
                        'keterangan'      => $inputParams['keterangan'][$i] ?? null,
                    ]
                );
                $keptIds[] = $model->id;

                // Sync nested photos mapping to this exact row's row_key
                $keptFotoIds = [];
                $rKey = $inputParams['row_key'][$i] ?? null;
                $rowFotos = $fotosByRowKey->get($rKey, []);

                foreach ($rowFotos as $fData) {
                    $fId = $fData['id'] ?? null;
                    $fPath = $fData['file'] ? $fData['file']->store($folderPath, 'public') : null;

                    if ($fId) {
                        $exFoto = $model->foto()->find($fId);
                        if ($exFoto) {
                            $exFoto->update([
                                'foto'       => $fPath ?? $exFoto->foto,
                                'latitude'   => $fData['latitude'],
                                'longitude'  => $fData['longitude'],
                                'keterangan' => $fData['keterangan'],
                            ]);
                            $keptFotoIds[] = $exFoto->id;
                            continue;
                        }
                    }

                    if ($fPath || !empty($fData['keterangan'])) {
                        $newF = $model->foto()->create([
                            'foto'       => $fPath,
                            'latitude'   => $fData['latitude'],
                            'longitude'  => $fData['longitude'],
                            'keterangan' => $fData['keterangan'],
                        ]);
                        $keptFotoIds[] = $newF->id;
                    }
                }
                empty($keptFotoIds) ? $model->foto()->delete() : $model->foto()->whereNotIn('id', $keptFotoIds)->delete();
            }
        }
        empty($keptIds) ? $relation->delete() : $relation->whereNotIn('id', $keptIds)->delete();
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