@extends('layouts.app')

@section('content')

{{-- Piutang --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Piutang</h3>
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

<div id="piutang-table-list">
    <div class="card shadow">
        <div class="card-header"></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    {{-- Input Pencarian --}}
                    <input type="text" class="form-control search" placeholder="Cari no invoice atau nama konsumen...">
                </div>
                <div class="col-md-8 text-md-end">
                    {{-- Tombol Sort --}}
                    <span class="me-2">Urutkan berdasarkan:</span>
                    <button class="btn btn-sm btn-outline-primary sort" data-sort="konsumen">Konsumen</button>
                    <button class="btn btn-sm btn-outline-danger sort" data-sort="sisa-tagihan">Sisa Tagihan</button>
                </div>
            </div>
            <div class="table-responsive mt-2">
                <table class="table my-0">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>Nama Konsumen</th>
                            <th>Nominal Tagihan</th>
                            <th>Sisa Tagihan</th>
                            <th>Nominal Bayar</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    {{-- Beri class="list" pada tbody --}}
                    <tbody class="list">
                        @forelse ($invoices as $invoice)
                        <tr>
                            {{-- Kita tidak bisa menaruh <form> di dalam <tr> jika <tbody> adalah target List.js --}}
                            <!--  Jadi, setiap tombol bayar akan memiliki form-nya sendiri -->
                            <td class="no-invoice">
                                <a href="{{ route('invoice.print', ['invoiceIds' => $invoice->id]) }}" target="_blank">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="konsumen">{{ $invoice->customer_name }}</td>
                            <td>Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            <td class="sisa-tagihan text-danger fw-bold" data-sisa-tagihan="{{ $invoice->remaining_amount }}">
                                Rp. {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                            </td>
                            <form action="{{ route('piutang.pay', $invoice->id) }}" method="POST">
                                @csrf
                                <td>
                                    <input type="number" class="form-control" name="amount" placeholder="Rp." required min="1" max="{{$invoice->remaining_amount}}">
                                </td>
                                <td>
                                    <button class="btn btn-success w-100" type="submit">Bayar</button>
                                </td>
                            </form>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data piutang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Kontainer untuk pagination List.js --}}
            <ul class="pagination mt-3"></ul>
        </div>
        <div class="card-footer"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Opsi untuk List.js
        var options = {
            valueNames: [
                'no-invoice',
                'konsumen',
                {
                    name: 'sisa-tagihan',
                    attr: 'data-sisa-tagihan'
                }
            ],
            page: 10,
            pagination: {
                innerWindow: 1,
                outerWindow: 1,
                paginationClass: "pagination", // Nama class untuk <ul>
            },
        };

        // Inisialisasi List.js
        var piutangList = new List('piutang-table-list', options);

        // Tambahkan class Bootstrap ke pagination yang digenerate List.js
        piutangList.on('updated', function(list) {
            const paginationItems = document.querySelectorAll('.pagination li');
            paginationItems.forEach(function(item) {
                item.classList.add('page-item');
                const link = item.querySelector('a');
                if (link) {
                    link.classList.add('page-link');
                }
            });
            const activeItem = document.querySelector('.pagination li.active');
            if (activeItem) {
                activeItem.classList.add('active');
            }
        });
    });
</script>
@endpush