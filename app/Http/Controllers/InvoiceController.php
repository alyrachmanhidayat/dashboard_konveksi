<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Spk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
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

    /**
     * Menampilkan view untuk SPK yang sudah ditutup/reject.
     * Digunakan sebagai redirect setelah close/reject order di edit SPK.
     */
    public function viewClosedRedirect()
    {
        $closedSpkList = Spk::whereIn('status', ['Closed', 'Rejected'])
            ->orderBy('closed_date', 'desc')
            ->get();

        return view('spkClose-view', compact('closedSpkList'));
    }
}
