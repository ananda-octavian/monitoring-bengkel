<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationController extends Controller
{
    public function bisnis()
    {
        // Ambil data bisnis yang unik dari tabel workshops
        $workshops = Workshop::select('bisnis')->groupBy('bisnis')->get();
        return view('navigation.bisnis', compact('workshops'));
    }

    public function manufaktur(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $workshops = Workshop::select('manufaktur')
            ->where('bisnis', $bisnis)
            ->groupBy('manufaktur')
            ->get();
        return view('navigation.manufaktur', compact('workshops', 'bisnis'));
    }

    public function dealer(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $workshops = Workshop::select('dealer')
            ->where('bisnis', $bisnis)
            ->where('manufaktur', $manufaktur)
            ->groupBy('dealer')
            ->get();
        return view('navigation.dealer', compact('workshops', 'manufaktur', 'bisnis'));
    }

    public function cabang(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $workshops = Workshop::select('cabang')
            ->where('bisnis', $bisnis)
            ->where('manufaktur', $manufaktur)
            ->where('dealer', $dealer)
            ->groupBy('cabang')
            ->get();
        return view('navigation.cabang', compact('workshops', 'manufaktur', 'dealer', 'bisnis'));
    }

    public function lokasi(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $workshops = Workshop::select('lokasi')
            ->where('bisnis', $bisnis)
            ->where('manufaktur', $manufaktur)
            ->where('dealer', $dealer)
            ->where('cabang', $cabang)
            ->groupBy('lokasi')
            ->get();
        return view('navigation.lokasi', compact('workshops', 'manufaktur', 'dealer', 'cabang', 'bisnis'));
    }

    public function home(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
        $workshop = Workshop::where('bisnis', $bisnis)
                            ->where('manufaktur', $manufaktur)
                            ->where('cabang', $cabang)
                            ->where('lokasi', $lokasi)
                            ->first();

        // Simpan id_bengkel ke user jika ditemukan
        if ($workshop) {
            $user = Auth::user();
            $user->id_bengkel = $workshop->id_bengkel;
            $user->save();
        }

        return view('home');
    }
}
