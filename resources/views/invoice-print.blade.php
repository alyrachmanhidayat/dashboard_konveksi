<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    @vite(['resources/css/invoice-print.css'])


</head>

<body>
    <div class="invoice-container">
        @foreach($invoices as $key => $invoice)
        <div class="invoice-page" style="@if($key > 0) page-break-before: always; @endif">
            <div class="invoice-header">
            </div>

            <table class="header-table">
                <tr>
                    <!-- Left side -->
                    <td class="company-info">
                        <div class="company-name">CV. XXXX</div>
                        <div class="company-address">JL. Mochammad Toha No. XXX</div>
                        <div class="company-number">Nomor : {{ $invoice->invoice_number }}</div>
                    </td>

                    <!-- Right side -->
                    <td class="invoice-info">
                        <div class="invoice-title">INVOICE</div>
                        <table class="invoice-meta">
                            <tr>
                                <td>Tanggal</td>
                                <td>:</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>Pembayaran</td>
                                <td>:</td>
                                <td>CASH</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="table-items">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA BARANG</th>
                        <th>QTY</th>
                        <th>HARGA @METER (Rp)</th>
                        <th>TOTAL (Rp)</th>
                        <th>TERBAYAR (Rp)</th>
                        <th>SISA TAGIHAN (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    <tr>
                        <td>1</td>
                        <td>{{ $invoice->order_name }} ({{ $invoice->spk->spk_number ?? 'N/A' }})</td>
                        <td>{{ $invoice->total_qty }}</td>
                        <td>{{ number_format($invoice->spk->price_per_meter ?? 0, 0, ',', '.') }}</td>
                        <td>{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        <td>{{ number_format($invoice->total_amount - $invoice->remaining_amount, 0, ',', '.') }}</td>
                        <td class="sisa-tagihan text-danger fw-bold" data-sisa-tagihan="{{ $invoice->remaining_amount }}">
                            {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="footer-section">
                <div class="signatures">
                    <div class="signature-box">
                        <p>Pembeli</p>
                        <div class="signature-line">_____________</div>
                    </div>
                    <div class="signature-box">
                        <p>Penjual</p>
                        <div class="signature-line">_____________</div>
                    </div>
                </div>

                <table class="summary-table">
                    <tr>
                        <td>Sub Total</td>
                        <td>:</td>
                        <td>{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>

                    </tr>
                    <tr class="total-row">
                        <td><strong>Total</strong></td>
                        <td>:</td>
                        <td><strong>{{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
        @endforeach
    </div>


</body>

</html>