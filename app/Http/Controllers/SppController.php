<?php

namespace App\Http\Controllers;

use App\Models\Spp;
use App\Models\Wip;
use App\Models\Workshop;
use App\Exports\SppExport;
use App\Imports\SppImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SppController extends Controller
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
            // Tangani kasus dimana workshop tidak ditemukan
            abort(404, 'Workshop tidak ditemukan');
        }

        return $workshop->id_bengkel;
    }

   public function sppview(Request $request)
{
    $id_bengkel = $this->getIdBengkel($request);
    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');
    $keyword = $request->query('keyword');

    $selectedMonth = $request->query('month', date('m'));
    $selectedYear = $request->query('year', date('Y'));

    $sort = $request->query('sort');
    $order = $request->query('order', 'asc');

    $query = Spp::where('id_bengkel', $id_bengkel)
        ->whereMonth('tglmasuk', $selectedMonth)
        ->whereYear('tglmasuk', $selectedYear);

    if ($keyword) {
        $query->where(function ($q) use ($keyword) {
            $q->where('nospp', 'like', '%' . $keyword . '%')
                ->orWhere('nopol', 'like', '%' . $keyword . '%')
                ->orWhere('sa', 'like', '%' . $keyword . '%')
                ->orWhere('type', 'like', '%' . $keyword . '%')
                ->orWhere('warna', 'like', '%' . $keyword . '%')
                ->orWhere('damage', 'like', '%' . $keyword . '%')
                ->orWhere('asuransi', 'like', '%' . $keyword . '%');
        });
    }

    // Jika parameter sort valid, gunakan. Jika tidak, urut berdasarkan angka nospp setelah 10 karakter
    $validSortColumns = ['nospp', 'nopol', 'sa', 'asuransi', 'type', 'warna', 'damage', 'tglmasuk', 'estimasi', 'proses', 'grandtotal'];

    if ($sort && in_array($sort, $validSortColumns)) {
        $query->orderBy($sort, $order);
    } else {
        // Default urut nospp berdasarkan angka di belakang
        $query->orderByRaw('CAST(SUBSTRING(nospp, 11) AS UNSIGNED) DESC');
    }

    $sppData = $query->paginate(9999);

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

    return view('spp.index', compact(
        'sppData', 'combinedNosppData',
        'totalNosppEntry', 'totalRpEntry',
        'selectedMonth', 'selectedYear',
        'bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi', 'id_bengkel', 'keyword', 'sort', 'order'
    ));
}



public function viewAllUnitEntry(Request $request)
{
    $id_bengkel = $this->getIdBengkel($request);
    $limit = 500;
    $keyword = $request->input('keyword');
    $selectedMonth = $request->query('month', date('m'));
    $selectedYear = $request->query('year', date('Y'));

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

    // Ambil semua tahun unik dari tglmasuk
    $availableYears = Spp::where('id_bengkel', $id_bengkel)
        ->selectRaw('YEAR(tglmasuk) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    if ($selectedYear) {
        $sppDataQuery->whereYear('tglmasuk', $selectedYear);
    }

    $sppData = $sppDataQuery->orderBy('nospp', 'desc')->paginate($limit);

    $months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];

    $monthlyDataMap = [];
    foreach ($months as $month) {
        $monthlyDataMap["{$selectedYear}-{$month}"] = [];
    }

    foreach ($sppData as $spp) {
        $monthYear = \Carbon\Carbon::parse($spp->tglmasuk)->format('Y-M');
        if (isset($monthlyDataMap[$monthYear])) {
            $monthlyDataMap[$monthYear][] = $spp->nospp;
        }
    }

    return view('spp.viewallunitentry', [
        'sppData' => $sppData,
        'monthlyDataMap' => $monthlyDataMap,
        'availableYears' => $availableYears, // Tambahkan ini untuk digunakan di tampilan
        'id_bengkel' => $id_bengkel,
        'keyword' => $keyword,
        'month' => $selectedMonth,
        'year' => $selectedYear
    ]);
}



public function printSpp($nospp)
{
    // Fetch the SPP data based on the nospp parameter
    $sppData = Spp::with('workshop')->where('nospp', $nospp)->firstOrFail();

    // You can also retrieve any additional data you need here
    $bisnis = $sppData->workshop->bisnis; // Example, adjust as needed
    $manufaktur = $sppData->workshop->manufaktur; // Example, adjust as needed
    $dealer = $sppData->workshop->dealer; // Example, adjust as needed
    $cabang = $sppData->workshop->cabang; // Example, adjust as needed
    $lokasi = $sppData->workshop->lokasi; // Example, adjust as needed

    return view('spp.printspp', compact('sppData', 'bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi'));
}

    public function sppsearch(Request $request)
{
    $keyword = $request->input('keyword', ''); // Default ke string kosong jika tidak ada input
    $bisnis = $request->query('bisnis', ''); // Pastikan bisnis juga memiliki default
    $manufaktur = $request->query('manufaktur', '');
    $dealer = $request->query('dealer', '');
    $cabang = $request->query('cabang', '');
    $lokasi = $request->query('lokasi', '');
    
    // Menggunakan nilai bulan dan tahun default jika tidak disediakan
    $month = $request->input('month', date('m')); // Default ke bulan saat ini
    $year = $request->input('year', date('Y')); // Default ke tahun saat ini

    return redirect()->route('manajemenspp', [
        'keyword' => $keyword,
        'bisnis' => $bisnis,
        'manufaktur' => $manufaktur,
        'dealer' => $dealer,
        'cabang' => $cabang,
        'lokasi' => $lokasi,
        'month' => $month,
        'year' => $year
    ]);
}


    public function export(Request $request)
{
    // Get query parameters from the URL
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');

    // Validate date input
    $startDate = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : Carbon::now()->startOfMonth()->format('Y-m-d');
    $endDate = $endDate ? Carbon::parse($endDate)->format('Y-m-d') : Carbon::now()->endOfMonth()->format('Y-m-d');

    // Generate the Excel export file
    return Excel::download(
        new SppExport($startDate, $endDate, $bisnis, $manufaktur, $dealer, $cabang, $lokasi),
        'unitentry_export_' . 
        preg_replace('/\s+/', '_', $bisnis) . '_' . 
        preg_replace('/\s+/', '_', $manufaktur) . '_' . 
        preg_replace('/\s+/', '_', $dealer) . '_' . 
        preg_replace('/\s+/', '_', $cabang) . '_' . 
        preg_replace('/\s+/', '_', $lokasi) . '_' . 
        $startDate . '_to_' . $endDate . '.xlsx'
    );    
}

public function import(Request $request)
{
    // Get the 'id_bengkel' from the request or other logic
    $id_bengkel = $this->getIdBengkel($request);

    // Perform the import
    Excel::import(new SppImport($id_bengkel), $request->file('file'));

            // Extract additional parameters from the request if needed
    $bisnis = $request->query('bisnis');
    $manufaktur = $request->query('manufaktur');
    $dealer = $request->query('dealer');
    $cabang = $request->query('cabang');
    $lokasi = $request->query('lokasi');

    return redirect()->route('manajemenspp', [
        'bisnis' => $bisnis,
        'manufaktur' => $manufaktur,
        'dealer' => $dealer,
        'cabang' => $cabang,
        'lokasi' => $lokasi
    ])->with('success', 'Data SPP berhasil diupdate.');
}



    public function showFormspp(Request $request)
    {
        // Extract additional parameters from the request if needed
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
        
        return view('importspp.form', compact('bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi'));
    }


    public function tambahsppview(Request $request)
    {
        // Extract additional parameters from the request if needed
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
        
        return view('spp.tambah', compact('bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi'));
    }

    public function tambahspp(Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $prefixedNospp = $id_bengkel . $request->nospp;
    
        $request->validate([
            'nopol' => 'required',
            'nospp' => ['required', function ($attribute, $value, $fail) use ($prefixedNospp) {
                if (Spp::where('nospp', $prefixedNospp)->exists()) {
                    $fail('The ' . $attribute . ' has already been taken.');
                }
            }],
            'sa' => 'nullable|string',
            'type' => 'nullable|string',
            'warna' => 'nullable|string',
            'damage' => 'nullable|string',
            'tglmasuk' => 'required|date',
            'estimasi' => 'nullable|date',
            'asuransi' => 'nullable|string',
            'grandtotal' => 'nullable|numeric',
        ]);
    
        // Buat entri baru di tabel SPP
        $spp = Spp::create([
            'nopol' => $request->nopol,
            'nospp' => $prefixedNospp,
            'sa' => $request->sa,
            'type' => $request->type,
            'warna' => $request->warna,
            'damage' => $request->damage,
            'tglmasuk' => $request->tglmasuk,
            'estimasi' => $request->estimasi,
            'asuransi' => $request->asuransi,
            'grandtotal' => $request->grandtotal,
            'id_bengkel' => $id_bengkel,
        ]);
    
        // Buat entri baru di tabel WIP
        $lastWip = Wip::where('id_wips', 'LIKE', $id_bengkel . '%')
                      ->orderBy('id_wips', 'desc')
                      ->first();
        $lastIdWips = $lastWip ? intval(substr($lastWip->id_wips, strlen($id_bengkel))) : 0;
        $newIdWips = $id_bengkel . str_pad($lastIdWips + 1, 6, '0', STR_PAD_LEFT);
    
        while (Wip::where('id_wips', $newIdWips)->exists()) {
            $lastIdWips++;
            $newIdWips = $id_bengkel . str_pad($lastIdWips + 1, 6, '0', STR_PAD_LEFT);
        }
    
        Wip::create([
            'id_wips' => $newIdWips,
            'nospp' => $prefixedNospp,
            'proses' => 'Job Dispatch',
            'id_bengkel' => $id_bengkel,
            'stopped_at' => now(),
        ]);
    
        // Ambil parameter untuk QR Code
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');
    
        // Generate QR Code data
        $qrCodeData = QrCode::size(200)->generate(route('manajemenspp', [
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi,
            'nospp' => $prefixedNospp // Make sure this is the correct value
        ]));        
    
        return redirect()->route('tambahdetailupview', [
            'nospp' => $prefixedNospp,
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi
        ])->with([
            'success' => 'Data SPP berhasil ditambahkan.',
            'qrCodeData' => $qrCodeData,
            'spp' => $spp // Sertakan model SPP dalam session
        ]);
    }
    

    
    
    

    public function editsppview($nospp, Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $spp = Spp::where('nospp', $nospp)->where('id_bengkel', $id_bengkel)->first();

        if (!$spp) {
            return redirect()->route('manajemenspp')->with('error', 'Data tidak ditemukan.');
        }

        // Extract additional parameters from the request if needed
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');

        return view('spp.edit', compact('spp', 'bisnis',  'manufaktur', 'dealer', 'cabang', 'lokasi'));
    }

    public function updatespp(Request $request, $nospp)
    {
        $request->validate([
            'nopol' => 'required',
            'sa' => 'nullable',
            'type' => 'nullable',
            'warna' => 'nullable',
            'damage' => 'nullable',
            'tglmasuk' => 'required',
            'estimasi' => 'nullable',
            'diterima' => 'nullable',
            'asuransi' => 'nullable',
            'grandtotal' => 'nullable',
        ]);

        $id_bengkel = $this->getIdBengkel($request);
        $spp = Spp::where('nospp', $nospp)->where('id_bengkel', $id_bengkel)->first();

        if (!$spp) {
            return redirect()->route('manajemenspp')->with('error', 'Data tidak ditemukan.');
        }

        $spp->update([
            'nopol' => $request->input('nopol'),
            'sa' => $request->input('sa'),
            'type' => $request->input('type'),
            'warna' => $request->input('warna'),
            'damage' => $request->input('damage'),
            'tglmasuk' => $request->input('tglmasuk'),
            'estimasi' => $request->input('estimasi'),
            'diterima' => $request->input('diterima'),
            'asuransi' => $request->input('asuransi'),
        ]);

        // Extract additional parameters from the request if needed
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');

        return redirect()->route('manajemenspp', [
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi
        ])->with('success', 'Data SPP berhasil diupdate.');
    }

    public function hapusspp($nospp, Request $request)
    {
        $id_bengkel = $this->getIdBengkel($request);
        $spp = Spp::where('nospp', $nospp)->where('id_bengkel', $id_bengkel)->first();

        if (!$spp) {
            return redirect()->route('manajemenspp')->with('error', 'Data tidak ditemukan.');
        }

        $spp->delete();

        // Extract additional parameters from the request if needed
        $bisnis = $request->query('bisnis');
        $manufaktur = $request->query('manufaktur');
        $dealer = $request->query('dealer');
        $cabang = $request->query('cabang');
        $lokasi = $request->query('lokasi');

        return redirect()->route('manajemenspp', [
            'bisnis' => $bisnis,
            'manufaktur' => $manufaktur,
            'dealer' => $dealer,
            'cabang' => $cabang,
            'lokasi' => $lokasi
        ])->with('success', 'Data SPP telah dihapus.');
    }
}
