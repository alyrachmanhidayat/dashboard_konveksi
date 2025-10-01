<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan daftar SPK di dashboard.
     */
    public function index()
    {
        // Menghitung data untuk kartu statistik di dashboard
        // Total Order card should only display ongoing orders (In Progress)
        $totalOrders = Spk::where('status', 'In Progress')->count();
        $today = Carbon::today();

        // Count SPKs with delivery date within 8 days from today (0-8 days from now)
        $deadlineH8 = Spk::where('status', 'In Progress')
            ->whereDate('delivery_date', '>=', $today)
            ->whereDate('delivery_date', '<=', $today->copy()->addDays(8))
            ->count();

        // Count SPKs with delivery date between 9-10 days from today
        $deadlineH10 = Spk::where('status', 'In Progress')
            ->whereDate('delivery_date', '>', $today->copy()->addDays(8))
            ->whereDate('delivery_date', '<=', $today->copy()->addDays(10))
            ->count();

        // Count SPKs with delivery date between 11-12 days from today
        $deadlineH12 = Spk::where('status', 'In Progress')
            ->whereDate('delivery_date', '>', $today->copy()->addDays(10))
            ->whereDate('delivery_date', '<=', $today->copy()->addDays(12))
            ->count();

        $deadlineH2 = Spk::where('status', 'In Progress')
            ->whereDate('delivery_date', '=', $today->copy()->addDays(2))->count();

        // Mengambil data untuk tabel SPK yang sedang berjalan
        $spkList = Spk::where('status', 'In Progress')
            ->orderBy('delivery_date', 'asc')
            ->get();

        return view('layouts.dashboard', compact('totalOrders', 'deadlineH8', 'deadlineH2', 'deadlineH10', 'deadlineH12', 'spkList'));
    }
}
