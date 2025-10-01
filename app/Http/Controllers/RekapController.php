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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $baseQuery = Spk::where('status', 'Rejected');

        // Data untuk Cards (berdasarkan rentang tanggal)
        $query = (clone $baseQuery)->whereBetween('closed_date', [$startDate, $endDate]);

        $orderReject = $query->count();
        $totalQtyReject = $query->sum('total_qty');
        $totalMeterReject = $query->sum('total_meter');
        // Nominal reject dihitung dari spk yang memiliki harga
        $totalNominalReject = (clone $query)->whereNotNull('price_per_meter')->get()->sum(function ($spk) {
            return $spk->total_meter * $spk->price_per_meter;
        });

        // Data untuk Tabel
        $rejectedSpks = (clone $baseQuery)
            ->whereBetween('closed_date', [$startDate, $endDate])
            ->orderBy('closed_date', 'desc')
            ->get();

        // Data untuk Chart (reject 12 bulan terakhir)
        $rejectChartData = (clone $baseQuery)->select(
            DB::raw('COUNT(id) as total_count'),
            DB::raw("DATE_FORMAT(closed_date, '%Y-%m') as month")
        )
            ->where('closed_date', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = $rejectChartData->pluck('month');
        $chartValues = $rejectChartData->pluck('total_count');

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
