<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Spp;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{

    public function home(Request $request)
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
        
        $availableMonths = Spp::where('id_bengkel', $id_bengkel)
            ->selectRaw('MONTH(tglmasuk) as month')
            ->distinct()
            ->pluck('month')
            ->sort()
            ->toArray();
        
        $availableYears = Spp::where('id_bengkel', $id_bengkel)
            ->selectRaw('YEAR(tglmasuk) as year')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();
        
        return view('home', compact(
            'sppData', 'unitOkData', 'combinedWipData', 'combinedNosppData',
            'totalNosppEntry', 'totalRpEntry', 
            'totalNosppOk', 'totalRpOk', 'totalNosppWip', 'totalRpWip',
            'selectedMonth', 'selectedYear', 
            'availableMonths', 'availableYears', 
            'bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi', 'id_bengkel'
        ));
    }

    
    

    public function info()
    {
        return view('info');
    }

    public function manajemen()
    {
        return view('navigation.manajemen');
    }

    public function preloginview()
    {
        return view('login.prelogin');
    }

    public function loginview()
    {
        return view('login.index');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Login successful
            Auth::login($user);
            $request->session()->regenerate();

            // Store necessary session data
            Session::put([
                'level' => $user->level,
                'username' => $user->username,
                'id_bengkel' => $user->id_bengkel,
            ]);

            session(['id_user' => $user->id_user]);

            // Check user level and redirect accordingly
            if ($user->level === 'superadmin') {
                return redirect()->route('bisnis');
            } elseif ($user->level === 'adminpadma') {
                $workshop = Workshop::find($user->id_bengkel);
                if ($workshop) {
                    $bisnis = $workshop->bisnis;
                    $manufaktur = $workshop->manufaktur;
                    $dealer = $workshop->dealer;
                    $cabang = $workshop->cabang;
                    $lokasi = $workshop->lokasi;

                    return redirect()->route('home', [
                        'bisnis' => $bisnis,
                        'manufaktur' => $manufaktur,
                        'dealer' => $dealer,
                        'cabang' => $cabang,
                        'lokasi' => $lokasi
                    ]);
                } else {
                    return redirect()->route('logout')->with('error', 'Workshop tidak ditemukan');
                }
            } elseif ($user->level === 'kepalamekanik') {
                $workshop = Workshop::find($user->id_bengkel);
                if ($workshop) {
                    $bisnis = $workshop->bisnis;
                    $manufaktur = $workshop->manufaktur;
                    $dealer = $workshop->dealer;
                    $cabang = $workshop->cabang;
                    $lokasi = $workshop->lokasi;

                    return redirect()->route('home', [
                        'bisnis' => $bisnis,
                        'manufaktur' => $manufaktur,
                        'dealer' => $dealer,
                        'cabang' => $cabang,
                        'lokasi' => $lokasi
                    ]);
                } else {
                    return redirect()->route('logout')->with('error', 'Workshop tidak ditemukan');
                }
            } elseif ($user->level === 'manajemen') {
                return redirect()->route('bisnis');
            } else {
                return redirect('logout')->with('error', 'Level user tidak dikenal');
            }
        } else {
            // Login failed
            return redirect()->route('login')->with('error', 'Username/Password anda salah');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

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
}
