<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Spk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        // Lakukan validasi secara manual agar bisa mengembalikan response JSON saat gagal
        $validator = Validator::make($request->all(), [
            'price_per_meter' => 'required|numeric|min:0',
        ]);

        // Jika validasi gagal, kirim response error JSON
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $spk->update([
                'price_per_meter' => $request->price_per_meter,
            ]);

            // Selalu kembalikan JSON jika sukses
            return response()->json(['success' => true, 'message' => 'Harga per meter berhasil disimpan.']);
        } catch (\Exception $e) {
            // Kembalikan error server jika ada masalah lain
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data ke database.'], 500);
        }
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
     * Display a single invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('spk');

        return view('invoice.show', compact('invoice'));
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
            $createdInvoiceIds = [];
            foreach ($selectedSpks as $spk) {
                if (is_null($spk->price_per_meter) || is_null($spk->total_meter)) {
                    throw new \Exception("SPK #{$spk->spk_number} belum memiliki harga/meter atau total meter.");
                }
                $totalAmount = $spk->total_meter * $spk->price_per_meter;
                $invoiceNumber = $this->generateInvoiceNumber();

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'spk_id' => $spk->id,
                    'customer_name' => $spk->customer_name,
                    'order_name' => $spk->order_name,
                    'total_qty' => $spk->total_qty,
                    'total_amount' => $totalAmount,
                ]);

                $createdInvoiceIds[] = $invoice->id;
            }

            DB::commit();

            // If redirect_to_print is true, redirect to print page
            // Jika user menekan tombol "Publish & Print"
            if ($request->has('redirect_to_print') && !empty($createdInvoiceIds)) {
                // Gabungkan array ID menjadi string dipisahkan koma (e.g., "1,2,3")
                $invoiceIdsString = implode(',', $createdInvoiceIds);

                // Redirect ke rute 'invoice.print' dengan parameter yang sudah digabung
                return redirect()->route('invoice.print', ['invoiceIds' => $invoiceIdsString]);
            }

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
            ->get();

        return view('piutang', compact('invoices'));
    }

    /**
     * Memproses pembayaran piutang.
     */
    public function payPiutang(Request $request, Invoice $invoice)
    {
        $paidAmount = $invoice->payments->sum('amount');
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . ($invoice->total_amount - $paidAmount),
        ]);

        DB::beginTransaction();
        try {
            $payment = $invoice->payments()->create([
                'amount' => $request->amount,
                'payment_date' => now(),
            ]);

            // Update the paid_amount field by recalculating total payments
            $totalPaidAmount = $invoice->payments()->sum('amount');
            $invoice->update(['paid_amount' => $totalPaidAmount]);

            // Cek apakah pembayaran sudah lunas
            if ($totalPaidAmount >= $invoice->total_amount) {
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
     * Menampilkan riwayat invoice yang sudah lunas.
     */
    public function paidHistoryIndex()
    {
        // Ambil semua data invoice dengan status is_paid = true
        $paidInvoices = Invoice::where('is_paid', true)
            ->orderBy('updated_at', 'desc') // Urutkan berdasarkan tanggal lunas terbaru
            ->get();

        // Kirim data ke view
        return view('invoice-paid-history', compact('paidInvoices'));
    }

    /**
     * Menampilkan view untuk SPK yang sudah ditutup/reject.
     * Digunakan sebagai redirect setelah close/reject order di edit SPK.
     */
    public function viewClosedRedirect()
    {
        $closedSpkList = Spk::whereIn('status', ['Closed', 'Rejected'])
            ->whereNull('price_per_meter')
            ->orderBy('closed_date', 'desc')
            ->get();

        return view('spkClose-view', compact('closedSpkList'));
    }

    /**
     * Print single or multiple invoices
     * Method ini menangani semua kasus (satu atau banyak ID).
     */
    public function printInvoice(Request $request, $invoiceIds = null)
    {
        // Pecah string ID berdasarkan koma menjadi array
        $idArray = explode(',', $invoiceIds);

        // Ambil semua invoice berdasarkan array ID yang sudah dipecah
        $invoices = Invoice::with('spk')->whereIn('id', $idArray)->get();

        // Jika tidak ada invoice yang ditemukan, tampilkan 404
        if ($invoices->isEmpty()) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        // Tampilkan view print dengan data invoice
        return view('invoice-print', compact('invoices'));
    }
}
