<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/dashboard-general-dashboard');

// Dashboard
Route::get('/dashboard-general-dashboard', function () {
    return view('pages.dashboard-general-dashboard', ['type_menu' => 'dashboard']);
});
Route::get('/dashboard-ecommerce-dashboard', function () {
    return view('pages.dashboard-ecommerce-dashboard', ['type_menu' => 'dashboard']);
});


//  Master Data
Route::get('/pasien', function () {
    return view('pages.pasien', ['type_menu' => 'master-data']);
});
Route::get('/dokter', function () {
    return view('pages.dokter', ['type_menu' => 'master-data']);
});

// Kunjungan
Route::get('/rujukan', function () {
    return view('pages.rujukan', ['type_menu' => 'kunjungan']);
});
Route::get('/pendaftaran', function () {
    return view('pages.pendaftaran', ['type_menu' => 'kunjungan']);
});

// credits
Route::get('/credits', function () {
    return view('pages.credits', ['type_menu' => '']);
});
