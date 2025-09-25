<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\SpkSize;
use App\Models\Invoice;
use App\Models\Payment;
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
            'customer_name' => 'required|string|max:255',
            'order_name' => 'required|string|max:255',
            'delivery_date' => 'required|date',
            'material' => 'required|string|max:255',
            'sizes' => 'required|array',
            'description' => 'nullable|string',
            'design_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $date = now();
            $month = $date->format('m');
            $year = $date->format('Y');
            $count = Spk::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count() + 1;
            $spkNumber = "SPK/{$month}/{$year}/" . Str::padLeft($count, 4, '0');

            $imagePath = null;
            if ($request->hasFile('design_image')) {
                $imagePath = $request->file('design_image')->store('design_images', 'public');
            }

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
                'design_image_path' => $imagePath,
            ]);

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
        $totalOrders = Spk::count();
        $today = Carbon::today();

        // Mengambil order yang deadline-nya TEPAT pada H-8, H-10, H-12
        $deadlineH8 = Spk::whereDate('delivery_date', $today->copy()->addDays(8))->count();
        $deadlineH10 = Spk::whereDate('delivery_date', $today->copy()->addDays(10))->count();
        $deadlineH12 = Spk::whereDate('delivery_date', $today->copy()->addDays(12))->count();

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

    public function savePrice(Request $request, Spk $spk)
    {
        $request->validate([
            'price_per_meter' => 'required|numeric|min:0',
        ]);

        $spk->update([
            'price_per_meter' => $request->price_per_meter,
        ]);

        return redirect()->back()->with('success', 'Harga berhasil disimpan.');
    }

    public function invoiceIndex()
    {
        $spkList = Spk::where('status', 'Closed')
            ->whereNotNull('price_per_meter') // Hanya tampilkan yg sudah ada harga/meter
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
                // Kalkulasi nilai invoice hanya jika price_per_meter dan total_meter ada
                $totalAmount = 0;
                if ($spk->total_meter && $spk->price_per_meter) {
                    $totalAmount = $spk->total_meter * $spk->price_per_meter;
                }

                $invoiceNumber = $this->generateInvoiceNumber();

                Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'spk_id' => $spk->id,
                    'customer_name' => $spk->customer_name,
                    'order_name' => $spk->order_name,
                    'total_qty' => $spk->total_qty,
                    'total_amount' => $totalAmount,
                ]);
            }

            DB::commit();

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diterbitkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menerbitkan invoice. ' . $e->getMessage());
        }
    }

    // == LOGIKA BARU UNTUK PIUTANG ==
    public function piutangIndex()
    {
        $invoices = Invoice::with('payments')
            ->where('is_paid', false)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('piutang', compact('invoices'));
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Catat pembayaran baru
            $invoice->payments()->create([
                'amount' => $request->amount,
                'payment_date' => now(),
            ]);

            // 2. Update total yang sudah terbayar di invoice
            $totalPaid = $invoice->payments()->sum('amount');
            $invoice->paid_amount = $totalPaid;

            // 3. Cek apakah sudah lunas
            if ($totalPaid >= $invoice->total_amount) {
                $invoice->is_paid = true;
            }

            $invoice->save();
            DB::commit();

            return redirect()->route('piutang.index')->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran. ' . $e->getMessage());
        }
    }


    private function generateInvoiceNumber()
    {
        $date = now();
        $month = $date->format('m');
        $year = $date->format('Y');
        $count = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;
        return "INV/{$month}/{$year}/" . Str::padLeft($count, 4, '0');
    }
}
