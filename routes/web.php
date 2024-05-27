<?php

use App\Http\Controllers\AdministratorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/register-administrator', [AdministratorController::class, 'register']);
Route::post('/login-administrator', [AdministratorController::class, 'login']);


Route::controller(AdministratorController::class)->group(function () {
    Route::middleware('auth:administrators')->group(function() {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });
});
