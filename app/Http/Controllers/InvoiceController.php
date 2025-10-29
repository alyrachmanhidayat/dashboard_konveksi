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
     * Menyimpan harga per meter dan/atau harga per piece pada SPK Closed.
     */
    public function savePrice(Request $request, Spk $spk)
    {
        // Lakukan validasi secara manual agar bisa mengembalikan response JSON saat gagal
        $validator = Validator::make($request->all(), [
            'price_per_meter' => 'nullable|numeric|min:0',
            'harga_per_piece' => 'nullable|numeric|min:0',
        ]);

        // Jika validasi gagal, kirim response error JSON
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Validasi bahwa setidaknya satu harga harus diisi
        if (empty($request->price_per_meter) && empty($request->harga_per_piece)) {
            return response()->json(['success' => false, 'message' => 'Setidaknya satu harga (per meter atau per piece) harus diisi.'], 422);
        }

        try {
            $updateData = [];
            if (!empty($request->price_per_meter)) {
                $updateData['price_per_meter'] = $request->price_per_meter;
            }
            if (!empty($request->harga_per_piece)) {
                $updateData['harga_per_piece'] = $request->harga_per_piece;
            }

            $spk->update($updateData);

            // Selalu kembalikan JSON jika sukses
            return response()->json(['success' => true, 'message' => 'Harga berhasil disimpan.']);
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
            'selected_spk_ids' => 'required|integer', // Changed from array to single integer
        ]);

        $selectedSpk = Spk::where('id', $request->selected_spk_ids)
            ->where('status', 'Closed')
            ->first();

        if (!$selectedSpk) {
            return redirect()->back()->with('error', 'Tidak ada SPK yang valid untuk diterbitkan invoice.');
        }

        DB::beginTransaction();
        try {
            $createdInvoiceIds = [];
            
            // Check if only price_per_meter is present
            if (!is_null($selectedSpk->price_per_meter) && is_null($selectedSpk->harga_per_piece)) {
                if (is_null($selectedSpk->total_meter)) {
                    throw new \Exception("SPK #{$selectedSpk->spk_number} belum memiliki total meter.");
                }
                
                $totalAmount = $selectedSpk->total_meter * $selectedSpk->price_per_meter;
                $invoiceNumber = $this->generateInvoiceNumber('MTR'); // Use INV/MTR format

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'spk_id' => $selectedSpk->id,
                    'customer_name' => $selectedSpk->customer_name,
                    'order_name' => $selectedSpk->order_name . ' (MTR)',
                    'total_qty' => $selectedSpk->total_qty,
                    'total_amount' => $totalAmount,
                    'total_meter' => $selectedSpk->total_meter,
                ]);

                $createdInvoiceIds[] = $invoice->id;
            }
            // Check if only harga_per_piece is present
            elseif (!is_null($selectedSpk->harga_per_piece) && is_null($selectedSpk->price_per_meter)) {
                $totalAmount = $selectedSpk->total_qty * $selectedSpk->harga_per_piece;
                $invoiceNumber = $this->generateInvoiceNumber('QTY'); // Use INV/QTY format

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'spk_id' => $selectedSpk->id,
                    'customer_name' => $selectedSpk->customer_name,
                    'order_name' => $selectedSpk->order_name . ' (QTY)',
                    'total_qty' => $selectedSpk->total_qty,
                    'total_amount' => $totalAmount,
                    'total_meter' => $selectedSpk->total_meter,
                ]);

                $createdInvoiceIds[] = $invoice->id;
            }
            // Check if both prices are present
            elseif (!is_null($selectedSpk->price_per_meter) && !is_null($selectedSpk->harga_per_piece)) {
                if (is_null($selectedSpk->total_meter)) {
                    throw new \Exception("SPK #{$selectedSpk->spk_number} belum memiliki total meter.");
                }
                
                // Create MTR invoice
                $totalAmountMtr = $selectedSpk->total_meter * $selectedSpk->price_per_meter;
                $invoiceNumberMtr = $this->generateInvoiceNumber('MTR'); // Use INV/MTR format
                
                $invoiceMtr = Invoice::create([
                    'invoice_number' => $invoiceNumberMtr,
                    'spk_id' => $selectedSpk->id,
                    'customer_name' => $selectedSpk->customer_name,
                    'order_name' => $selectedSpk->order_name . ' (MTR)',
                    'total_qty' => $selectedSpk->total_qty,
                    'total_amount' => $totalAmountMtr,
                    'total_meter' => $selectedSpk->total_meter,
                ]);

                $createdInvoiceIds[] = $invoiceMtr->id;
                
                // Create QTY invoice
                $totalAmountQty = $selectedSpk->total_qty * $selectedSpk->harga_per_piece;
                $invoiceNumberQty = $this->generateInvoiceNumber('QTY'); // Use INV/QTY format
                
                $invoiceQty = Invoice::create([
                    'invoice_number' => $invoiceNumberQty,
                    'spk_id' => $selectedSpk->id,
                    'customer_name' => $selectedSpk->customer_name,
                    'order_name' => $selectedSpk->order_name . ' (QTY)',
                    'total_qty' => $selectedSpk->total_qty,
                    'total_amount' => $totalAmountQty,
                    'total_meter' => $selectedSpk->total_meter,
                ]);

                $createdInvoiceIds[] = $invoiceQty->id;
            }
            else {
                throw new \Exception("SPK #{$selectedSpk->spk_number} tidak memiliki harga yang valid.");
            }

            DB::commit();

            // If redirect_to_print is true, redirect to print page
            // Jika user menekan tombol "Publish & Print"
            if ($request->has('redirect_to_print') && !empty($createdInvoiceIds)) {
                $invoiceIdsString = implode(',', $createdInvoiceIds);

                // Return JSON response with redirect URL for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'redirect' => route('invoice.print', ['invoiceIds' => $invoiceIdsString]),
                        'message' => count($createdInvoiceIds) . ' invoice berhasil diterbitkan!'
                    ]);
                }
                
                // Regular redirect for non-AJAX requests
                return redirect()->route('invoice.print', ['invoiceIds' => $invoiceIdsString]);
            }

            // Return JSON for AJAX requests or redirect for regular requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => count($createdInvoiceIds) . ' invoice berhasil diterbitkan!',
                    'invoice_ids' => $createdInvoiceIds // Add this line to return the array of invoice IDs
                ]);
            }
            
            return redirect()->route('invoice.index')->with('success', count($createdInvoiceIds) . ' invoice berhasil diterbitkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Return JSON for AJAX requests or redirect for regular requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menerbitkan invoice. ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal menerbitkan invoice. ' . $e->getMessage());
        }
    }

    /**
     * Helper function untuk membuat nomor invoice unik.
     * @param string $type - 'MTR' for meter-based pricing or 'QTY' for piece-based pricing
     */
    private function generateInvoiceNumber($type = 'MTR')
    {
        return DB::transaction(function () use ($type) {
            $date = now();
            $month = $date->format('m');
            $year = $date->format('Y');
            
            // Count invoices with the same type in current month/year
            $pattern = "INV/{$type}/{$month}/{$year}/%";
            $count = Invoice::where('invoice_number', 'LIKE', $pattern)
                ->lockForUpdate()
                ->count() + 1;
            
            return "INV/{$type}/{$month}/{$year}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
        }, 5);
    }

    /**
     * Menampilkan halaman piutang.
     */
    public function piutangIndex()
    {
        $query = Invoice::where('is_paid', false)->with('payments');
        
        // Handle date filtering
        $startDate = request('start_date');
        $endDate = request('end_date');
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $invoices = $query->get();

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
            
            // Preserve the date filter when redirecting back
            $queryParams = [];
            if ($request->has('start_date')) {
                $queryParams['start_date'] = $request->get('start_date');
            }
            if ($request->has('end_date')) {
                $queryParams['end_date'] = $request->get('end_date');
            }
            
            $redirectUrl = route('piutang.index', $queryParams);
            return redirect($redirectUrl)->with('success', 'Pembayaran berhasil dicatat.');
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
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNull('price_per_meter')
                      ->orWhere('price_per_meter', 0);
                })
                ->where(function($q) {
                    $q->whereNull('harga_per_piece')
                      ->orWhere('harga_per_piece', 0);
                });
            })
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
