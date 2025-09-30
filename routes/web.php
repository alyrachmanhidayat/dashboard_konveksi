<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekapController;

// Public routes (accessible to guests)
Route::get('/', [SpkController::class, 'index'])->name('home');
// SPK Routes
Route::get('/spk', [SpkController::class, 'create'])->name('spk.create');
Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
Route::get('/spk/{spk}/edit', [SpkController::class, 'edit'])->name('spk.edit'); // ubah dari {spk} menjadi {spk}/edit
Route::put('/spk/{spk}', [SpkController::class, 'update'])->name('spk.update'); // route untuk update data
Route::post('/spk/{spk}/status', [SpkController::class, 'updateStatus'])->name('spk.update_status'); // route untuk progress & status


// Routes that require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/spk-close', [SpkController::class, 'spkClosedIndex'])->name('spk-close');
    Route::post('/spk-close/{spk}/save-price', [SpkController::class, 'savePrice'])->name('spk.save_price');

    Route::get('/dashboard', [SpkController::class, 'index'])->name('dashboard');

    // Invoice Routes
    Route::get('/invoice', [SpkController::class, 'invoiceIndex'])->name('invoice.index');
    Route::post('/invoice/publish', [SpkController::class, 'publishInvoice'])->name('invoice.publish');

    // Piutang (Accounts Receivable) Routes
    Route::get('/piutang', [SpkController::class, 'piutangIndex'])->name('piutang.index');
    Route::post('/piutang/{invoice}/pay', [SpkController::class, 'storePayment'])->name('piutang.pay');


    // Rekap Routes
    Route::get('/rekap-omzet', [RekapController::class, 'omzetIndex'])->name('rekap-omzet');
    Route::get('/rekap-reject', [RekapController::class, 'rejectIndex'])->name('rekap-reject');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
