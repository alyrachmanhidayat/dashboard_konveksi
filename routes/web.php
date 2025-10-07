<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
// Tambahkan controller baru di sini
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RekapController; // Jika Anda sudah membuatnya


// =========================================================================
// PUBLIC
// =========================================================================
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// =========================================================================
// MANAJEMEN SPK
// =========================================================================
Route::get('/spk', [SpkController::class, 'create'])->name('spk.create');
Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
Route::get('/spk/{spk}/edit', [SpkController::class, 'edit'])->name('spk.edit');
Route::put('/spk/{spk}', [SpkController::class, 'update'])->name('spk.update');
Route::post('/spk/{spk}/status', [SpkController::class, 'updateStatus'])->name('spk.update_status');
Route::get('/spkClose-view', [InvoiceController::class, 'viewClosedRedirect'])->name('spk.closed.view');
// menampilkan halaman cetak SPK
Route::get('/spk/{spk}/print', [SpkController::class, 'print'])->name('spk.print');


Route::middleware('auth')->group(function () {
    // ---------------------------------------------------------------------
    // Keuangan (Invoice, Piutang, dll.)
    // ---------------------------------------------------------------------
    Route::get('/spk-close', [InvoiceController::class, 'spkClosedIndex'])->name('invoice.spk-close');
    Route::post('/spk-close/{spk}/save-price', [InvoiceController::class, 'savePrice'])->name('invoice.save_price');
    Route::get('/invoice', [InvoiceController::class, 'invoiceIndex'])->name('invoice.index');
    Route::get('/invoice/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::post('/invoice/publish', [InvoiceController::class, 'publishInvoice'])->name('invoice.publish');
    Route::get('/invoice/history', [InvoiceController::class, 'paidHistoryIndex'])->name('invoice.history');
    Route::get('/invoice/print/{invoiceIds}', [InvoiceController::class, 'printInvoice'])->name('invoice.print');
    Route::get('/piutang', [InvoiceController::class, 'piutangIndex'])->name('piutang.index');
    Route::post('/piutang/{invoice}/pay', [InvoiceController::class, 'payPiutang'])->name('piutang.pay');

    // ---------------------------------------------------------------------
    // Laporan (Rekapitulasi)
    // ---------------------------------------------------------------------
    Route::get('/rekap-omzet', [RekapController::class, 'omzet'])->name('rekap-omzet');
    Route::get('/rekap-reject', [RekapController::class, 'reject'])->name('rekap-reject');

    // ---------------------------------------------------------------------
    // Rute Profil Pengguna
    // ---------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
