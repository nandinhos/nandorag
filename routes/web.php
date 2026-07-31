<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/admin/login', fn () => redirect('/login'));

use App\Http\Controllers\TusController;

// TUS upload endpoint (suporta GET, HEAD, POST, PATCH, DELETE)
Route::middleware(['web', 'auth'])->group(function () {
    Route::any('/tus/upload{path?}', [TusController::class, 'handle'])
        ->where('path', '.*')
        ->name('tus.upload');
});
