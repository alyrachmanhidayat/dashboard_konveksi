<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\SpkSize;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SpkController extends Controller
{
    /**
     * Tampilkan halaman form SPK.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('spk');
    }

    /**
     * Simpan data SPK baru ke database.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'customer_name' => 'required|string',
            'order_name' => 'required|string',
            'delivery_date' => 'required|date',
            'material' => 'required|string',
            'sizes' => 'required|array',
            'description' => 'nullable|string',
            // Tambahkan validasi untuk file gambar jika diperlukan
        ]);

        // 2. Transaksi Database
        // Pastikan kedua tabel (spks & spk_sizes) berhasil disimpan
        DB::beginTransaction();
        try {
            // Generate Nomor SPK Otomatis
            // Contoh format: SEP/15/0027/2025 -> bulan/hari/nomor_urut/tahun
            $date = now();
            $month = $date->format('m');
            $year = $date->format('Y');
            $count = Spk::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count() + 1;
            $spkNumber = "SPK/{$month}/{$year}/" . Str::padLeft($count, 4, '0');

            // Simpan data utama ke tabel 'spks'
            $spk = Spk::create([
                'spk_number' => $spkNumber,
                'customer_name' => $request->customer_name,
                'order_name' => $request->order_name,
                'entry_date' => $date->toDateString(),
                'delivery_date' => $request->delivery_date,
                'material' => $request->material,
                'description' => $request->description,
                'total_qty' => collect($request->sizes)->sum(),
                'status' => 'In Progress',
            ]);

            // Simpan data ukuran ke tabel 'spk_sizes'
            foreach ($request->sizes as $size => $quantity) {
                if ($quantity > 0) {
                    SpkSize::create([
                        'spk_id' => $spk->id,
                        'size' => $size,
                        'quantity' => $quantity,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('spk.create')->with('success', 'Data SPK berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data SPK. Silakan coba lagi.')->withInput();
        }
    }


    public function index()
    {
        //count data untuk cards dashboard
        $totalOrders = Spk::count();
        $today = Carbon::today();
        $deadlineH8 = Spk::where('delivery_date', $today->copy()->addDays(8))->count();
        $deadlineH10 = Spk::where('delivery_date', $today->copy()->addDays(10))->count();
        $deadlineH12 = Spk::where('delivery_date', $today->copy()->addDays(12))->count();

        //get data untuk tabel
        $spkList = Spk::where('status', 'In Progress')
            ->orderBy('delivery_date', 'asc')
            ->get();

        return view('layouts.dashboard', compact('totalOrders', 'deadlineH8', 'deadlineH10', 'deadlineH12', 'spkList'));
    }

    public function spkClosedIndex()
    {
        $closedSpkList = Spk::whereIn('status', ['Closed', 'Rejected'])
            ->orderBy('closed_date', 'desc')
            ->get();

        return view('spk-close', compact('closedSpkList'));
    }

    /**
     * Menyimpan harga per meter pada SPK Closed.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Spk  $spk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function savePrice(Request $request, Spk $spk)
    {
        $request->validate([
            'price_per_meter' => 'required|numeric|min:0',
        ]);

        $spk->update([
            'price_per_meter' => $request->price_per_meter,
        ]);

        return redirect()->back()->with('success', 'Harga berhasil disimpan dan SPK ditutup.');
    }

    public function invoiceIndex()
    {
        // Ambil SPK yang sudah closed dan belum punya invoice
        $spkList = Spk::with('invoice')
            ->where('status', 'Closed')
            ->whereDoesntHave('invoice')
            ->get();

        return view('invoice', compact('spkList'));
    }

    public function publishInvoice(Request $request)
    {
        $request->validate([
            'selected_spk_ids' => 'required|array|min:1',
        ]);

        $selectedSpks = Spk::whereIn('id', $request->selected_spk_ids)
            ->where('status', 'Closed')
            ->get();

        if ($selectedSpks->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada SPK yang valid untuk diterbitkan invoice.');
        }

        DB::beginTransaction();
        try {
            foreach ($selectedSpks as $spk) {
                // Hitung total nominal
                $totalAmount = $spk->total_meter * $spk->price_per_meter;

                // Generate Nomor Invoice
                $invoiceNumber = $this->generateInvoiceNumber();

                // Simpan data invoice
                Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'spk_id' => $spk->id,
                    'customer_name' => $spk->customer_name,
                    'order_name' => $spk->order_name,
                    'total_qty' => $spk->total_qty,
                    'total_amount' => $totalAmount,
                ]);

                // Ubah status SPK
                $spk->update(['status' => 'Invoiced']);
            }

            DB::commit();

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diterbitkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menerbitkan invoice. ' . $e->getMessage());
        }
    }

    private function generateInvoiceNumber()
    {
        // Logika untuk generate nomor invoice otomatis
        $date = now();
        $month = $date->format('m');
        $year = $date->format('Y');
        $count = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;
        return "INV/{$month}/{$year}/" . Str::padLeft($count, 4, '0');
    }
}
