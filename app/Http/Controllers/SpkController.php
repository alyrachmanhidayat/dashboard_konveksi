<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\SpkSize;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SpkController extends Controller
{
    /**
     * Menampilkan daftar SPK di dashboard.
     */
    public function index()
    {
        // Menghitung data untuk kartu statistik di dashboard
        $totalOrders = Spk::count();
        $today = Carbon::today();
        
        // Count SPKs with delivery date within 8 days from today (0-8 days from now)
        $deadlineH8 = Spk::whereDate('delivery_date', '>=', $today)
                        ->whereDate('delivery_date', '<=', $today->copy()->addDays(8))
                        ->count();
        
        // Count SPKs with delivery date between 9-10 days from today
        $deadlineH10 = Spk::whereDate('delivery_date', '>', $today->copy()->addDays(8))
                          ->whereDate('delivery_date', '<=', $today->copy()->addDays(10))
                          ->count();
        
        // Count SPKs with delivery date between 11-12 days from today
        $deadlineH12 = Spk::whereDate('delivery_date', '>', $today->copy()->addDays(10))
                          ->whereDate('delivery_date', '<=', $today->copy()->addDays(12))
                          ->count();
        
        // Also keeping the old $deadlineH2 variable for any other uses if needed
        $deadlineH2 = Spk::whereDate('delivery_date', '=', $today->copy()->addDays(2))->count();

        // Mengambil data untuk tabel SPK yang sedang berjalan
        $spkList = Spk::where('status', 'In Progress')
            ->orderBy('delivery_date', 'asc')
            ->get();

        return view('layouts.dashboard', compact('totalOrders', 'deadlineH8', 'deadlineH2', 'deadlineH10', 'deadlineH12', 'spkList'));
    }

    /**
     * Tampilkan halaman form untuk membuat SPK baru.
     */
    public function create()
    {
        return view('spk');
    }

    /**
     * Simpan data SPK baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_name' => 'required|string|max:255',
            'delivery_date' => 'required|date',
            'material' => 'required|string|max:255',
            'sizes' => 'required|array|min:1',
            'description' => 'required|string|min:1',
            'design_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Custom validation: Check if at least one size has a value greater than 0
        $hasSizeValue = false;
        if (is_array($request->sizes)) {
            foreach ($request->sizes as $size => $quantity) {
                if (is_numeric($quantity) && $quantity > 0) {
                    $hasSizeValue = true;
                    break;
                }
            }
        }

        if (!$hasSizeValue) {
            return redirect()->back()->with('error', 'Setidaknya satu ukuran harus memiliki jumlah lebih dari 0.')->withInput();
        }

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ($request->hasFile('design_image')) {
                $imagePath = $request->file('design_image')->store('spk_designs', 'public');
            }

            // --- LOGIKA BARU UNTUK NOMOR SPK ---
            $spkNumber = $this->generateSpkNumber();

            $spk = Spk::create([
                'spk_number' => $spkNumber,
                'customer_name' => $request->customer_name,
                'order_name' => $request->order_name,
                'entry_date' => now()->toDateString(),
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
            return redirect()->route('home')->with('success', 'SPK baru berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data SPK. Silakan coba lagi. Pesan Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Helper function untuk membuat nomor SPK unik dan aman dari race condition.
     */
    private function generateSpkNumber()
    {
        $date = now();
        $month = $date->format('m');
        $year = $date->format('Y');

        // Use a lock to prevent race conditions when generating SPK numbers
        return DB::transaction(function () use ($month, $year) {
            // Lock the table to prevent concurrent access
            $lastSpk = Spk::where('spk_number', 'LIKE', "SPK/{$month}/{$year}/%")
                ->orderBy('spk_number', 'DESC')
                ->lockForUpdate()
                ->first();

            $count = 1;
            if ($lastSpk) {
                // Ambil nomor urut dari SPK terakhir dan tambahkan 1
                $lastSpkNumberParts = explode('/', $lastSpk->spk_number);
                $lastCount = end($lastSpkNumberParts);
                $count = intval($lastCount) + 1;
            }

            return "SPK/{$month}/{$year}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
        }, 3); // Retry up to 3 times in case of deadlock
    }


    /**
     * Menampilkan halaman untuk mengedit SPK.
     */
    public function edit(Spk $spk)
    {
        return view('spk', compact('spk'));
    }

    /**
     * Update data SPK yang sudah ada.
     */
    public function update(Request $request, Spk $spk)
    {
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
            $imagePath = $spk->design_image_path;
            if ($request->hasFile('design_image')) {
                // Hapus gambar lama jika ada
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('design_image')->store('spk_designs', 'public');
            }

            $spk->update([
                'customer_name' => $request->customer_name,
                'order_name' => $request->order_name,
                'delivery_date' => $request->delivery_date,
                'material' => $request->material,
                'description' => $request->description,
                'total_qty' => collect($request->sizes)->sum(),
                'design_image_path' => $imagePath,
            ]);

            // Hapus ukuran lama dan masukkan yang baru
            $spk->spkSizes()->delete();
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
            return redirect()->route('spk.edit', $spk->id)->with('success', 'Data SPK berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data SPK.')->withInput();
        }
    }

    /**
     * Update status progress, close, atau reject SPK.
     */
    public function updateStatus(Request $request, Spk $spk)
    {
        $action = $request->input('action');

        if ($action == 'update_progress') {
            $spk->update([
                'is_design_done' => $request->has('is_design_done'),
                'is_print_done' => $request->has('is_print_done'),
                'is_press_done' => $request->has('is_press_done'),
                'is_delivery_done' => $request->has('is_delivery_done'),
                'total_meter' => $request->total_meter,
            ]);
            return redirect()->back()->with('success', 'Progress pengerjaan berhasil diperbarui.');
        }

        if ($action == 'close_order') {
            $spk->update([
                'status' => 'Closed',
                'closed_date' => now()
            ]);
            return redirect()->route('spk-close')->with('success', 'SPK telah ditutup.');
        }

        if ($action == 'reject_order') {
            $spk->update([
                'status' => 'Rejected',
                'closed_date' => now()
            ]);
            return redirect()->route('spk-close')->with('success', 'SPK telah ditolak (rejected).');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * Menampilkan daftar SPK yang sudah ditutup atau ditolak.
     */
    public function spkClosedIndex()
    {
        $closedSpkList = Spk::whereIn('status', ['Closed', 'Rejected'])
            ->orderBy('closed_date', 'desc')
            ->get();

        return view('spk-close', compact('closedSpkList'));
    }

    /**
     * Menyimpan harga per meter pada SPK Closed.
     */
    public function savePrice(Request $request, Spk $spk)
    {
        $request->validate([
            'price_per_meter' => 'required|numeric|min:0',
        ]);

        $spk->update([
            'price_per_meter' => $request->price_per_meter,
        ]);

        return redirect()->back()->with('success', 'Harga per meter berhasil disimpan.');
    }

    /**
     * Menampilkan daftar SPK yang siap dibuatkan invoice.
     */
    public function invoiceIndex()
    {
        $spkList = Spk::with('invoice')
            ->where('status', 'Closed')
            ->whereDoesntHave('invoice')
            ->get();

        return view('invoice', compact('spkList'));
    }

    /**
     * Membuat satu atau lebih invoice dari SPK yang dipilih.
     */
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
                if (is_null($spk->price_per_meter) || is_null($spk->total_meter)) {
                    throw new \Exception("SPK #{$spk->spk_number} belum memiliki harga/meter atau total meter.");
                }
                $totalAmount = $spk->total_meter * $spk->price_per_meter;
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

    /**
     * Helper function untuk membuat nomor invoice unik.
     */
    private function generateInvoiceNumber()
    {
        $date = now();
        $month = $date->format('m');
        $year = $date->format('Y');
        $count = Invoice::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        return "INV/{$month}/{$year}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menampilkan halaman piutang.
     */
    public function piutangIndex()
    {
        $invoices = Invoice::where('is_paid', false)
            ->with('payments')
            ->get()
            ->each(function ($invoice) {
                $paidAmount = $invoice->payments->sum('amount');
                $invoice->remaining_amount = $invoice->total_amount - $paidAmount;
            });

        return view('piutang', compact('invoices'));
    }

    /**
     * Memproses pembayaran piutang.
     */
    public function payPiutang(Request $request, Invoice $invoice)
    {
        $paidAmount = $invoice->payments->sum('amount');
        $remainingAmount = $invoice->total_amount - $paidAmount;

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $remainingAmount,
        ]);

        DB::beginTransaction();
        try {
            $invoice->payments()->create([
                'amount' => $request->amount,
                'payment_date' => now(),
            ]);

            // Cek apakah pembayaran sudah lunas
            if (($paidAmount + $request->amount) >= $invoice->total_amount) {
                $invoice->update(['is_paid' => true]);
            }

            DB::commit();
            return redirect()->route('piutang.index')->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran.');
        }
    }
}
