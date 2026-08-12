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
        $validated = $request->validate([
            'jenis_bencana' => 'nullable|string',
            'nama_bencana' => 'nullable|string',
            'waktu_kejadian' => 'nullable|string',
            'wilayah_waktu' => 'nullable|in:WIB,WITA,WIT',
            'lokasi' => 'nullable|string',
            'lintang' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'deskripsi' => 'nullable|string',
            'dampak_bencana' => 'nullable|string',
            'infrastruktur_terdampak' => 'nullable|string',
            'kebutuhan_mendesak' => 'nullable|string',
            'pic' => 'nullable|array', // Validasi array PIC
        ]);

        // Simpan laporan tanpa menyertakan array 'pic' ke kolom tabel utama
        $laporanData = collect($validated)->except('pic')->toArray();
        $laporanData['status'] = 'ditangani';
        
        $laporan = LaporanMasyarakat::create($laporanData);

        // TODO: pastikan dulu nama tabel pivot yang benar
        Auth::user()->balai->laporanMasyarakats()->attach($laporan->id);

        // --- SIMPAN DATA PIC ---
        if ($request->has('pic') && is_array($request->pic)) {
            foreach ($request->pic as $picData) {
                if (!empty($picData['pic_lainnya'])) {
                    $laporan->picBencanas()->create([
                        'pic_lainnya' => $picData['pic_lainnya']
                    ]);
                } elseif (!empty($picData['balai_id'])) {
                    $laporan->picBencanas()->create([
                        'balai_id' => $picData['balai_id'],
                        'nama_pic' => $picData['nama_pic'] ?? null,
                        'kontak'   => $picData['kontak'] ?? null,
                    ]);
                }
            }
        }

        $balai = Auth::user()->balai;
        $laporan->logs()->create([
            'action'       => 'created',
            'user_id'      => Auth::id(),
            'nama_balai'   => $balai->nama_balai ?? 'Unknown Balai',
            'kepala_balai' => $balai->kepala ?? 'Unknown Kepala',
        ]);

        return redirect()
            ->route('balai.laporan-penanganan-balai')
            ->with('success', 'Laporan berhasil disimpan.');
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

        $validated = $request->validate([
            'jenis_bencana' => 'nullable|string',
            'nama_bencana' => 'nullable|string',
            'waktu_kejadian' => 'nullable|string',
            'wilayah_waktu' => 'nullable|in:WIB,WITA,WIT',
            'lokasi' => 'nullable|string',
            'lintang' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'deskripsi' => 'nullable|string',
            'dampak_bencana' => 'nullable|string',
            'infrastruktur_terdampak' => 'nullable|string',
            'kebutuhan_mendesak' => 'nullable|string',
            'pic' => 'nullable|array', // Validasi array PIC
        ]);

        // Update laporan utama
        $laporan->update(collect($validated)->except('pic')->toArray());

        // --- UPDATE DATA PIC ---
        // Hapus data PIC yang lama terlebih dahulu
        $laporan->picBencanas()->delete();

        // Insert ulang data PIC dari request
        if ($request->has('pic') && is_array($request->pic)) {
            foreach ($request->pic as $picData) {
                if (!empty($picData['pic_lainnya'])) {
                    $laporan->picBencanas()->create([
                        'pic_lainnya' => $picData['pic_lainnya']
                    ]);
                } elseif (!empty($picData['balai_id'])) {
                    $laporan->picBencanas()->create([
                        'balai_id' => $picData['balai_id'],
                        'nama_pic' => $picData['nama_pic'] ?? null,
                        'kontak'   => $picData['kontak'] ?? null,
                    ]);
                }
            }
        }

        $balai = Auth::user()->balai;
        $laporan->logs()->create([
            'action'       => 'updated',
            'user_id'      => Auth::id(),
            'nama_balai'   => $balai->nama_balai ?? 'Unknown Balai',
            'kepala_balai' => $balai->kepala ?? 'Unknown Kepala',
        ]);

        return redirect()
            ->route('balai.laporan-penanganan-balai')
            ->with('success', 'Laporan berhasil diperbarui.');
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