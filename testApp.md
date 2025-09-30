Panduan Testing Aplikasi Sistem Internal Konveksi
Dokumen ini bertujuan untuk memandu proses pengujian aplikasi dari awal hingga akhir. Ikuti langkah-langkah di setiap skenario secara berurutan.

Persiapan Awal
Sebelum memulai, pastikan database Anda sudah terisi dengan data awal yang bersih. Jalankan perintah ini di terminal Anda:

php artisan migrate:fresh --seed

Ini akan mengosongkan database dan mengisinya kembali dengan data dummy yang sudah kita siapkan.

Skenario 1: Alur Sukses (Happy Path - Order Selesai & Lunas)
Skenario ini menguji alur kerja normal dari pesanan masuk hingga pembayaran lunas dan tercatat sebagai omzet.

Langkah 1.1: Membuat Pesanan Baru (SPK)

Aksi: Buka aplikasi, dari sidebar klik menu "SPK".

Input:

Isi semua form: "Nama Konsumen", "Nama Order", "Tanggal Kirim", "Bahan".

Masukkan jumlah (QTY) untuk beberapa ukuran (misal: M=10, L=15).

Hasil yang Diharapkan:

Anda melihat notifikasi "Data SPK berhasil disimpan!".

Anda diarahkan kembali ke halaman form SPK yang kosong.

Buka halaman "Dashboard". Pesanan yang baru Anda buat muncul di tabel paling bawah (karena tanggalnya paling jauh), dengan progress kosong.

Langkah 1.2: Memproses & Menyelesaikan Pesanan

Aksi: Di halaman Dashboard, cari pesanan yang baru Anda buat, lalu klik tombol "Opsi".

Hasil yang Diharapkan: Anda dibawa ke halaman SPK dengan semua data pesanan tersebut sudah terisi (mode edit).

Aksi:

Scroll ke bawah ke bagian "Progress Pengerjaan".

Centang beberapa progress, misalnya "Design" dan "Print". Klik tombol "Update Progress". Halaman akan me-reload.

Setelah itu, klik tombol "Close Order".

Hasil yang Diharapkan:

Pesanan tersebut hilang dari tabel di Dashboard.

Anda diarahkan ke halaman "SPK Closed". Pesanan tersebut sekarang muncul di tabel halaman ini dengan status "Closed".

Langkah 1.3: Mengisi Harga & Menyiapkan Invoice

Aksi: Di halaman "SPK Closed", cari pesanan yang baru saja Anda selesaikan.

Input: Di kolom "Harga @meter", masukkan sebuah angka (misalnya: 65000) lalu klik tombol "Save".

Hasil yang Diharapkan: Halaman me-reload dengan notifikasi sukses.

Aksi: Buka halaman "Invoice" dari sidebar.

Hasil yang Diharapkan: Pesanan yang tadi Anda "Save" harganya sekarang muncul di tabel halaman Invoice, siap untuk dibuatkan tagihan. Kolom "Nilai" sudah terisi hasil perhitungan (QTY x Harga).

Langkah 1.4: Menerbitkan Invoice & Mencatat Piutang

Aksi: Di halaman "Invoice", centang kotak di kolom "Select" pada baris pesanan tersebut.

Aksi: Klik tombol "Terbitkan Invoice".

Hasil yang Diharapkan:

Anda melihat notifikasi "Invoice berhasil diterbitkan!".

Pesanan tersebut hilang dari tabel di halaman Invoice.

Aksi: Buka halaman "Piutang".

Hasil yang Diharapkan: Invoice yang baru Anda terbitkan sekarang muncul di halaman Piutang, dengan "Sisa Tagihan" yang harus dibayar.

Langkah 1.5: Memproses Pembayaran

Aksi: Di halaman "Piutang", cari invoice tersebut.

Input: Masukkan setengah dari total tagihan di kolom "Nominal Bayar", lalu klik "Bayar".

Hasil yang Diharapkan:

Halaman me-reload, notifikasi sukses muncul.

"Nominal Terbayar" ter-update, dan "Sisa Tagihan" berkurang. Invoice masih ada di daftar.

Input: Masukkan sisa tagihan di kolom "Nominal Bayar", lalu klik "Bayar".

Hasil yang Diharapkan: Invoice tersebut hilang dari daftar piutang karena sudah lunas.

Langkah 1.6: Verifikasi Laporan Omzet

Aksi: Buka halaman "Rekap Omzet".

Hasil yang Diharapkan:

Invoice yang baru saja lunas tercatat di tabel.

Angka pada kartu statistik ("Order Selesai", "Omzet", "QTY") bertambah.

Grafik omzet untuk bulan ini juga ikut diperbarui.

Skenario 2: Alur Alternatif (Order Ditolak/Reject)
Skenario ini menguji alur jika sebuah pesanan gagal atau ditolak di tengah jalan.

Langkah 2.1: Membuat & Menolak Pesanan

Aksi: Buat pesanan baru seperti pada Langkah 1.1.

Aksi: Buka pesanan tersebut dari Dashboard dengan mengklik "Opsi".

Aksi: Di halaman edit SPK, langsung scroll ke bawah dan klik tombol "Reject Order".

Hasil yang Diharapkan:

Pesanan hilang dari tabel Dashboard.

Anda diarahkan ke halaman "SPK Closed", dan pesanan tersebut muncul dengan status "Rejected" (berwarna merah).

Langkah 2.2: Verifikasi Laporan Reject

Aksi: Buka halaman "Rekap Reject".

Hasil yang Diharapkan:

Pesanan yang di-reject tadi muncul di tabel.

Angka pada kartu statistik dan grafik di halaman ini diperbarui.

Aksi: Buka halaman "Rekap Omzet".

Hasil yang Diharapkan: Pesanan yang di-reject TIDAK MUNCUL di rekap omzet.
