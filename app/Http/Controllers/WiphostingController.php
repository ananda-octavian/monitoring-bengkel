<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wip;
use App\Models\Spp;
use App\Models\Workshop;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WipController extends Controller
{
    // Metode untuk menangkap parameter URL dan mencari id_bengkel yang sesuai
    protected function getIdBengkel(Request $request)
    {
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
    
        $workshop = Workshop::where('bisnis', $bisnis)
        ->where('manufaktur', $manufaktur)
        ->where('dealer', $dealer)
        ->where('cabang', $cabang)
        ->where('lokasi', $lokasi)
        ->first();
    
        if (!$workshop) {
            abort(404, 'Workshop tidak ditemukan');
        }
    
        return $workshop->id_bengkel; // Pastikan ini adalah string atau integer, bukan array
    }
    
    public function wipview(Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
    
        $userLevel = auth()->user()->level;
        $filter = $request->query('filter', '');
        $keyword = $request->input('keyword', '');
        $limit = 9999;
    
        // Redirect based on filter
        if ($filter === 'All') {
            if (in_array($userLevel, ['superadmin', 'manajemen', 'adminpadma', 'kepalamekanik'])) {
                return redirect()->route('viewallwip', [
                    'id_bengkel' => $id_bengkel,
                    'bisnis' => $bisnis,
                    'manufaktur' => $manufaktur,
                    'dealer' => $dealer,
                    'cabang' => $cabang,
                    'lokasi' => $lokasi
                ]);
            }
        }
    
        // Process statuses
        $inProgressProcesses = [
            'Job Dispatch', 'Panel', 'Putty', 'Surfacer', 'Masking',
            'Painting', 'Polishing', 'Assembly', 'Washing',
            'Finishing', 'Final Check', 'Work Paused'
        ];
        $allProcesses = array_merge(['All'], $inProgressProcesses);
    
        // Get latest WIPs
        $latestWips = Wip::select('wips.*')
            ->join(DB::raw('(SELECT MAX(id_wips) as latest_id FROM wips GROUP BY nospp) as latest'), 'wips.id_wips', '=', 'latest.latest_id')
            ->where('wips.id_bengkel', $id_bengkel)
            ->get();
    
        $statusCounts = array_fill_keys($allProcesses, 0);
    
        foreach ($latestWips as $wip) {
            $status = $wip->proses;
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
            if ($status !== 'Unit OK') {
                $statusCounts['All']++;
            }
        }
    
        // Query SPP data
        $sppDataQuery = Spp::where('id_bengkel', $id_bengkel)
            ->orderBy('estimasi', 'asc'); // Sort by estimasi
    
        if ($keyword) {
            $sppDataQuery->where(function ($query) use ($keyword) {
                $query->where('nopol', 'like', "%{$keyword}%")
                      ->orWhere('nospp', 'like', "%{$keyword}%")
                      ->orWhere('sa', 'like', "%{$keyword}%")
                      ->orWhere('type', 'like', "%{$keyword}%")
                      ->orWhere('warna', 'like', "%{$keyword}%")
                      ->orWhere('damage', 'like', "%{$keyword}%")
                      ->orWhere('asuransi', 'like', "%{$keyword}%");
            });
        }
    
        // Filter by process status
        if ($filter) {
            $sppDataQuery->whereHas('wip', function ($query) use ($filter) {
                $query->where('proses', $filter)
                      ->whereIn('id_wips', function ($subQuery) {
                          $subQuery->select(DB::raw('MAX(id_wips)'))
                                   ->from('wips')
                                   ->groupBy('nospp');
                      });
            });
        }
    
        // Get the SPP data
        $sppData = $sppDataQuery->paginate($limit);
    
        // Extract the 'Unit OK' items
        $unitOkData = Spp::where('id_bengkel', $id_bengkel)
            ->whereHas('wip', function ($query) {
                $query->where('proses', 'Unit OK');
            })
            ->orderBy('estimasi', 'asc')
            ->get();
    
        // Remove 'Unit OK' items from $sppData
        $sppData = $sppData->filter(function ($item) {
            return !$item->wip->pluck('proses')->contains('Unit OK');
        });
    
        // Calculate total WIP count
        $totalWipCount = $statusCounts['All'];
    
        // Calculate total RP for WIP
        $totalRpWip = number_format($sppData->sum('grandtotal'), 0, ',', '.');

        // Calculate the difference between stopped_at and created_at
        $stoppedAt = Carbon::parse($wip->stopped_at);
        $createdAt = Carbon::parse($wip->created_at);
        $difference = $stoppedAt->diff($createdAt);

    
        return view('wip.index', compact('sppData', 'statusCounts', 'filter', 'id_bengkel', 'bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi', 'totalWipCount', 'totalRpWip', 'difference'));
    }
    
    
    public function viewAllWip(Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $limit = 500;
        $keyword = $request->input('keyword');

        $processes = [
            'Job Dispatch','Panel','Putty','Surfacer','Masking',
            'Painting','Polishing','Assembly','Washing',
            'Finishing','Final Check','Work Paused'
        ];

        $sppDataQuery = Spp::where('id_bengkel', $id_bengkel);

        if ($keyword) {
            $sppDataQuery->where(function ($query) use ($keyword) {
                $query->where('nopol', 'like', "%{$keyword}%")
                      ->orWhere('nospp', 'like', "%{$keyword}%")
                      ->orWhere('sa', 'like', "%{$keyword}%")
                      ->orWhere('type', 'like', "%{$keyword}%")
                      ->orWhere('warna', 'like', "%{$keyword}%")
                      ->orWhere('damage', 'like', "%{$keyword}%")
                      ->orWhere('asuransi', 'like', "%{$keyword}%");
            });
        }

        $sppData = $sppDataQuery->with(['wip' => function($query) {
            $query->latest('id_wips');
        }])->orderBy('nospp', 'desc')->paginate($limit);

        $processMap = [];
        foreach ($processes as $process) {
            $processMap[$process] = [];
        }

        foreach ($sppData as $spp) {
            if ($spp->wip->isNotEmpty()) {
                $latestWip = $spp->wip->first();
                if (in_array($latestWip->proses, $processes)) {
                    $processMap[$latestWip->proses][] = $spp->nopol;
                }
            }
        }

        return view('wip.viewallwip', [
            'sppData' => $sppData,
            'processMap' => $processMap,
            'processes' => $processes,
            'id_bengkel' => $id_bengkel
        ]);
    }

    public function wipsearch(Request $request)
    {
        $keyword = $request->input('keyword');
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
    
        return redirect()->route('manajemenwip', [
            'keyword' => $keyword,
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi
        ]);
    }

    public function resumeview(Request $request)
{
    $id_bengkel = $this->getIdBengkel($request);
    
    $selectedMonth = $request->query('month', date('m'));
    $selectedYear = $request->query('year', date('Y'));
    
    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');
    
    $query = Spp::where('id_bengkel', $id_bengkel)
                ->whereMonth('tglmasuk', $selectedMonth)
                ->whereYear('tglmasuk', $selectedYear);
    
    $sppData = $query->paginate(9999);
    
    $unitOkData = $sppData->filter(function ($item) {
        return $item->wip->pluck('proses')->contains('Unit OK');
    });
    
    $totalNosppOk = $unitOkData->count();
    $totalRpOk = number_format($unitOkData->sum('grandtotal'), 0, ',', '.');
    
    $wipData = $sppData->filter(function ($item) {
        return !$item->wip->pluck('proses')->contains('Unit OK');
    });
    
    $previousWipData = Spp::where('id_bengkel', $id_bengkel)
                          ->where(function ($query) use ($selectedYear, $selectedMonth) {
                              $query->whereYear('tglmasuk', '<', $selectedYear)
                                    ->orWhere(function ($query) use ($selectedYear, $selectedMonth) {
                                        $query->whereYear('tglmasuk', $selectedYear)
                                              ->whereMonth('tglmasuk', '<', $selectedMonth);
                                    });
                          })
                          ->whereHas('wip', function ($query) {
                              $query->where('proses', '!=', 'Unit OK');
                          })
                          ->get();
    
    $combinedWipData = $wipData->concat($previousWipData)->filter(function ($item) {
        return !$item->wip->pluck('proses')->contains('Unit OK');
    });
    
    $totalNosppWip = $combinedWipData->count();
    $totalRpWip = number_format($combinedWipData->sum('grandtotal'), 0, ',', '.');
    
    $previousNosppData = Spp::where('id_bengkel', $id_bengkel)
                            ->where(function ($query) use ($selectedYear, $selectedMonth) {
                                $query->whereYear('tglmasuk', '<', $selectedYear)
                                      ->orWhere(function ($query) use ($selectedYear, $selectedMonth) {
                                          $query->whereYear('tglmasuk', $selectedYear)
                                                ->whereMonth('tglmasuk', '<', $selectedMonth);
                                      });
                            })
                            ->whereDoesntHave('wip', function ($query) {
                                $query->where('proses', '=', 'Unit OK');
                            })
                            ->get();
    
    $combinedNosppData = $sppData->concat($previousNosppData);
    
    $totalNosppEntry = $combinedNosppData->count();
    $totalRpEntry = number_format($combinedNosppData->sum('grandtotal'), 0, ',', '.');
    
    return view('wip.resume', compact(
        'sppData', 'unitOkData', 'combinedWipData', 'combinedNosppData',
        'totalNosppEntry', 'totalRpEntry', 
        'totalNosppOk', 'totalRpOk', 'totalNosppWip', 'totalRpWip',
        'selectedMonth', 'selectedYear',
        'bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi', 'id_bengkel'
    ));
}

    public function wipscanin(Request $request)
    {
        // Validate the incoming request to ensure 'nospp' is present
        $request->validate([
            'nospp' => 'required|string', // Adjust validation rules as needed
        ]);

        // Fetch the nospp from the request
        $nospp = $request->query('nospp');

        // Assuming you have a method to fetch SPP data by nospp
        $sppData = Spp::where('nospp', $nospp)->first();

        // Return the view and pass the nospp and sppData to it
        return view('wip.scanin', compact('nospp', 'sppData'));
    }

    public function wipscanout(Request $request)
    {
        // Validate the incoming request to ensure 'nospp' is present
        $request->validate([
            'nospp' => 'required|string', // Adjust validation rules as needed
        ]);

        // Fetch the nospp from the request
        $nospp = $request->query('nospp');

        // Assuming you have a method to fetch SPP data by nospp
        $sppData = Spp::where('nospp', $nospp)->first();

        // Return the view and pass the nospp and sppData to it
        return view('wip.scanout', compact('nospp', 'sppData'));
    }
    public function processWipScanout(Request $request)
    {
        $validated = $request->validate([
            'nospp' => 'required|string',
            'qr_code' => 'required|string',
        ]);
    
        $nospp = $validated['nospp'];
        $qrCode = $validated['qr_code'];
    
        // Validate the QR code
        if ($qrCode !== $nospp) {
            return response()->json(['error' => 'QR Code does not match the provided nospp.'], 400);
        }
    
        // Get the latest WIP for the specified nospp
        $latestWip = Wip::where('nospp', $nospp)->latest()->first();
    
        if (!$latestWip) {
            return response()->json(['error' => 'No WIP entry found for the provided nospp.'], 404);
        }
    
        // Logic to handle stopping the current WIP entry
        if ($latestWip->stopped_at === null) {
            $latestWip->stopped_at = now(); // Mark as stopped
            $latestWip->save();
    
            // Clear the nospp from session
            session()->forget('nospp');
    
            return response()->json(['success' => 'WIP entry stopped successfully.'], 200);
        }
    
        return response()->json(['error' => 'WIP entry has already been stopped.'], 409);
    }
    
    

    public function show($id_wips)
    {
        $wip = Wip::find($id_wips);

        if (!$wip) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json(['wip' => $wip]);
    }

    public function tambahwipview(Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $nospp = $request->query('nospp');

        if (!Spp::where('nospp', $nospp)->where('id_bengkel', $id_bengkel)->exists()) {
            return redirect()->route('manajemenwip')->with('error', 'No SPP tidak valid.');
        }

        return view('wip.tambah', compact('nospp', 'id_bengkel'));
    }

    public function tambahwip(Request $request)
    {
        // Validasi input
        $request->validate([
            'nospp' => 'required|exists:spps,nospp',
            'proses' => 'required|string',
            'keterangan' => 'nullable|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'camera_image' => 'nullable|string' // Handle gambar base64 dari kamera
        ]);
    
        // Ambil query parameter
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
        
        // Ambil id_bengkel
        $id_bengkel = $this->getIdBengkel($request);
        
        if (is_array($id_bengkel)) {
            abort(500, 'Terjadi kesalahan saat mendapatkan ID Bengkel.');
        }
    
        // Generate unique id_wips
        $lastWip = Wip::where('id_bengkel', $id_bengkel)->orderBy('id_wips', 'desc')->first();
        $lastIdWips = $lastWip ? intval(substr($lastWip->id_wips, strlen($id_bengkel))) : 0;
        $newIdWips = $id_bengkel . str_pad($lastIdWips + 1, 6, '0', STR_PAD_LEFT);
    
        // Ambil data dari request
        $data = $request->only(['nospp', 'proses', 'keterangan']);
        $data['id_wips'] = $newIdWips;
        $data['id_bengkel'] = $id_bengkel;

        // Jika proses yang dipilih adalah "Unit OK", isi stopped_at dengan waktu saat ini
        if ($request->proses === 'Unit OK') {
            $data['stopped_at'] = Carbon::now();
        }
    
        // Handle file uploads dan base64 camera image
        $fotoPaths = [];
    
        // Jika ada file yang diunggah melalui file input
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $file->store('images', 'public'); // Simpan file ke folder public/storage/images
                $fotoPaths[] = $path; // Tambahkan path ke array fotoPaths
            }
        }
    
        // Jika gambar diambil dari kamera (base64)
        if ($request->filled('camera_image')) {
            // Ambil data gambar base64 dari kamera
            $imageData = $request->input('camera_image');
            $imageData = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,'], '', $imageData); // Hapus header base64 jika ada
            $imageData = base64_decode($imageData); // Decode base64 menjadi binary
    
            // Tentukan nama file untuk gambar dari kamera
            $imageName = 'camera_' . time() . '.png';
            $path = storage_path('app/public/images/' . $imageName); // Tentukan path penyimpanan
    
            // Simpan gambar dari kamera ke storage
            file_put_contents($path, $imageData);
    
            // Tambahkan path gambar dari kamera ke array fotoPaths
            $fotoPaths[] = 'images/' . $imageName;
        }
    
        // Simpan semua foto ke dalam kolom 'foto' sebagai JSON
        $data['foto'] = json_encode($fotoPaths);
    
        try {
            // Buat record baru di tabel WIP
            Wip::create($data);
        } catch (\Exception $e) {
            // Tangani kesalahan saat menyimpan data WIP
            abort(500, 'Terjadi kesalahan saat menyimpan data WIP.');
        }
    
        // Redirect ke halaman manajemen WIP dengan pesan sukses
        return redirect()->route('manajemenwip', [
            'id_bengkel' => $data['id_bengkel'],
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi
        ])->with('success', 'Data WIP berhasil ditambahkan');
    }
    

    

public function hapuswip($id_wips, Request $request)
{
    $wip = Wip::where('id_wips', $id_wips)->firstOrFail();

    if ($wip->foto) {
        $fotos = json_decode($wip->foto);
        foreach ($fotos as $foto) {
            Storage::disk('public')->delete($foto);
        }
    }

    $wip->delete();

    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');
    
    return redirect()->route('manajemenwip', [
        'id_bengkel' => $wip->id_bengkel,
        'bisnis' => $bisnis,
        'manufaktur' => $manufaktur,
        'dealer' => $dealer,
        'cabang' => $cabang,
        'lokasi' => $lokasi
    ])->with('success', 'Data WIP berhasil dihapus');
}

public function hapusunitok($id_wips, Request $request)
{
    $wip = Wip::where('id_wips', $id_wips)->firstOrFail();

    if ($wip->foto) {
        $fotos = json_decode($wip->foto);
        foreach ($fotos as $foto) {
            Storage::disk('public')->delete($foto);
        }
    }

    $wip->delete();

    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');
    
    return redirect()->route('resumewip', [
        'id_bengkel' => $wip->id_bengkel,
        'bisnis' => $bisnis,
        'manufaktur' => $manufaktur,
        'dealer' => $dealer,
        'cabang' => $cabang,
        'lokasi' => $lokasi
    ])->with('success', 'Unit OK berhasil dihapus');
}
    

public function tambahfoto(Request $request)
{
    $request->validate([
        'id_wips' => 'required|exists:wips,id_wips',
        'foto' => 'required|array',
        'foto.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
    
    $wip = Wip::where('id_wips', $request->id_wips)->firstOrFail();
    
    $fotoPaths = json_decode($wip->foto, true) ?? [];
    
    foreach ($request->file('foto') as $file) {
        $path = $file->store('images', 'public');
        $fotoPaths[] = $path;
    }
    
    $wip->foto = json_encode($fotoPaths);
    $wip->save();
    
    return redirect()->back()->with('success', 'Foto berhasil ditambahkan');
}


    public function hapusfoto($foto)
    {
        Storage::disk('public')->delete($foto);

        return redirect()->back()->with('success', 'Foto berhasil dihapus');
    }
}