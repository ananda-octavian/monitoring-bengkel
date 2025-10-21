<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function userview(Request $request)
{
    $limit = 25;
    $keyword = $request->input('keyword');

    $query = User::query();

    // Filter pencarian
    if (!empty($keyword)) {
        $query->where(function ($q) use ($keyword) {
            $q->where('username', 'like', "%$keyword%")
              ->orWhere('level', 'like', "%$keyword%");
        });
    }

    // Filter untuk menyembunyikan user dengan level superadmin
    $query->where('level', '!=', 'superadmin');

    $userData = $query->paginate($limit);

    return view('user.index', compact('userData'));
}


    public function usersearch(Request $request)
    {
        return redirect()->route('manajemenuser', [
            'keyword' => $request->input('keyword'),
        ]);
    }

    public function adduserview(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
        
        $workshops = Workshop::all();

        return view('user.add', compact('bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi', 'workshops'));
    }

    public function adduser(Request $request)
{

    $jumlahuser = User::count();
    $id_user = $jumlahuser + 1;

    $request->validate([
        'username' => 'required|unique:users,username',
        'password' => 'required',
        'level' => 'required',
        'id_bengkel' => 'nullable', // Mengharuskan input id_bengkel dari form
    ]);

    User::create([
        'id_user' => $id_user,
        'username' => $request->username,
        'password' => bcrypt($request->password),
        'level' => $request->level,
        'id_bengkel' => $request->id_bengkel, // Mengambil dari input form
    ]);

    return redirect()->route('manajemenuser')->with('success', 'Data Workshop berhasil ditambah.');
}

public function edituserview($id_user, Request $request)
{
    $user = User::find($id_user);

    if (!$user) {
        return redirect()->route('manajemenuser')->with('error', 'Data tidak Ditemukan.');
    }

    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');

    $workshops = Workshop::all(); // Ambil semua data workshop

    return view('user.edit', compact('user', 'bisnis',  'manufaktur', 'dealer', 'cabang', 'lokasi', 'workshops'));
}


    public function updateuser(Request $request, $id_user)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'level' => 'required',
            'id_bengkel' => 'nullable',
        ]);

        $user = User::find($id_user);

        if (!$user) {
            return redirect()->route('manajemenuser')->with('error', 'Data tidak Ditemukan.');
        }

        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));
        $user->level = $request->input('level');
        $user->id_bengkel = $request->input('id_bengkel');

        $user->save();

        return redirect()->route('manajemenuser', [
            'bisnis' => $request->query('bisnis'),
            'manufaktur' => $request->query('manufaktur'),
            'dealer' => $request->query('dealer'),
            'cabang' => $request->query('cabang'),
            'lokasi' => $request->query('lokasi')
        ])->with('success', 'Data user berhasil diupdate.');
    }

    public function hapususer($id_user, Request $request)
    {
        $user = User::find($id_user);

        if (!$user) {
            return redirect()->route('manajemenuser', [
            'bisnis' => $request->query('bisnis'),
            'manufaktur' => $request->query('manufaktur'),
            'dealer' => $request->query('dealer'),
            'cabang' => $request->query('cabang'),
            'lokasi' => $request->query('lokasi'),
            ])->with('error', 'Data Tidak Ditemukan.');
        }

        $user->delete();

        return redirect()->route('manajemenuser', [
            'bisnis' => $request->query('bisnis'),
            'manufaktur' => $request->query('manufaktur'),
            'dealer' => $request->query('dealer'),
            'cabang' => $request->query('cabang'),
            'lokasi' => $request->query('lokasi')
        ])->with('success', 'Data telah dihapus.');
    }
}
