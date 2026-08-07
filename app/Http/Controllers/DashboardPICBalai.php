<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Balai;
use App\Models\Provinsi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class DashboardPICBalai extends Controller
{
    public function databalai()
    {
        $balais = Balai::all();

        return view('dashboards.datapicbalai', [
            'title' => 'Data PIC Balai - SITABA',
            'componentName' => 'opps.data-pic',
            'balais' => $balais
        ]);
    }

    public function createBalai()
    {
        return view('layouts.laporanpicbalai', [
            'title' => 'Tambah Data Balai - SITABA',
            'componentName' => 'opps.data-pic-create',
            'provinsis' => Provinsi::orderBy('nama')->get()
        ]);
    }

    public function storeBalai(Request $request)
    {
        $validatedData = $request->validate([
            'nama_balai' => 'required|string|max:255',
            'unker'      => 'nullable|string|max:255',
            'unor'       => 'nullable|string|max:255',
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50',

            'provinsi'   => 'nullable|array|max:2',
            'provinsi.*' => 'string|max:255',

            // Balai no longer has its own login — each PIC is a full account now.
            'pics'              => 'nullable|array',
            'pics.*.nama'       => 'required_with:pics|string|max:255',
            'pics.*.kontak'     => 'required_with:pics|string|max:50',
            'pics.*.username'   => 'required_with:pics|string|max:255',
            'pics.*.password'   => 'required_with:pics|string|min:6',
        ]);

        // Reject duplicate usernames within the submission itself, and against
        // existing users, before touching the database.
        $usernames = collect($request->input('pics', []))->pluck('username')->filter();
        if ($usernames->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([
                'pics' => 'Username PIC tidak boleh sama satu sama lain.',
            ]);
        }
        foreach ($usernames as $index => $username) {
            if (User::where('username', $username)->exists()) {
                throw ValidationException::withMessages([
                    "pics.{$index}.username" => "Username \"{$username}\" sudah digunakan.",
                ]);
            }
        }

        if (isset($validatedData['provinsi'])) {
            $validatedData['provinsi'] = implode(', ', $validatedData['provinsi']);
        }

        $balaiData = Arr::except($validatedData, ['pics']);

        $balai = Balai::create($balaiData);

        if ($request->filled('provinsi') && is_array($request->provinsi)) {
            $provinsiIds = Provinsi::whereIn('nama', $request->provinsi)->pluck('id')->toArray();
            $balai->provinsis()->sync($provinsiIds);
        }

        if ($request->has('pics') && is_array($request->pics)) {
            foreach ($request->pics as $picData) {
                User::create([
                    'name'     => $picData['nama'],
                    'username' => $picData['username'],
                    'password' => Hash::make($picData['password']),
                    'kontak'   => $picData['kontak'],
                    'role'     => 'pic',
                    'balai_id' => $balai->id,
                ]);
            }
        }

        return redirect()->route('data.pic-balai')
                         ->with('success', 'Data Balai Bencana berhasil ditambahkan!');
    }

    public function balaiShow(Balai $balai)
    {
        $balai->load('pics');

        return view('layouts.laporanpicbalai', [
            'title' => 'Detail PIC Balai - SITABA',
            'componentName' => 'opps.data-pic-show',
            'balai' => $balai
        ]);
    }

    public function editBalai(Balai $balai)
    {
        $balai->load('pics');

        return view('layouts.laporanpicbalai', [
            'title' => 'Edit Data Balai - SITABA',
            'componentName' => 'opps.data-pic-edit',
            'balai' => $balai,
            'provinsis' => Provinsi::orderBy('nama')->get()
        ]);
    }

    public function updateBalai(Request $request, Balai $balai)
    {
        $validatedData = $request->validate([
            'nama_balai' => 'required|string|max:255',
            'unker'      => 'nullable|string|max:255',
            'unor'       => 'nullable|string|max:255',
            'pulau'      => 'nullable|string|max:255',
            'kepala'     => 'nullable|string|max:255',
            'kontak'     => 'nullable|string|max:50',

            'provinsi'   => 'nullable|array|max:2',
            'provinsi.*' => 'string|max:255',

            'pics'              => 'required|array|min:1',
            'pics.*.id'         => 'nullable|integer',
            'pics.*.nama'       => 'required|string|max:255',
            'pics.*.kontak'     => 'required|string|max:50',
            'pics.*.username'   => 'required|string|max:255',
            'pics.*.password'   => 'nullable|string|min:6',
        ]);

        // Manual uniqueness + "password required for new PICs" checks —
        // array-wildcard unique/required_if can't express "except this row's own id".
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

        if (isset($validatedData['provinsi'])) {
            $validatedData['provinsi'] = implode(', ', $validatedData['provinsi']);
        } else {
            $validatedData['provinsi'] = null;
        }

        $balaiData = Arr::except($validatedData, ['pics']);

        $balai->update($balaiData);

        if ($request->filled('provinsi') && is_array($request->provinsi)) {
            $provinsiIds = Provinsi::whereIn('nama', $request->provinsi)->pluck('id')->toArray();
            $balai->provinsis()->sync($provinsiIds);
        } else {
            $balai->provinsis()->detach();
        }

        $submittedPicIds = collect($request->pics)->pluck('id')->filter()->toArray();

        // Remove PICs that were dropped from the form.
        User::where('balai_id', $balai->id)
            ->where('role', 'pic')
            ->whereNotIn('id', $submittedPicIds ?: [0])
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

        return redirect()->route('data.pic-balai-show', $balai->id)
                         ->with('success', 'Data Balai Bencana berhasil diperbarui!');
    }

    public function destroyBalai(Balai $balai)
    {
        User::where('balai_id', $balai->id)->where('role', 'pic')->delete();
        $balai->delete();

        return redirect()->route('data.pic-balai')->with('status', 'Data Balai berhasil dihapus.');
    }
}