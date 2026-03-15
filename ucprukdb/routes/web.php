<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientBioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WheelchairController;
use App\Http\Controllers\ClientAssessmentController;
use App\Http\Controllers\DataManagementController;
use App\Http\Controllers\ClientExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth routes (guest only) with rate limiting
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
});

// Protected routes (authenticated users)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sebaran-data', [DashboardController::class, 'sebaranData'])->name('sebaran-data');

    // Client Bio CRUD
    Route::resource('client-bio', ClientBioController::class);

    // Wheelchair Management
    Route::resource('wheelchairs', WheelchairController::class);

    // Client Assessments
    Route::resource('client-assessments', ClientAssessmentController::class);

    // Customizable Client Export
    Route::get('/export-client', [ClientExportController::class, 'index'])->name('export.client.index');
    Route::post('/export-client', [ClientExportController::class, 'export'])->name('export.client.process');

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        // Data Management
        Route::prefix('data-management')->name('data-management.')->group(function () {
            Route::get('/', [DataManagementController::class, 'index'])->name('index');
            Route::get('/export-csv', [DataManagementController::class, 'exportCsv'])->name('export.csv');
            Route::post('/import-csv', [DataManagementController::class, 'importCsv'])->name('import.csv');
            Route::get('/export-sql', [DataManagementController::class, 'exportSql'])->name('export.sql');
            Route::post('/import-sql', [DataManagementController::class, 'importSql'])->name('import.sql');
        });
    });
});
