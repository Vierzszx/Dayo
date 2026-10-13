<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenawaranController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TokoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('penawaran.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1');

    Route::get('/register', [AuthController::class, 'registerForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::resource('toko', TokoController::class)
        ->parameters(['toko' => 'id']);

    Route::resource('produk', ProdukController::class)
        ->parameters(['produk' => 'id']);

    Route::resource('penawaran', PenawaranController::class)
        ->parameters(['penawaran' => 'id']);

    Route::resource('pesanan', PesananController::class)
        ->parameters(['pesanan' => 'id']);
});