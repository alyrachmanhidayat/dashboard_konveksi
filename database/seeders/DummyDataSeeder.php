<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Spk;
use App\Models\SpkSize;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kosongkan tabel sebelum mengisi agar tidak ada data duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Spk::truncate();
        SpkSize::truncate();
        Invoice::truncate();
        Payment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $customers = [
            'CV. Maju Jaya', 'PT. Fashionista', 'Distro Gaul', 'Mr. Budi Santoso', 'Ibu Rina Wijaya',
            'Toko Baju Anak', 'Seragam Sekolah Nusantara', 'Butik Mode Modern', 'Konveksi Koko', 
            'Usaha Kecil Mandiri', 'Toko Seragam Budi', 'Konveksi Seragam PT', 'Grosir Baju Murah',
            'CV. Berkah Sukses', 'PT. Modern Tekstil', 'Butik Bella', 'Toko Kain Sejahtera',
            'Konveksi Jaya Abadi', 'Seragam Sekolah Nusantara', 'Butik Baju Muslim'
        ];
        
        $orders = [
            'Kaos Polos Combed 30s', 'Kemeja Seragam Kantor', 'Polo Shirt Event Reuni', 'Hoodie Komunitas', 'Jaket Bomber Custom',
            'Seragam Sekolah SD', 'Tunik Muslimah', 'Kemeja Batik', 'Celana Panjang', 'Jilbab Instan',
            'T-Shirt Promosi', 'Baju Batik Couple', 'Jas Almamater', 'Seragam Guru', 'Rompi Seragam',
            'Kostum Olahraga', 'Kaos Komunitas', 'Baju Keluarga', 'Jaket Komunitas', 'Celana Pendek',
            'Kemeja Formal', 'Blus Wanita', 'Kaos Anak', 'Kemeja Kasual', 'Tunik Kerja'
        ];
        
        $materials = [
            'Cotton Combed 30s', 'Cotton Combed 20s', 'Cotton TC', 'Cotton Carded', 'Katun Rayon',
            'Baby Terry', 'Fleece', 'Baby Doll', 'Diamond', 'Wolly Crepe', 'Satin', 'Sifon',
            'Taslan', 'Parasut', 'Wol', 'Leather', 'Denim', 'Rajut', 'Twill', 'Linon'
        ];
        
        $descriptions = [
            'Bahan lembut dan adem saat dipakai, cocok untuk cuaca tropis.',
            'Kain stretchable dengan tekstur lembut dan menyerap keringat.',
            'Kualitas premium, nyaman dipakai sehari-hari.',
            'Bahan tebal dan kuat, tahan lama dan tidak mudah sobek.',
            'Desain eksklusif dengan warna tahan lama.',
            'Produksi massal untuk kebutuhan seragam kantor.',
            'Kain lembut dan ringan, cocok untuk anak-anak.',
            'Desain eksklusif dengan bordir kualitas tinggi.',
            'Kain adem dan tidak panas saat dipakai.',
            'Bahan premium dengan jahitan rapi dan kuat.',
            'Kain stretchable dengan tekstur lembut dan menyerap keringat.',
            'Model eksklusif dengan desain modern dan kekinian.',
            'Bahan lembut dan tidak mudah melar, tahan lama.',
            'Desain custom sesuai permintaan, kualitas terbaik.',
            'Produksi dengan teknologi tinggi, hasil sangat rapi.',
            'Kain anti kusut dan mudah perawatannya.',
            'Material premium dengan warna tahan lama.',
            'Jahitan rapi dan kuat, tahan lama saat digunakan.',
            'Kain adem dan nyaman saat dipakai.',
            'Produksi dengan kualitas ekspor, hasil sangat memuaskan.'
        ];

        for ($i = 0; $i < 25; $i++) {
            // Buat tanggal acak dalam 3 bulan terakhir
            $entryDate = Carbon::now()->subDays(rand(1, 90));
            $deliveryDate = $entryDate->copy()->addDays(rand(7, 21));

            // Buat data SPK utama
            $spk = Spk::create([
                'spk_number' => 'SPK/' . $entryDate->format('m/Y/') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_name' => $customers[array_rand($customers)],
                'order_name' => $orders[array_rand($orders)],
                'entry_date' => $entryDate,
                'delivery_date' => $deliveryDate,
                'material' => $materials[array_rand($materials)],
                'description' => $descriptions[array_rand($descriptions)],
                'total_qty' => 0, // Akan di-update nanti
                'total_meter' => rand(30, 200),
                'price_per_meter' => rand(40000, 100000),
                'status' => 'In Progress', // Default status
                'is_design_done' => rand(0, 1),
                'is_print_done' => rand(0, 1),
                'is_press_done' => rand(0, 1),
                'is_delivery_done' => rand(0, 1),
                'created_at' => $entryDate,
                'updated_at' => $entryDate,
            ]);

            // Buat data ukuran dan hitung total QTY
            $sizeOptions = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
            $sizes = [];
            foreach ($sizeOptions as $size) {
                if (rand(0, 1) === 1) { // Hanya tambahkan ukuran secara acak
                    $sizes[$size] = rand(5, 40);
                }
            }
            
            // Jika tidak ada ukuran yang ditambahkan, tambahkan minimal satu ukuran
            if (empty($sizes)) {
                $sizes['M'] = rand(10, 30);
            }
            
            $totalQty = 0;
            foreach ($sizes as $size => $qty) {
                SpkSize::create([
                    'spk_id' => $spk->id,
                    'size' => $size,
                    'quantity' => $qty
                ]);
                $totalQty += $qty;
            }
            $spk->total_qty = $totalQty;
            $spk->save();

            // Tentukan status secara acak (Closed, Rejected, atau tetap In Progress)
            $randStatus = rand(1, 10);
            if ($randStatus <= 6) { // 60% kemungkinan 'Closed'
                $closedDate = $deliveryDate->copy()->subDays(rand(1, 3));
                $spk->status = 'Closed';
                $spk->closed_date = $closedDate;
                $spk->updated_at = $closedDate;
                
                // Update progress status saat status menjadi Closed
                $spk->is_design_done = true;
                $spk->is_print_done = true;
                $spk->is_press_done = true;
                $spk->is_delivery_done = true;
                
                $spk->save();

                // PERUBAHAN: Buat invoice hanya untuk separuh SPK yang closed
                if (rand(0, 1) == 1) {
                    $invoice = Invoice::create([
                        'invoice_number' => 'INV/' . $closedDate->format('m/Y/') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                        'spk_id' => $spk->id,
                        'customer_name' => $spk->customer_name,
                        'order_name' => $spk->order_name,
                        'total_qty' => $spk->total_qty,
                        'total_amount' => $spk->total_meter * $spk->price_per_meter,
                        'is_paid' => false, // Default
                        'created_at' => $closedDate,
                        'updated_at' => $closedDate,
                    ]);

                    // Buat pembayaran secara acak
                    $randPayment = rand(1, 3);
                    if ($randPayment == 1) { // Lunas
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'amount' => $invoice->total_amount,
                            'payment_date' => $closedDate->copy()->addDay(),
                        ]);
                        $invoice->paid_amount = $invoice->total_amount;
                        $invoice->is_paid = true;
                        $invoice->save();
                    } elseif ($randPayment == 2) { // Bayar sebagian
                        $partialAmount = $invoice->total_amount / 2;
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'amount' => $partialAmount,
                            'payment_date' => $closedDate->copy()->addDay(),
                        ]);
                        $invoice->paid_amount = $partialAmount;
                        $invoice->save();
                    }
                }
            } elseif ($randStatus <= 8) { // 20% kemungkinan 'Rejected'
                $closedDate = $deliveryDate->copy()->subDays(rand(1, 3));
                $spk->status = 'Rejected';
                $spk->closed_date = $closedDate;
                $spk->updated_at = $closedDate;
                $spk->save();
            }
        }
    }
}
