<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $spk->spk_number }}</title>
    @vite(['resources/css/print.css'])
</head>

<body>
    <div class="container">
        <div class="periode-title">
            PERIODE {{ \Carbon\Carbon::parse($spk->entry_date)->format('Y') }}
        </div>
        <table class="header-table" border="1">
            <thead>
                <tr>
                    <td class="no-spk" colspan="5" style="text-align: right;">NO : {{ substr($spk->spk_number, -4) }}</td>
                    <td class="info-parent" colspan="2">NAMA FILE</td>
                    <td class="info-child" colspan="2">{{ $spk->order_name }}</td>
                </tr>
                <tr>
                    <td class="spk-title" colspan="5" rowspan="3">
                        <h1>SPK</h1>
                    </td>
                </tr>
                <tr>
                    <td class="info-parent" colspan="2">NAMA PEMESAN</td>
                    <td class="info-child" colspan="2">{{ $spk->customer_name }}</td>
                </tr>
                <tr>
                    <td class="info-parent" colspan="2">TANGGAL MASUK</td>
                    <td class="info-child" colspan="2">{{ \Carbon\Carbon::parse($spk->entry_date)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="image-header" colspan="5" style="text-align: center;">{{ $spk->customer_name }}</td>
                    <td class="info-parent" colspan="2">TANGGAL KIRIM</td>
                    <td class="info-child" colspan="2">{{ \Carbon\Carbon::parse($spk->delivery_date)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td colspan="5" rowspan="9">
                        <div class="image-section">
                            @if($spk->design_image_path)
                            <img src="{{ asset('storage/' . $spk->design_image_path) }}" alt="Design">
                            @else
                            <div class="no-image">Tidak Ada Gambar</div>
                            @endif
                        </div>
                    </td>
                    <td class="info-parent" colspan="2">BAHAN</td>
                    <td class="info-child" colspan="2">{{ $spk->material }}</td>
                </tr>

                <div class="size-section">
                    <tr>
                        <td colspan="4" style="text-align: center;">SIZE</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:0;">
                            <table class="size-table" width="100%">
                                <tr>
                                    @foreach(['S','M','L','XL'] as $size)
                                    <td class="size-table-td">
                                        <div class="size-label">{{ $size }}</div>
                                        <div class="size-qty">
                                            {{ $spk->spkSizes->where('size',$size)->first()->quantity ?? 0 }}
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach(['2XL','3XL', '4XL', '5XL'] as $size)
                                    <td class="size-table-td">
                                        <div class="size-label">{{ $size }}</div>
                                        <div class="size-qty">
                                            {{ $spk->spkSizes->where('size',$size)->first()->quantity ?? 0 }}
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>
                </div>

                <tr>
                    <td class="total-order-section" colspan="2">TOTAL ORDER</td>
                    <td colspan="2" style="text-align: center; vertical-align: middle;">{{ $spk->total_qty }}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="keterangan-section">
                            <div class="title">KETERANGAN</div>
                            <div class="content">{!! nl2br(e($spk->description)) !!}</div>
                        </div>
                    </td>
                </tr>
            </thead>
        </table>

        <tbody>
            <table class="progress-table">
                <tr>
                    <td colspan="6" class="baca-dan-pahami">
                        BACA DAN PAHAMI SEBELUM BEKERJA
                    </td>
                </tr>
                <tr>
                    <td class="progress-head">CUTTING</td>
                    <td class="progress-head">PRINT</td>
                    <td class="progress-head">PRESS</td>
                    <td class="progress-head">JAHIT</td>
                    <td class="progress-head">PACKING</td>
                    <td class="progress-head">KIRIM</td>
                </tr>
                <tr>
                    <td class="progress-head-isi"></td>
                    <td class="progress-head-isi"></td>
                    <td class="progress-head-isi"></td>
                    <td class="progress-head-isi"></td>
                    <td class="progress-head-isi"></td>
                    <td class="progress-head-isi"></td>
                </tr>
            </table>
        </tbody>
        <div class="container-footer">
            <tfoot>
                <tr>
                    <td>
                        NB:
                        <ul>
                            <li>Selesai POTONGAN harus ditandai label SIZE </li>
                            <li>Selesai BAHAN di PRESS harus di rapihkan SUSUNAN pesanan Label SIZE</li>
                            <li>Selesai JAHIT dan PACKING, PRODUKSI harus di rapihkan dan CEK ULANG</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table class="footer-table">
                            <tr>
                                <td colspan="3" class="mengetahui">
                                    <p>Mengetahui:</p>
                                    <p class="admin">ADMIN</p>
                                    <div class="signature-line"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="footer-no">NO : {{ substr($spk->spk_number, -4) }}</td>
                                <td></td>
                                <td class="perhatian">Mohon diperhatikan TELITI dan CEK ULANG</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.print(); // Otomatis buka dialog print saat halaman selesai dimuat
        });
    </script>

</body>

</html>