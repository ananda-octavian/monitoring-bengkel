<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\WipController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\DetailupController;
use App\Http\Controllers\NavigationController;
use Illuminate\Support\Facades\Session;

Route::get('/session/refresh', function() {
    Session::put('last_activity', time());
    return response()->json(['status' => 'Session refreshed']);
})->name('session.refresh');

// Public Routes
Route::get('/', [HomeController::class, 'preloginview'])->name('prelogin');
Route::get('/padmabusinesslogin', [HomeController::class, 'loginview'])->name('login');
Route::post('/login', [HomeController::class, 'authenticate'])->name('authenticate');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');

// Protected Routes with auth.session Middleware
Route::middleware(['auth.session'])->group(function () {

    // Home Route
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // Info Route
    Route::get('/info', [HomeController::class, 'info'])->name('info');

    // Manajemen Route
    Route::get('/manajemen', [HomeController::class, 'manajemen'])->name('manajemen');

    // Navigation Routes for Superadmin
    Route::get('/bisnis', [NavigationController::class, 'bisnis'])->name('bisnis');
    Route::get('/manufaktur', [NavigationController::class, 'manufaktur'])->name('manufaktur');
    Route::get('/dealer', [NavigationController::class, 'dealer'])->name('dealer');
    Route::get('/cabang', [NavigationController::class, 'cabang'])->name('cabang');
    Route::get('/lokasi', [NavigationController::class, 'lokasi'])->name('lokasi');

    // Spp Management Routes
    Route::get('/spps', [SppController::class, 'sppview'])->name('manajemenspp');
    Route::get('/spps/viewall', [SppController::class, 'viewAllUnitEntry'])->name('viewallunitentry');
    Route::get('/spps/tambah', [SppController::class, 'tambahsppview'])->name('tambahsppview');
    Route::post('/spps/tambah', [SppController::class, 'tambahspp'])->name('tambahspp');
    Route::get('/spps/edit/{nospp}', [SppController::class, 'editsppview'])->name('editsppview');
    Route::put('/spps/edit/{nospp}', [SppController::class, 'updatespp'])->name('updatespp');
    Route::delete('/spps/hapus/{nospp}', [SppController::class, 'hapusspp'])->name('hapusspp');
    Route::get('/importspp/form', [SppController::class, 'showFormspp'])->name('importspp.form');
    Route::post('/importspp', [SppController::class, 'importspp'])->name('importspp');
    Route::get('/spps/search', [SppController::class, 'sppsearch'])->name('sppsearch');
    Route::get('/spps/export', [SppController::class, 'export'])->name('spp.export');
    Route::post('/spps/import', [SppController::class, 'import'])->name('spp.import');
    Route::get('/spps/print/{nospp}', [SppController::class, 'printSpp'])->name('printspp');

    // User Management
    Route::get('/users', [UserController::class, 'userview'])->name('manajemenuser');
    Route::get('/users/add', [UserController::class, 'adduserview'])->name('adduserview');
    Route::post('/users/add', [UserController::class, 'adduser'])->name('adduser');
    Route::get('/users/addinside', [UserController::class, 'addinsideuserview'])->name('addinsideuserview');
    Route::post('/users/addinside', [UserController::class, 'addinsideuser'])->name('addinsideuser');
    Route::get('/users/edit/{id_user}', [UserController::class, 'edituserview'])->name('edituserview');
    Route::put('/users/edit/{id_user}', [UserController::class, 'updateuser'])->name('updateuser');
    Route::delete('/users/hapus/{id_user}', [UserController::class, 'hapususer'])->name('hapususer');
    Route::get('/users/search', [UserController::class, 'usersearch'])->name('usersearch');

    // Wip Management Routes
    Route::get('/wip', [WipController::class, 'wipview'])->name('manajemenwip');
    Route::get('/wip/viewall', [WipController::class, 'viewAllWip'])->name('viewallwip');
    Route::get('/resume', [WipController::class, 'resumeview'])->name('resumewip');
    Route::get('/wip/tambah', [WipController::class, 'tambahwipview'])->name('tambahwipview');
    Route::post('/wip/tambah', [WipController::class, 'tambahwip'])->name('tambahwip');
    Route::delete('/wip/hapus/{id_wips}', [WipController::class, 'hapuswip'])->name('hapuswip');
    Route::delete('/wip/hapusunitok/{id_wips}', [WipController::class, 'hapusunitok'])->name('hapusunitok');
    Route::get('/wip/search', [WipController::class, 'wipsearch'])->name('wipsearch');
    Route::get('/stopwip', [WipController::class, 'stopWip'])->name('stopwip');
    Route::post('/wip/scanout', [WipController::class, 'processWipScanout'])->name('processWipScanout');

    // Routes for adding and deleting photos
    Route::post('/wip/{id_wips}/tambahfoto', [WipController::class, 'tambahfoto'])->name('tambahfoto');
    Route::delete('/wip/hapusfoto/{foto}', [WipController::class, 'hapusfoto'])->name('hapusfoto');

    // Detailup Management Routes
    Route::get('detailups/{nospp}', [DetailupController::class, 'detailupview'])->name('manajemendetailup');
    Route::get('detailup/tambah/{nospp}', [DetailupController::class, 'tambahdetailupview'])->name('tambahdetailupview');
    Route::post('detailup/tambah', [DetailupController::class, 'tambahdetailup'])->name('tambahdetailup');
    Route::get('detailup/edit/{id_uraian}', [DetailupController::class, 'editdetailupview'])->name('editdetailupview');
    Route::post('detailup/update/{id_uraian}', [DetailupController::class, 'updatedetailup'])->name('updatedetailup');
    Route::delete('detailup/hapus/{id_uraian}', [DetailupController::class, 'hapusdetailup'])->name('hapusdetailup');    

    // Workshop Management Routes
    Route::get('/workshops', [WorkshopController::class, 'workshopview'])->name('manajemenworkshop');
    Route::get('/workshops/add', [WorkshopController::class, 'addworkshopview'])->name('addworkshopview');
    Route::post('/workshops/add', [WorkshopController::class, 'addworkshop'])->name('addworkshop');
    Route::get('/workshops/addinside', [WorkshopController::class, 'addinsideworkshopview'])->name('addinsideworkshopview');
    Route::post('/workshops/addinside', [WorkshopController::class, 'addinsideworkshop'])->name('addinsideworkshop');
    Route::get('/workshops/edit/{id_bengkel}', [WorkshopController::class, 'editworkshopview'])->name('editworkshopview');
    Route::put('/workshops/edit/{id_bengkel}', [WorkshopController::class, 'updateworkshop'])->name('updateworkshop');
    Route::delete('/workshops/hapus/{id_bengkel}', [WorkshopController::class, 'hapusworkshop'])->name('hapusworkshop');
    Route::get('/workshops/search', [WorkshopController::class, 'workshopsearch'])->name('workshopsearch');
    Route::get('/get-filtered-data', [WorkshopController::class, 'getFilteredData'])->name('getFilteredData');

});
