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

class BalaiController extends Controller
{

    public function balaiDashboard(Request $request)
    {
        $balai = Auth::user()->balai;
        
        $provinsiBalai = $balai && $balai->provinsi 
            ? array_map('trim', explode(',', $balai->provinsi)) 
            : [];

        // --- TAB BENCANA TERKINI (Khusus status: ditangani) ---
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

        // --- TAB LAPORAN MASYARAKAT (Semua status ditampilkan) ---
        $laporanMasyarakat = LaporanMasyarakat::query()
            ->whereHas('provinsi', function ($q) use ($provinsiBalai) {
                $q->whereIn('nama', $provinsiBalai);
            })
            // Blok filter status dihapus dari sini
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

        // --- TAB BENCANA TERKINI (Khusus status: ditangani) ---
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

        // --- TAB LAPORAN MASYARAKAT (Semua status ditampilkan) ---
        $laporanMasyarakat = LaporanMasyarakat::query()
            ->whereHas('provinsi', function ($q) use ($provinsiBalai) {
                $q->whereIn('nama', $provinsiBalai);
            })
            // Blok filter status dihapus dari sini
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
        // 1. Ambil data semua provinsi
        $provinsis = Provinsi::orderBy('nama')->get();

        return view('dashboards.form-laporan-bencana', [
            'mode' => 'create',
            'laporan' => null,
            'provinsis' => $provinsis, // <-- Pastikan ini dipassing
        ]);
    }

    // BARU — tujuan tombol "Submit Data" di form mode create.
    public function laporanPenangananStore(Request $request)
    {
        // 1. Validasi Data Laporan Utama & Data Laporan Balai (Array)
        $validated = $request->validate([
            // --- Data Laporan Masyarakat ---
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
            'infrastruktur_terdampak' => 'nullable|string', // Kolom text lama (opsional)
            'kebutuhan_mendesak'      => 'nullable|string',
            
            // --- Data Laporan Balai (Atribut Utama) ---
            'status_terkini'          => 'nullable|string',
            'tanggal_respon'          => 'nullable|date',
            'catatan'                 => 'nullable|string',

            // --- Data Laporan Balai (Relasi/Array) ---
            'pic'                     => 'nullable|array',
            'infrastruktur'           => 'nullable|array', // Asumsi atribut form name="infrastruktur[][nama_kolom]"
            'penanganan_sementara'    => 'nullable|array', // Asumsi atribut form name="penanganan_sementara[][nama_kolom]"
            'penanganan_permanen'     => 'nullable|array', // Asumsi atribut form name="penanganan_permanen[][nama_kolom]"
        ]);

        // 2. Buat Laporan Masyarakat Utama
        $laporanData = collect($validated)->only([
            'jenis_bencana', 'nama_bencana', 'waktu_kejadian', 'wilayah_waktu', 
            'lokasi', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id', 
            'lintang', 'bujur', 'deskripsi', 'dampak_bencana', 'infrastruktur_terdampak', 'kebutuhan_mendesak'
        ])->toArray();
        
        $laporanData['status'] = 'ditangani';
        $laporan = LaporanMasyarakat::create($laporanData);

        // 3. Buat Laporan Balai
        $laporanBalai = LaporanBalai::create([
            'laporan_masyarakat_id' => $laporan->id, // Menggunakan nama field yang sesuai dengan Model Anda
            'balai_id'              => Auth::user()->balai->id,
            'created_by'            => Auth::id(),
            'status_terkini'        => $request->status_terkini ?? null,
            'tanggal_respon'        => $request->tanggal_respon ?? now(),
            'catatan'               => $request->catatan ?? null,
        ]);

        // 4. Simpan Relasi: PIC Bencana
        if ($request->has('pic') && is_array($request->pic)) {
            foreach ($request->pic as $picData) {
                if (!empty($picData['pic_lainnya'])) {
                    $laporanBalai->picBencanas()->create(['pic_lainnya' => $picData['pic_lainnya']]);
                } elseif (!empty($picData['balai_id'])) {
                    $laporanBalai->picBencanas()->create([
                        'balai_id' => $picData['balai_id'],
                        'nama_pic' => $picData['nama_pic'] ?? null,
                        'kontak'   => $picData['kontak'] ?? null,
                    ]);
                }
            }
        }

        // 5. Simpan Relasi: Infrastruktur Terdampak
        if ($request->has('infrastruktur') && isset($request->infrastruktur['unor'])) {
            $jumlahBaris = count($request->infrastruktur['unor']);
            
            for ($i = 0; $i < $jumlahBaris; $i++) {
                // Pastikan baris ini tidak kosong sebelum disimpan
                if (!empty($request->infrastruktur['unor'][$i]) || !empty($request->infrastruktur['kategori'][$i])) {
                    $laporanBalai->infrastrukturTerdampak()->create([
                        'unor'     => $request->infrastruktur['unor'][$i] ?? null,
                        'kategori' => $request->infrastruktur['kategori'][$i] ?? null,
                        'nama'     => $request->infrastruktur['nama'][$i] ?? null,
                        'satuan'   => $request->infrastruktur['satuan'][$i] ?? null,
                        'jumlah'   => $request->infrastruktur['jumlah'][$i] ?? null,
                        'detail'   => $request->infrastruktur['detail'][$i] ?? null,
                    ]);
                }
            }
        }

        // 6. Simpan Relasi: Penanganan Sementara
        if ($request->has('penanganan_sementara') && isset($request->penanganan_sementara['kewenangan'])) {
            $jumlahBaris = count($request->penanganan_sementara['kewenangan']);
            
            for ($i = 0; $i < $jumlahBaris; $i++) {
                if (!empty($request->penanganan_sementara['kewenangan'][$i])) {
                    $laporanBalai->penangananSementara()->create([
                        'tanggal'    => $request->penanganan_sementara['tanggal'][$i] ?? null,
                        'kewenangan' => $request->penanganan_sementara['kewenangan'][$i] ?? null,
                        'target'     => $request->penanganan_sementara['target'][$i] ?? null,
                        'progres'    => $request->penanganan_sementara['progres'][$i] ?? null,
                        // Tambahkan kolom lain jika ada
                    ]);
                }
            }
        }

        // 7. Simpan Relasi: Penanganan Permanen
        if ($request->has('penanganan_permanen') && isset($request->penanganan_permanen['kewenangan'])) {
            $jumlahBaris = count($request->penanganan_permanen['kewenangan']);
            
            for ($i = 0; $i < $jumlahBaris; $i++) {
                if (!empty($request->penanganan_permanen['kewenangan'][$i])) {
                    $laporanBalai->penangananPermanen()->create([
                        'tanggal'    => $request->penanganan_permanen['tanggal'][$i] ?? null,
                        'kewenangan' => $request->penanganan_permanen['kewenangan'][$i] ?? null,
                        'target'     => $request->penanganan_permanen['target'][$i] ?? null,
                        'progres'    => $request->penanganan_permanen['progres'][$i] ?? null,
                        // Tambahkan kolom lain jika ada
                    ]);
                }
            }
        }

        // 8. Rekam Log (History)
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

    // BARU — tujuan tombol "Edit" di tabel.
    public function laporanPenangananEdit($id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);
        
        // 1. Ambil data semua provinsi
        $provinsis = Provinsi::orderBy('nama')->get();

        return view('dashboards.form-laporan-bencana', [
            'mode' => 'edit',
            'laporan' => $laporan,
            'provinsis' => $provinsis, // <-- Pastikan ini dipassing
        ]);
    }

    // BARU — submit dari form mode edit.
    public function laporanPenangananUpdate(Request $request, $id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);

        // 1. Validasi Data
        $validated = $request->validate([
            // (Masukkan rules validasi yang sama persis seperti di method store di atas)
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
            
            'status_terkini'          => 'nullable|string',
            'tanggal_respon'          => 'nullable|date',
            'catatan'                 => 'nullable|string',
            'pic'                     => 'nullable|array',
            'infrastruktur'           => 'nullable|array',
            'penanganan_sementara'    => 'nullable|array',
            'penanganan_permanen'     => 'nullable|array',
        ]);

        // 2. Update Laporan Utama
        $laporanData = collect($validated)->only([
            'jenis_bencana', 'nama_bencana', 'waktu_kejadian', 'wilayah_waktu', 
            'lokasi', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id', 
            'lintang', 'bujur', 'deskripsi', 'dampak_bencana', 'infrastruktur_terdampak', 'kebutuhan_mendesak'
        ])->toArray();
        $laporan->update($laporanData);

        // 3. Update atau Create Laporan Balai
        $laporanBalai = LaporanBalai::firstOrCreate(
            [
                'laporan_masyarakat_id' => $laporan->id,
                'balai_id'              => Auth::user()->balai->id,
            ],
            [
                'created_by' => Auth::id(),
            ]
        );

        // Update atribut statis Laporan Balai
        $laporanBalai->update([
            'status_terkini' => $request->status_terkini ?? $laporanBalai->status_terkini,
            'tanggal_respon' => $request->tanggal_respon ?? $laporanBalai->tanggal_respon,
            'catatan'        => $request->catatan ?? $laporanBalai->catatan,
        ]);

        // 4. Hapus Data Lama untuk mencegah penumpukan baris ganda
        $laporanBalai->picBencanas()->delete();
        $laporanBalai->infrastrukturTerdampak()->delete();
        $laporanBalai->penangananSementara()->delete();
        $laporanBalai->penangananPermanen()->delete();

        // 5. Insert Ulang Relasi (Proses ini sama persis dengan Create)
        
        // PIC
        if ($request->has('pic') && is_array($request->pic)) {
            foreach ($request->pic as $picData) {
                if (!empty($picData['pic_lainnya'])) {
                    $laporanBalai->picBencanas()->create(['pic_lainnya' => $picData['pic_lainnya']]);
                } elseif (!empty($picData['balai_id'])) {
                    $laporanBalai->picBencanas()->create([
                        'balai_id' => $picData['balai_id'],
                        'nama_pic' => $picData['nama_pic'] ?? null,
                        'kontak'   => $picData['kontak'] ?? null,
                    ]);
                }
            }
        }

        // Infrastruktur Terdampak
        if ($request->has('infrastruktur') && is_array($request->infrastruktur)) {
            foreach ($request->infrastruktur as $infraData) {
                $laporanBalai->infrastrukturTerdampak()->create($infraData);
            }
        }

        // Penanganan Sementara
        if ($request->has('penanganan_sementara') && is_array($request->penanganan_sementara)) {
            foreach ($request->penanganan_sementara as $sementaraData) {
                $laporanBalai->penangananSementara()->create($sementaraData);
            }
        }

        // Penanganan Permanen
        if ($request->has('penanganan_permanen') && is_array($request->penanganan_permanen)) {
            foreach ($request->penanganan_permanen as $permanenData) {
                $laporanBalai->penangananPermanen()->create($permanenData);
            }
        }

        // 6. Rekam Log (History)
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

    public function laporanPenangananShow($id)
    {
        $laporan = LaporanMasyarakat::with('fotos')->findOrFail($id);

        // Datang dari tab "Bencana Terkini" (status sudah ditangani) -> form lengkap read-only.
        if ($laporan->status === 'ditangani') {
            return view('dashboards.form-laporan-bencana', [
                'mode' => 'detail',
                'laporan' => $laporan,
            ]);
        }

        // Datang dari tab "Laporan Masyarakat" -> halaman detail simpel lama, TIDAK berubah.
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

        // ASUMSI: begitu status diubah jadi "ditangani" dari menu Laporan Masyarakat,
        // balai langsung dilempar ke form biar bisa lanjut isi Penanganan Sementara,
        // Sumberdaya, dst. Kalau maunya tetap balik ke daftar seperti semula,
        // hapus blok if ini dan biarkan cuma redirect()->back() di bawah.
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