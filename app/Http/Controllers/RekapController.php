<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Spk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RekapController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->is_admin) {
                abort(403, 'Unauthorized access. Admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Menampilkan halaman rekap omzet dengan data dinamis.
     */
    public function omzet(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Data untuk Cards (berdasarkan rentang tanggal yang dipilih)
        // Omzet dihitung dari invoice yang sudah lunas (is_paid = true)
        $query = Invoice::where('is_paid', true)->whereBetween('updated_at', [$startDate, $endDate]);

        $orderSelesai = $query->count();
        $totalOmzet = $query->sum('total_amount');

        // Untuk QTY dan Meter, kita perlu join ke SPK
        $totalQty = $query->sum('total_qty');
        $totalMeter = Spk::whereIn('id', $query->pluck('spk_id'))->sum('total_meter');

        // Data untuk Tabel (difilter berdasarkan tanggal) with eager loading to fix N+1 query
        $invoices = Invoice::with('spk') // Eager load the SPK relationship
            ->where('is_paid', true)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Data untuk Chart (omzet 12 bulan terakhir) - improved to show all 12 months
        $omzetChartData = Invoice::select(
            DB::raw('SUM(total_amount) as total'),
            DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as month")
        )
            ->where('is_paid', true)
            ->where('updated_at', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare chart data to include all 12 months with zero values for months without data
        $months = collect();
        $values = collect();

        // Generate last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $months->push($month);

            $data = $omzetChartData->firstWhere('month', $month);
            $values->push($data ? (float)$data->total : 0);
        }

        $chartLabels = $months;
        $chartValues = $values;

        return view('rekap-omzet', compact(
            'orderSelesai',
            'totalOmzet',
            'totalQty',
            'totalMeter',
            'invoices',
            'startDate',
            'endDate',
            'chartLabels',
            'chartValues'
        ));
    }

    /**
     * Menampilkan halaman rekap reject dengan data dinamis.
     */
    public function reject(Request $request)
    {
        // Logika untuk filter tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // --- LOGIKA BARU UNTUK CARDS (Berdasarkan bulan ini) ---
        $currentMonth = Carbon::now();
        $orderReject = Spk::where('status', 'Rejected')
            ->whereYear('closed_date', $currentMonth->year)
            ->whereMonth('closed_date', $currentMonth->month)
            ->count();
        $totalQtyReject = Spk::where('status', 'Rejected')
            ->whereYear('closed_date', $currentMonth->year)
            ->whereMonth('closed_date', $currentMonth->month)
            ->sum('total_qty');
        $totalMeterReject = Spk::where('status', 'Rejected')
            ->whereYear('closed_date', $currentMonth->year)
            ->whereMonth('closed_date', $currentMonth->month)
            ->sum('total_meter');

        // Untuk nominal, kita perlu mengkalkulasi secara manual
        $rejectedSpksThisMonth = Spk::where('status', 'Rejected')
            ->whereYear('closed_date', $currentMonth->year)
            ->whereMonth('closed_date', $currentMonth->month)
            ->whereNotNull('price_per_meter') // Hanya hitung yang sudah ada harganya
            ->get();
        $totalNominalReject = $rejectedSpksThisMonth->sum(function ($spk) {
            return $spk->total_meter * $spk->price_per_meter;
        });

        // --- DATA UNTUK TABEL (Berdasarkan filter tanggal) ---
        $rejectedSpks = Spk::where('status', 'Rejected')
            ->whereBetween('closed_date', [$startDate, $endDate])
            ->get();

        // --- LOGIKA BARU UNTUK CHART (12 bulan terakhir) ---
        $chartData = Spk::where('status', 'Rejected')
            ->selectRaw('YEAR(closed_date) as year, MONTH(closed_date) as month, SUM(total_meter * price_per_meter) as total_nominal')
            ->where('closed_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->whereNotNull('price_per_meter')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = [];
        $chartValues = [];
        $months = collect([]);

        // Buat kerangka 12 bulan ke belakang dari sekarang
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'year' => $date->year,
                'month' => $date->month,
                'label' => $date->format('Y-m'), // Format label menjadi 'YYYY-MM'
            ]);
        }

        // Isi data nominal reject ke dalam kerangka bulan
        foreach ($months as $month) {
            $chartLabels[] = $month['label'];
            $dataPoint = $chartData->first(function ($item) use ($month) {
                return $item->year == $month['year'] && $item->month == $month['month'];
            });
            $chartValues[] = $dataPoint ? $dataPoint->total_nominal : 0;
        }

        return view('rekap-reject', compact(
            'orderReject',
            'totalNominalReject',
            'totalQtyReject',
            'totalMeterReject',
            'rejectedSpks',
            'startDate',
            'endDate',
            'chartLabels',
            'chartValues'
        ));
    }
}
