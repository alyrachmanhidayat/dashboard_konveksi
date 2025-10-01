<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\SpkSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SpkController extends Controller
{
    /**
     * Tampilkan halaman form untuk membuat SPK baru.
     */
    public function create()
    {
        $spkNumber = $this->generateSpkNumber();
        return view('spk', compact('spkNumber'));
    }

    /**
     * Simpan data SPK baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_name' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'delivery_date' => 'required|date',
            'material' => 'required|string|max:255',
            'sizes' => 'required|array|min:1',
            'description' => 'required|string|min:1',
            'design_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

            $spkNumber = $this->generateSpkNumber();

            $spk = Spk::create([
                'spk_number' => $spkNumber,
                'customer_name' => $request->customer_name,
                'order_name' => $request->order_name,
                'entry_date' => $request->entry_date,
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
        return DB::transaction(function () {
            $date = now();
            $month = $date->format('m');
            $year = $date->format('Y');

            $lastSpk = Spk::where('spk_number', 'LIKE', "SPK/{$month}/{$year}/%")
                ->orderBy('spk_number', 'DESC')
                ->lockForUpdate()
                ->first();

            $count = 1;
            if ($lastSpk) {
                $lastSpkNumberParts = explode('/', $lastSpk->spk_number);
                $lastCount = end($lastSpkNumberParts);
                $count = intval($lastCount) + 1;
            }

            return "SPK/{$month}/{$year}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
        }, 5);
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
            'entry_date' => 'required|date',
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
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('design_image')->store('spk_designs', 'public');
            }

            $spk->update([
                'customer_name' => $request->customer_name,
                'order_name' => $request->order_name,
                'entry_date' => $request->entry_date,
                'delivery_date' => $request->delivery_date,
                'material' => $request->material,
                'description' => $request->description,
                'total_qty' => collect($request->sizes)->sum(),
                'design_image_path' => $imagePath,
            ]);

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

        if ($action == 'close_order' || $action == 'reject_order') {
            // Validasi: harus selesai print dan jumlah meter wajib diisi
            if (!$spk->is_print_done) {
                return redirect()->back()->with('error', 'Tidak dapat ' . ($action == 'close_order' ? 'menutup' : 'menolak') . ' SPK sebelum proses print selesai.');
            }

            if (is_null($request->total_meter) || $request->total_meter <= 0) {
                return redirect()->back()->with('error', 'Jumlah meter harus diisi dengan nilai lebih dari 0 untuk ' . ($action == 'close_order' ? 'menutup' : 'menolak') . ' SPK.');
            }

            $spk->update([
                'status' => $action == 'close_order' ? 'Closed' : 'Rejected',
                'closed_date' => now(),
                'total_meter' => $request->total_meter, // Ensure total_meter is saved when closing/rejecting
            ]);

            return redirect()->route('spk.closed.view')->with('success', 'SPK telah ' . ($action == 'close_order' ? 'ditutup' : 'ditolak (rejected)') . '.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }
}
