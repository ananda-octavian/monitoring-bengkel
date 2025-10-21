<?php

namespace App\Http\Controllers;

use App\Models\Detailup;
use App\Models\Spp;
use App\Models\Workshop;
use Illuminate\Http\Request;

class DetailupController extends Controller
{
    protected function getIdBengkel(Request $request)
    {
        return Workshop::where([
            'bisnis' => $request->query('bisnis'),
            'manufaktur' => $request->query('manufaktur'),
            'dealer' => $request->query('dealer'),
            'cabang' => $request->query('cabang'),
            'lokasi' => $request->query('lokasi')
        ])->firstOrFail()->id_bengkel;
    }

    public function detailupview(Request $request, $nospp)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $detailupData = Detailup::where('nospp', $nospp)
                                ->where('id_bengkel', $id_bengkel)
                                ->paginate(25);
        $sppData = Spp::where('nospp', $nospp)
                      ->where('id_bengkel', $id_bengkel)
                      ->firstOrFail();
        $grandTotal = $detailupData->sum('hargauraian');

        return view('detailup.index', compact('sppData', 'detailupData', 'grandTotal'));
    }

    public function tambahdetailupview(Request $request, $nospp)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $sppData = Spp::where('nospp', $nospp)
                      ->where('id_bengkel', $id_bengkel)
                      ->firstOrFail();
        $detailupData = Detailup::where('nospp', $nospp)
                                ->where('id_bengkel', $id_bengkel)
                                ->get();
        $grandTotal = $detailupData->sum('hargauraian');

        return view('detailup.tambah', compact('sppData', 'detailupData', 'grandTotal'));
    }

    public function tambahdetailup(Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);

        // Validasi input
        $request->validate([
            'nospp' => 'required|exists:spps,nospp',
            'namauraian.*' => 'required|string',
            'hargauraian.*' => 'required|numeric',
        ]);

        $nospp = $request->input('nospp');
        $namauraian = $request->input('namauraian');
        $hargauraian = $request->input('hargauraian');

        if (count($namauraian) !== count($hargauraian)) {
            return redirect()->back()->with('error', 'Jumlah nama uraian dan harga uraian tidak cocok.');
        }

        foreach ($namauraian as $index => $uraian) {
            // Generate id_uraian with prefix id_bengkel and unique identifier
            $lastDetailup = Detailup::where('id_bengkel', $id_bengkel)
                                    ->orderBy('id_uraian', 'desc')
                                    ->first();
            $lastIdUraian = $lastDetailup ? intval(substr($lastDetailup->id_uraian, strlen($id_bengkel))) : 0;
            $id_uraian = $id_bengkel . str_pad($lastIdUraian + 1, 6, '0', STR_PAD_LEFT);

            Detailup::create([
                'id_uraian' => $id_uraian,
                'nospp' => $nospp,
                'namauraian' => $uraian,
                'hargauraian' => $hargauraian[$index],
                'id_bengkel' => $id_bengkel,
            ]);
        }

        $this->updateGrandTotal($nospp, $id_bengkel);

        return redirect()->route('tambahdetailupview', [
            'nospp' => $nospp,
            'bisnis' => $request->query('bisnis'),
            'manufaktur' => $request->query('manufaktur'),
            'dealer' => $request->query('dealer'),
            'cabang' => $request->query('cabang'),
            'lokasi' => $request->query('lokasi')
        ])->with('success', 'Uraian berhasil ditambahkan.');
    }

    public function hapusdetailup($id_uraian, Request $request)
    {
        $detailup = Detailup::where('id_uraian', $id_uraian)->firstOrFail();
    
        $nospp = $detailup->nospp;  // Pastikan `nospp` diambil dari `detailup`
        $id_bengkel = $detailup->id_bengkel;
    
        $detailup->delete();
    
        // Update grandtotal after deletion
        $this->updateGrandTotal($nospp, $id_bengkel);
    
        // Extract additional parameters from the request if needed
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
    
        return redirect()->route('manajemendetailup', [
            'nospp' => $nospp,  // Tambahkan `nospp` sebagai parameter
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi
        ])->with('success', 'Data Uraian berhasil dihapus');
    }
    

    protected function updateGrandTotal($nospp, $id_bengkel)
    {
        $grandTotal = Detailup::where('nospp', $nospp)
                              ->where('id_bengkel', $id_bengkel)
                              ->sum('hargauraian');
        Spp::where('nospp', $nospp)
           ->where('id_bengkel', $id_bengkel)
           ->update(['grandtotal' => $grandTotal]);
    }
}
