<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');

    Route::middleware(['admin'])->group(function () {
        Route::get('/trades/create', [TradeController::class, 'create'])->name('trades.create');
        Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
        Route::get('/trades/{trade}/edit', [TradeController::class, 'edit'])->name('trades.edit');
        Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
        Route::delete('/trades/{trade}', [TradeController::class, 'destroy'])->name('trades.destroy');
    });
});