<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpkController;
use Illuminate\Support\Facades\Route;

// Public routes (accessible to guests)
Route::get('/', [SpkController::class, 'index'])->name('home');

// Routes that require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SpkController::class, 'index'])->name('dashboard');

    Route::get('/spk-close', [SpkController::class, 'spkClosedIndex'])->name('spk-close');

    // Route::get('/invoice', function () {
    //     return view('invoice');
    // })->name('invoice');

    Route::get('/invoice', [SpkController::class, 'invoiceIndex'])->name('invoice.index');
    Route::post('/invoice/publish', [SpkController::class, 'publishInvoice'])->name('invoice.publish');

    // Route::get('/piutang', function () {
    //     return view('piutang');
    // })->name('piutang');

    Route::get('/piutang', [SpkController::class, 'piutangIndex'])->name('piutang.index');

    Route::get('/rekap-omzet', function () {
        return view('rekap-omzet');
    })->name('rekap-omzet');

    Route::get('/rekap-reject', function () {
        return view('rekap-reject');
    })->name('rekap-reject');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/spk', [SpkController::class, 'create'])->name('spk.create');
    Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');

    Route::get('/spk/{spk}', [SpkController::class, 'edit'])->name('spk.edit');
});

require __DIR__ . '/auth.php';
