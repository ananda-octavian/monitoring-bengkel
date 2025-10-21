<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{

    public function workshopview(Request $request)
    {
        $limit = 25; // Jumlah data per halaman
        $keyword = $request->input('keyword');

        // Query builder untuk data workshop
        $query = Workshop::query();

        // Jika terdapat keyword, lakukan pencarian
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('id_bengkel', 'like', "%$keyword%")
                    ->orWhere('bisnis', 'like', "%$keyword%")
                    ->orWhere('manufaktur', 'like', "%$keyword%")
                    ->orWhere('dealer', 'like', "%$keyword%")
                    ->orWhere('cabang', 'like', "%$keyword%")
                    ->orWhere('lokasi', 'like', "%$keyword%");
            });
        }

        // Ambil data workshop dengan pagination
        $workshopData = $query->paginate($limit);

        return view('workshop.index', compact('workshopData'));
    }

    public function workshopsearch(Request $request)
    {
        return redirect()->route('manajemenworkshop', [
            'keyword' => $request->input('keyword'),
        ]);
    }

    public function addworkshopview()
    {
        $bisniss = Workshop::distinct()->pluck('bisnis');
        $manufakturs = Workshop::distinct()->pluck('manufaktur');
        $dealers = Workshop::distinct()->pluck('dealer');
        $cabangs = Workshop::distinct()->pluck('cabang');
        $lokasis = Workshop::distinct()->pluck('lokasi');

        return view('workshop.add', compact('bisniss', 'manufakturs', 'dealers', 'cabangs', 'lokasis'));
    }

    public function addworkshop(Request $request)
    {
        $request->validate([
            'id_bengkel' => 'required',
            'bisnis' => 'required_without:bisnis_text',
            'bisnis_text' => 'required_without:bisnis',
            'manufaktur' => 'required_without:manufaktur_text',
            'manufaktur_text' => 'required_without:manufaktur',
            'dealer' => 'required_without:dealer_text',
            'dealer_text' => 'required_without:dealer',
            'cabang' => 'required_without:cabang_text',
            'cabang_text' => 'required_without:cabang',
            'lokasi' => 'required_without:lokasi_text',
            'lokasi_text' => 'required_without:lokasi',
        ]);

        $bisnis = $request->bisnis === 'lainnya' ? $request->bisnis_text : $request->bisnis;
        $manufaktur = $request->manufaktur === 'lainnya' ? $request->manufaktur_text : $request->manufaktur;
        $dealer = $request->dealer === 'lainnya' ? $request->dealer_text : $request->dealer;
        $cabang = $request->cabang === 'lainnya' ? $request->cabang_text : $request->cabang;
        $lokasi = $request->lokasi === 'lainnya' ? $request->lokasi_text : $request->lokasi;

        Workshop::create([
            'id_bengkel' => $request->id_bengkel,
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi,
        ]);

        return redirect()->route('manajemenworkshop')->with('success', 'Data Workshop berhasil ditambah.');
    }

    public function editworkshopview($id_bengkel, Request $request)
    {
        $workshop = Workshop::find($id_bengkel);

        $bisniss = Workshop::distinct()->pluck('bisnis');
        $manufakturs = Workshop::distinct()->pluck('manufaktur');
        $dealers = Workshop::distinct()->pluck('dealer');
        $cabangs = Workshop::distinct()->pluck('cabang');
        $lokasis = Workshop::distinct()->pluck('lokasi');

        return view('workshop.edit', compact('workshop', 'bisniss', 'dealers', 'manufakturs', 'cabangs', 'lokasis'));
    }

    public function updateworkshop(Request $request, $id_bengkel)
    {
        $request->validate([
            'id_bengkel' => 'required',
            'bisnis' => 'required_without:bisnis_text',
            'bisnis_text' => 'required_without:bisnis',
            'manufaktur' => 'required_without:manufaktur_text',
            'manufaktur_text' => 'required_without:manufaktur',
            'dealer' => 'required_without:dealer_text',
            'dealer_text' => 'required_without:dealer',
            'cabang' => 'required_without:cabang_text',
            'cabang_text' => 'required_without:cabang',
            'lokasi' => 'required_without:lokasi_text',
            'lokasi_text' => 'required_without:lokasi',
        ]);

        $workshop = Workshop::findOrFail($id_bengkel);

        $bisnis = $request->bisnis === 'lainnya' ? $request->bisnis_text : $request->bisnis;
        $manufaktur = $request->manufaktur === 'lainnya' ? $request->manufaktur_text : $request->manufaktur;
        $dealer = $request->dealer === 'lainnya' ? $request->dealer_text : $request->dealer;
        $cabang = $request->cabang === 'lainnya' ? $request->cabang_text : $request->cabang;
        $lokasi = $request->lokasi === 'lainnya' ? $request->lokasi_text : $request->lokasi;

        $workshop->update([
            'id_bengkel' => $request->id_bengkel,
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi,
        ]);

        return redirect()->route('manajemenworkshop')->with('success', 'Data Workshop berhasil diperbarui.');
    }

    public function deleteworkshop($id_bengkel)
    {
        $workshop = Workshop::findOrFail($id_bengkel);
        $workshop->delete();

        return redirect()->route('manajemenworkshop')->with('success', 'Data Workshop berhasil dihapus.');
    }
}
