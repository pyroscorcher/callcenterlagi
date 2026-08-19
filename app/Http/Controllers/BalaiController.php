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
use App\Models\InfrastrukturTerdampak;
use App\Models\PenangananSementara;
use App\Models\PenangananSementaraFoto;
use App\Models\PenangananPermanen;
use App\Models\PenangananPermanenFoto;
use App\Models\DokumenLaporanPimpinan;


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
        $balais = Balai::orderBy('nama_balai')->get();
 
        return view('dashboards.form-laporan-bencana', [
            'mode' => 'create',
            'laporan' => null,
            'laporanBalai' => null,
            'provinsis' => $provinsis,
            'balais' => $balais,
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
            'fotos.id.*'                => 'nullable|integer',
            'fotos.file.*'              => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'fotos.keterangan.*'        => 'nullable|string',
    
            'pic'                       => 'nullable|array',
    
            'infrastruktur'                 => 'nullable|array',
            'infrastruktur.id.*'            => 'nullable|integer',
            'infrastruktur.dokumentasi.*'   => 'nullable|file|image|max:10240',
    
            'penanganan_sementara'              => 'nullable|array',
            'penanganan_sementara.id.*'         => 'nullable|integer',
            'penanganan_sementara.row_key.*'    => 'nullable|string',
            'penanganan_sementara_foto'             => 'nullable|array',
            'penanganan_sementara_foto.row_key.*'   => 'nullable|string',
            'penanganan_sementara_foto.id.*'        => 'nullable|integer',
            'penanganan_sementara_foto.file.*'      => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
    
            'sumberdaya'                => 'nullable|array',
    
            'penanganan_permanen'               => 'nullable|array',
            'penanganan_permanen.id.*'          => 'nullable|integer',
            'penanganan_permanen.row_key.*'     => 'nullable|string',
            'penanganan_permanen_foto'              => 'nullable|array',
            'penanganan_permanen_foto.row_key.*'    => 'nullable|string',
            'penanganan_permanen_foto.id.*'         => 'nullable|integer',
            'penanganan_permanen_foto.file.*'       => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
    
            'dokumen'                   => 'nullable|array',
            'dokumen.id.*'              => 'nullable|integer',
            'dokumen.file.*'            => 'nullable|file|max:20480',
        ]);
    
        $laporanData = collect($validated)->only([
            'jenis_bencana', 'nama_bencana', 'waktu_kejadian', 'wilayah_waktu',
            'lokasi', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id',
            'lintang', 'bujur', 'deskripsi', 'dampak_bencana', 'infrastruktur_terdampak', 'kebutuhan_mendesak'
        ])->toArray();
    
        $laporanData['status'] = 'ditangani';
        $laporan = LaporanMasyarakat::create($laporanData);
    
        $this->syncDokumentasiBencana($laporan, $request);
    
        $laporanBalai = LaporanBalai::create([
            'laporan_masyarakat_id' => $laporan->id,
            'balai_id'              => Auth::user()->balai->id,
            'created_by'            => Auth::id(),
            'status_terkini'        => $request->status_terkini ?? null,
            'tanggal_respon'        => $request->tanggal_respon ?? now(),
            'catatan'               => $request->catatan ?? null,
        ]);
    
        $this->syncKewenangan($laporanBalai, $request);
        $this->syncInfrastruktur($laporanBalai, $request);
        $this->syncPenangananSementara($laporanBalai, $request);
        $this->syncPenangananPermanen($laporanBalai, $request);
        $this->syncSumberdaya($laporanBalai, $request);
        $this->syncDokumenLaporanPimpinan($laporanBalai, $request);
        $this->syncPic($laporanBalai, $request);
    
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
    
        // CHANGED: was ->where('balai_id', Auth::user()->balai->id) -- that
        // scoped each Balai to their own isolated response. Now any Balai
        // assigned to this report finds and edits the SAME shared row.
        $laporanBalai = LaporanBalai::where('laporan_masyarakat_id', $laporan->id)
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
            // -- identical rules to store(), omitted here for brevity --
            // copy the same validate() array from laporanPenangananStore above.
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
            'fotos.id.*'                => 'nullable|integer',
            'fotos.file.*'              => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'fotos.keterangan.*'        => 'nullable|string',
    
            'pic'                       => 'nullable|array',
    
            'infrastruktur'                 => 'nullable|array',
            'infrastruktur.id.*'            => 'nullable|integer',
            'infrastruktur.dokumentasi.*'   => 'nullable|file|image|max:10240',
    
            'penanganan_sementara'              => 'nullable|array',
            'penanganan_sementara.id.*'         => 'nullable|integer',
            'penanganan_sementara.row_key.*'    => 'nullable|string',
            'penanganan_sementara_foto'             => 'nullable|array',
            'penanganan_sementara_foto.row_key.*'   => 'nullable|string',
            'penanganan_sementara_foto.id.*'        => 'nullable|integer',
            'penanganan_sementara_foto.file.*'      => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
    
            'sumberdaya'                => 'nullable|array',
    
            'penanganan_permanen'               => 'nullable|array',
            'penanganan_permanen.id.*'          => 'nullable|integer',
            'penanganan_permanen.row_key.*'     => 'nullable|string',
            'penanganan_permanen_foto'              => 'nullable|array',
            'penanganan_permanen_foto.row_key.*'    => 'nullable|string',
            'penanganan_permanen_foto.id.*'         => 'nullable|integer',
            'penanganan_permanen_foto.file.*'       => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
    
            'dokumen'                   => 'nullable|array',
            'dokumen.id.*'              => 'nullable|integer',
            'dokumen.file.*'            => 'nullable|file|max:20480',
        ]);
    
        $laporanData = collect($validated)->only([
            'jenis_bencana', 'nama_bencana', 'waktu_kejadian', 'wilayah_waktu',
            'lokasi', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id',
            'lintang', 'bujur', 'deskripsi', 'dampak_bencana', 'infrastruktur_terdampak', 'kebutuhan_mendesak'
        ])->toArray();
        $laporan->update($laporanData);
    
        $this->syncDokumentasiBencana($laporan, $request);
    
        $laporanBalai = LaporanBalai::firstOrCreate(
            [
                'laporan_masyarakat_id' => $laporan->id,
            ],
            [
                'balai_id'   => Auth::user()->balai->id,
                'created_by' => Auth::id(),
            ]
        );
    
        $laporanBalai->update([
            'status_terkini' => $request->status_terkini ?? $laporanBalai->status_terkini,
            'tanggal_respon' => $request->tanggal_respon ?? $laporanBalai->tanggal_respon,
            'catatan'        => $request->catatan ?? $laporanBalai->catatan,
        ]);
    
        // Kewenangan: single row, no files wired to it yet -- delete+recreate is fine here.
        $laporanBalai->kewenangan()->delete();
        $this->syncKewenangan($laporanBalai, $request);
    
        // PIC: no file fields -- delete+recreate is fine, no data-loss risk.
        $laporanBalai->picBencanas()->delete();
        $this->syncPic($laporanBalai, $request);
    
        // Everything below has file fields and/or nested children -- proper
        // id-based sync (F fix), NOT delete-then-recreate.
        $this->syncInfrastruktur($laporanBalai, $request);
        $this->syncPenangananSementara($laporanBalai, $request);
        $this->syncPenangananPermanen($laporanBalai, $request);
        $this->syncSumberdaya($laporanBalai, $request);
        $this->syncDokumenLaporanPimpinan($laporanBalai, $request);
    
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
    
    // ---------------------------------------------------------------------
    // Sync helpers
    // ---------------------------------------------------------------------
    
    /**
     * Dokumentasi Bencana photos, tied to LaporanMasyarakat->fotos().
     * id-based upsert: only overwrites file_path when a new file is actually
     * uploaded, so re-saving the form without touching a file input doesn't
     * wipe the existing photo.
     */
    private function syncDokumentasiBencana(LaporanMasyarakat $laporan, Request $request): void
    {
        $ids = $request->input('fotos.id', []);
        $keterangan = $request->input('fotos.keterangan', []);
    
        $keepIds = [];
    
        foreach ($ids as $i => $existingId) {
            $file = $request->file("fotos.file.$i");
            $ket = $keterangan[$i] ?? null;
    
            $attrs = array_filter([
                'keterangan' => $ket,
            ], fn ($v) => $v !== null && $v !== '');
    
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $attrs['file_path'] = $file->store('bencana_fotos', 'public');
            }
    
            if (!empty($existingId)) {
                if (!empty($attrs)) {
                    $laporan->fotos()->whereKey($existingId)->update($attrs);
                }
                $keepIds[] = (int) $existingId;
            } elseif (!empty($attrs['file_path'])) {
                $new = $laporan->fotos()->create($attrs);
                $keepIds[] = $new->id;
            }
        }
    
        $laporan->fotos()->whereNotIn('id', $keepIds ?: [0])->delete();
    }
    
    private function syncKewenangan(LaporanBalai $laporanBalai, Request $request): void
    {
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
    }
    
    /**
     * Infrastruktur Terdampak -- id-based upsert. dokumentasi path is only
     * overwritten when a new file is uploaded for that row.
     */
    private function syncInfrastruktur(LaporanBalai $laporanBalai, Request $request): void
    {
        if (!$request->has('infrastruktur') || !isset($request->infrastruktur['unor'])) {
            $laporanBalai->infrastrukturTerdampak()->delete();
            return;
        }
    
        $ids = $request->input('infrastruktur.id', []);
        $count = count($request->infrastruktur['unor']);
        $keepIds = [];
    
        for ($i = 0; $i < $count; $i++) {
            $unor = $request->infrastruktur['unor'][$i] ?? null;
            $kategori = $request->infrastruktur['kategori'][$i] ?? null;
    
            if (empty($unor) && empty($kategori)) {
                continue;
            }
    
            $attrs = [
                'unor'     => $unor,
                'kategori' => $kategori,
                'nama'     => $request->infrastruktur['nama'][$i] ?? null,
                'satuan'   => $request->infrastruktur['satuan'][$i] ?? null,
                'jumlah'   => $request->infrastruktur['jumlah'][$i] ?? null,
                'detail'   => $request->infrastruktur['detail'][$i] ?? null,
            ];
    
            $file = $request->file("infrastruktur.dokumentasi.$i");
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $attrs['dokumentasi'] = $file->store('infrastruktur', 'public');
            }
    
            $existingId = $ids[$i] ?? null;
    
            if (!empty($existingId)) {
                $item = InfrastrukturTerdampak::where('laporan_balai_id', $laporanBalai->id)->find($existingId);
                if ($item) {
                    $item->update($attrs);
                    $keepIds[] = $item->id;
                    continue;
                }
            }
    
            $new = $laporanBalai->infrastrukturTerdampak()->create($attrs);
            $keepIds[] = $new->id;
        }
    
        $laporanBalai->infrastrukturTerdampak()->whereNotIn('id', $keepIds ?: [0])->delete();
    }
    
    /**
     * Penanganan Sementara + its foto, correlated via row_key.
     * Pass 1: upsert each entry, build row_key -> model map.
     * Pass 2: upsert each foto against its parent via that map.
     */
    private function syncPenangananSementara(LaporanBalai $laporanBalai, Request $request): void
    {
        if (!$request->has('penanganan_sementara') || !isset($request->penanganan_sementara['kewenangan'])) {
            $laporanBalai->penangananSementara()->delete();
            return;
        }
    
        $ids = $request->input('penanganan_sementara.id', []);
        $rowKeys = $request->input('penanganan_sementara.row_key', []);
        $count = count($request->penanganan_sementara['kewenangan']);
    
        $keepEntryIds = [];
        $rowKeyToModel = [];
    
        for ($i = 0; $i < $count; $i++) {
            $kewenangan = $request->penanganan_sementara['kewenangan'][$i] ?? null;
            $tanggal = $request->penanganan_sementara['tanggal'][$i] ?? null;
    
            if (empty($kewenangan) && empty($tanggal)) {
                continue;
            }
    
            $attrs = [
                'tanggal'         => $tanggal,
                'kewenangan'      => $kewenangan,
                'jumlah_personil' => $request->penanganan_sementara['jumlah_personil'][$i] ?? null,
                'keterangan'      => $request->penanganan_sementara['keterangan'][$i] ?? null,
            ];
    
            $existingId = $ids[$i] ?? null;
            $rowKey = $rowKeys[$i] ?? null;
    
            if (!empty($existingId)) {
                $entry = PenangananSementara::where('laporan_balai_id', $laporanBalai->id)->find($existingId);
                if ($entry) {
                    $entry->update($attrs);
                }
            }
    
            if (empty($entry)) {
                $entry = $laporanBalai->penangananSementara()->create($attrs);
            }
    
            $keepEntryIds[] = $entry->id;
            if ($rowKey !== null) {
                $rowKeyToModel[$rowKey] = $entry;
            }
            $entry = null; // reset for next loop iteration
        }
    
        $laporanBalai->penangananSementara()->whereNotIn('id', $keepEntryIds ?: [0])->delete();
    
        // Pass 2: photos, regrouped by row_key back to their parent entry.
        $keepFotoIds = [];
    
        if ($request->has('penanganan_sementara_foto') && isset($request->penanganan_sementara_foto['row_key'])) {
            $fotoRowKeys = $request->penanganan_sementara_foto['row_key'];
            $fotoIds = $request->input('penanganan_sementara_foto.id', []);
            $fc = count($fotoRowKeys);
    
            for ($j = 0; $j < $fc; $j++) {
                $parentKey = $fotoRowKeys[$j] ?? null;
                $parent = $rowKeyToModel[$parentKey] ?? null;
    
                if (!$parent) {
                    continue; // orphaned photo whose parent row was removed/invalid
                }
    
                $file = $request->file("penanganan_sementara_foto.file.$j");
                $attrs = array_filter([
                    'latitude'   => $request->penanganan_sementara_foto['latitude'][$j] ?? null,
                    'longitude'  => $request->penanganan_sementara_foto['longitude'][$j] ?? null,
                    'keterangan' => $request->penanganan_sementara_foto['keterangan'][$j] ?? null,
                ], fn ($v) => $v !== null && $v !== '');
    
                if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                    $attrs['foto'] = $file->store('penanganan_sementara', 'public');
                }
    
                $existingFotoId = $fotoIds[$j] ?? null;
    
                if (!empty($existingFotoId)) {
                    $foto = PenangananSementaraFoto::where('penanganan_sementara_id', $parent->id)->find($existingFotoId);
                    if ($foto) {
                        if (!empty($attrs)) {
                            $foto->update($attrs);
                        }
                        $keepFotoIds[] = $foto->id;
                        continue;
                    }
                }
    
                if (!empty($attrs['foto'])) {
                    $newFoto = $parent->foto()->create($attrs);
                    $keepFotoIds[] = $newFoto->id;
                }
            }
        }
    
        PenangananSementaraFoto::whereIn('penanganan_sementara_id', $keepEntryIds ?: [0])
            ->whereNotIn('id', $keepFotoIds ?: [0])
            ->delete();
    }
    
    /**
     * Penanganan Permanen + its foto -- same row_key pattern as Sementara.
     */
    private function syncPenangananPermanen(LaporanBalai $laporanBalai, Request $request): void
    {
        if (!$request->has('penanganan_permanen') || !isset($request->penanganan_permanen['kewenangan'])) {
            $laporanBalai->penangananPermanen()->delete();
            return;
        }
    
        $ids = $request->input('penanganan_permanen.id', []);
        $rowKeys = $request->input('penanganan_permanen.row_key', []);
        $count = count($request->penanganan_permanen['kewenangan']);
    
        $keepEntryIds = [];
        $rowKeyToModel = [];
    
        for ($i = 0; $i < $count; $i++) {
            $kewenangan = $request->penanganan_permanen['kewenangan'][$i] ?? null;
            $tanggal = $request->penanganan_permanen['tanggal'][$i] ?? null;
    
            if (empty($kewenangan) && empty($tanggal)) {
                continue;
            }
    
            $attrs = [
                'tanggal'    => $tanggal,
                'kewenangan' => $kewenangan,
                'keterangan' => $request->penanganan_permanen['keterangan'][$i] ?? null,
            ];
    
            $existingId = $ids[$i] ?? null;
            $rowKey = $rowKeys[$i] ?? null;
            $entry = null;
    
            if (!empty($existingId)) {
                $entry = PenangananPermanen::where('laporan_balai_id', $laporanBalai->id)->find($existingId);
                if ($entry) {
                    $entry->update($attrs);
                }
            }
    
            if (empty($entry)) {
                $entry = $laporanBalai->penangananPermanen()->create($attrs);
            }
    
            $keepEntryIds[] = $entry->id;
            if ($rowKey !== null) {
                $rowKeyToModel[$rowKey] = $entry;
            }
        }
    
        $laporanBalai->penangananPermanen()->whereNotIn('id', $keepEntryIds ?: [0])->delete();
    
        $keepFotoIds = [];
    
        if ($request->has('penanganan_permanen_foto') && isset($request->penanganan_permanen_foto['row_key'])) {
            $fotoRowKeys = $request->penanganan_permanen_foto['row_key'];
            $fotoIds = $request->input('penanganan_permanen_foto.id', []);
            $fc = count($fotoRowKeys);
    
            for ($j = 0; $j < $fc; $j++) {
                $parentKey = $fotoRowKeys[$j] ?? null;
                $parent = $rowKeyToModel[$parentKey] ?? null;
    
                if (!$parent) {
                    continue;
                }
    
                $file = $request->file("penanganan_permanen_foto.file.$j");
                $attrs = array_filter([
                    'latitude'   => $request->penanganan_permanen_foto['latitude'][$j] ?? null,
                    'longitude'  => $request->penanganan_permanen_foto['longitude'][$j] ?? null,
                    'keterangan' => $request->penanganan_permanen_foto['keterangan'][$j] ?? null,
                ], fn ($v) => $v !== null && $v !== '');
    
                if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                    $attrs['foto'] = $file->store('penanganan_permanen', 'public');
                }
    
                $existingFotoId = $fotoIds[$j] ?? null;
    
                if (!empty($existingFotoId)) {
                    $foto = PenangananPermanenFoto::where('penanganan_permanen_id', $parent->id)->find($existingFotoId);
                    if ($foto) {
                        if (!empty($attrs)) {
                            $foto->update($attrs);
                        }
                        $keepFotoIds[] = $foto->id;
                        continue;
                    }
                }
    
                if (!empty($attrs['foto'])) {
                    $newFoto = $parent->foto()->create($attrs);
                    $keepFotoIds[] = $newFoto->id;
                }
            }
        }
    
        PenangananPermanenFoto::whereIn('penanganan_permanen_id', $keepEntryIds ?: [0])
            ->whereNotIn('id', $keepFotoIds ?: [0])
            ->delete();
    }
    
    /**
     * Sumberdaya (Alat & Bahan) -- report-level, no file fields, so blind
     * delete+recreate is safe here (no data-loss risk).
     */
    private function syncSumberdaya(LaporanBalai $laporanBalai, Request $request): void
    {
        $laporanBalai->alatDanBahan()->delete();
    
        if ($request->has('sumberdaya') && isset($request->sumberdaya['kategori'])) {
            $count = count($request->sumberdaya['kategori']);
            for ($s = 0; $s < $count; $s++) {
                if (!empty($request->sumberdaya['kategori'][$s])) {
                    $laporanBalai->alatDanBahan()->create([
                        'kategori' => $request->sumberdaya['kategori'][$s],
                        'kelas'    => $request->sumberdaya['kelas'][$s] ?? null,
                        'model'    => $request->sumberdaya['model'][$s] ?? null,
                        'jumlah'   => $request->sumberdaya['jumlah'][$s] ?? 0,
                    ]);
                }
            }
        }
    }
    
    /**
     * Dokumen Laporan Pimpinan -- id-based upsert (was already close to this
     * pattern; kept consistent with the rest here).
     */
    private function syncDokumenLaporanPimpinan(LaporanBalai $laporanBalai, Request $request): void
    {
        if (!$request->has('dokumen') || !isset($request->dokumen['nama_dokumen'])) {
            $laporanBalai->dokumenLaporanPimpinan()->delete();
            return;
        }
    
        $ids = $request->input('dokumen.id', []);
        $count = count($request->dokumen['nama_dokumen']);
        $keepIds = [];
    
        for ($i = 0; $i < $count; $i++) {
            $file = $request->file("dokumen.file.$i");
    
            if (empty($request->dokumen['nama_dokumen'][$i]) && !($file instanceof \Illuminate\Http\UploadedFile && $file->isValid())) {
                continue;
            }
    
            $attrs = [
                'nama_dokumen' => $request->dokumen['nama_dokumen'][$i] ?? null,
                'deskripsi'    => $request->dokumen['deskripsi'][$i] ?? null,
            ];
    
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $attrs['file_path'] = $file->store('dokumen_pimpinan', 'public');
            }
    
            $existingId = $ids[$i] ?? null;
    
            if (!empty($existingId)) {
                $dok = DokumenLaporanPimpinan::where('laporan_balai_id', $laporanBalai->id)->find($existingId);
                if ($dok) {
                    $dok->update($attrs);
                    $keepIds[] = $dok->id;
                    continue;
                }
            }
    
            $new = $laporanBalai->dokumenLaporanPimpinan()->create($attrs);
            $keepIds[] = $new->id;
        }
    
        $laporanBalai->dokumenLaporanPimpinan()->whereNotIn('id', $keepIds ?: [0])->delete();
    }
    
    /**
     * PIC -- no file fields, so delete+recreate is fine (no data-loss risk).
     * nama_pic/kontak trusted from client per your call on E (autofilled
     * client-side from the selected Balai in both create and edit views).
     */
    private function syncPic(LaporanBalai $laporanBalai, Request $request): void
    {
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
    }


    public function laporanPenangananShow($id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);
    
        if ($laporan->status === 'ditangani') {
            // CHANGED: same as edit() above -- no more balai_id filter.
            $laporanBalai = LaporanBalai::where('laporan_masyarakat_id', $laporan->id)
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