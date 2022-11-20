<?php

use App\Http\Controllers\GamesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::middleware('auth')->group(function () {
    Route::get('/games', [GamesController::class, 'index'])->name('games.list');
    Route::post('/predictions/save', [GamesController::class, 'store'])->name('prediction.save');
});



require __DIR__.'/auth.php';
