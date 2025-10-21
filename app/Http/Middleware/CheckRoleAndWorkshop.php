<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CheckRoleAndWorkshop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }

        $role = $user->level; // Assuming 'level' is the column name for role
        $idBengkel = $user->id_bengkel; // Assuming 'id_bengkel' is the column name for workshop

        // Define accessible routes for each role
        $routes = [
            'superadmin' => [
                'bisnis', 'manufaktur', 'cabang', 'lokasi', 'home', 'info', 'manajemen',
                'manajemenspp', 'tambahsppview', 'tambahspp', 'editsppview', 'updatespp',
                'hapusspp', 'spp.export', 'importspp.form', 'importspp', 'sppsearch',
                'manajemendetailup', 'tambahdetailupview', 'tambahdetailup', 'editdetailupview',
                'updatedetailup', 'hapusdetailup', 'manajemenwip', 'tambahwipview', 'tambahwip',
                'editwipview', 'updatewip', 'hapuswip', 'hapusunitok', 'wipsearch', 'viewallwip',
                'resumewip', 'manajemenup', 'tambahupview', 'tambahup', 'editupview', 'updateup',
                'hapusup', 'upsearch', 'manajemenworkshop', 'addworkshopview', 'addworkshop',
                'addinsideworkshopview', 'addinsideworkshop', 'editworkshopview', 'updateworkshop',
                'hapusworkshop', 'workshopsearch', 'manajemenuser', 'adduserview', 'adduser',
                'addinsideuserview', 'addinsideuser', 'edituserview', 'updateuser', 'hapususer',
                'importuser.form', 'importuser', 'usersearch'
            ],
            'admin' => [
                'bisnis', 'manufaktur', 'cabang', 'lokasi', 'home', 'info', 'manajemenspp',
                'tambahsppview', 'tambahspp', 'editsppview', 'updatespp', 'hapusspp',
                'importspp.form', 'importspp', 'sppsearch', 'manajemendetailup',
                'tambahdetailupview', 'tambahdetailup', 'editdetailupview', 'updatedetailup',
                'hapusdetailup', 'manajemenwip', 'tambahwipview', 'tambahwip', 'editwipview',
                'updatewip', 'hapuswip', 'wipsearch', 'viewallwip', 'resumewip', 'trackingwip',
                'trackingsearch'
            ],
            'manajemen' => [
                'bisnis', 'manufaktur', 'cabang', 'lokasi', 'home', 'info', 'manajemen',
                'manajemenspp', 'tambahsppview', 'tambahspp', 'editsppview', 'updatespp',
                'hapusspp', 'importspp.form', 'importspp', 'sppsearch', 'manajemendetailup',
                'tambahdetailupview', 'tambahdetailup', 'editdetailupview', 'updatedetailup',
                'hapusdetailup', 'manajemenwip', 'tambahwipview', 'tambahwip', 'editwipview',
                'updatewip', 'hapuswip', 'wipsearch', 'viewallwip', 'trackingwip', 'trackingsearch',
                'manajemenup', 'tambahupview', 'tambahup', 'editupview', 'updateup', 'hapusup',
                'upsearch', 'manajemenworkshop', 'addworkshopview', 'addworkshop',
                'addinsideworkshopview', 'addinsideworkshop', 'editworkshopview', 'updateworkshop',
                'hapusworkshop', 'workshopsearch', 'manajemenuser', 'tambahuserview', 'tambahuser',
                'edituserview', 'updateuser', 'hapususer', 'importuser.form', 'importuser',
                'usersearch'
            ]
        ];

        $routeName = $request->route()->getName();

        if (isset($routes[$role])) {
            if (in_array($routeName, $routes[$role])) {
                Log::info("{$role} access granted for route: {$routeName}");
                return $next($request);
            } else {
                Log::warning("{$role} access denied for route: {$routeName}");
                return redirect('/')->withErrors('You do not have access to this page.');
            }
        }

        // Log the access denial
        Log::warning('Access denied for user: ' . $user->username . ', Route: ' . $routeName);

        // If user does not have access, redirect to home with an error message
        return redirect('/')->withErrors('You do not have access to this page.');
    }
}
