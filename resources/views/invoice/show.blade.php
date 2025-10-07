@extends('layouts.app')

@section('content')
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Detail Invoice - {{ $invoice->invoice_number }}</h3>
    <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-primary" target="_blank">
        <i class="fas fa-print"></i> Print Invoice
    </a>
</div>

{{-- notif alert --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Invoice</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nomor Invoice</strong></td>
                                <td>{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Konsumen</strong></td>
                                <td>{{ $invoice->customer_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Order</strong></td>
                                <td>{{ $invoice->order_name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Total QTY</strong></td>
                                <td>{{ $invoice->total_qty }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount</strong></td>
                                <td>Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Pembayaran</strong></td>
                                <td>
                                    @if($invoice->is_paid)
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related SPK Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi SPK</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nomor SPK</strong></td>
                                <td>{{ $invoice->spk->spk_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Konsumen</strong></td>
                                <td>{{ $invoice->spk->customer_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Order</strong></td>
                                <td>{{ $invoice->spk->order_name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Tanggal Masuk</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->spk->entry_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Kirim</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->spk->delivery_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Meter</strong></td>
                                <td>{{ $invoice->spk->total_meter }} m</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Harga per Meter</strong></td>
                                <td>Rp. {{ number_format($invoice->spk->price_per_meter, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment History -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
            </div>
            <div class="card-body">
                @if($invoice->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                        <td>Rp. {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total Dibayar</th>
                                    <th>Rp. {{ number_format($invoice->paid_amount ?? $invoice->payments->sum('amount'), 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th>Sisa Tagihan</th>
                                    <th>Rp. {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p>Belum ada pembayaran.</p>
                @endif
            </div>
        </div>
        
        <!-- Print Button -->
        <div class="d-flex justify-content-end">
            <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-print"></i> Print Invoice
            </a>
        </div>
    </div>
</div>
@endsection