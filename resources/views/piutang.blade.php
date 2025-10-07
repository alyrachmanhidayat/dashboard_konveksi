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
                <div class="col-md-6">
                    {{-- Input Pencarian --}}
                    <input type="text" class="form-control search" placeholder="Cari no invoice atau nama konsumen...">
                </div>
                <div class="col-md-6 text-md-end">
                    {{-- Form Filter Tanggal --}}
                    <form method="GET" action="{{ route('piutang.index') }}" class="d-inline-block me-2">
                        <div class="input-group">
                            <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" title="Dari Tanggal">
                            <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" title="Sampai Tanggal">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            <a href="{{ route('piutang.index') }}" class="btn btn-sm btn-outline-secondary" title="Hapus Filter">Clear</a>
                        </div>
                    </form>
                    <br><br>
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
                            <td>
                                <input type="number" class="form-control amount-input" name="amount" placeholder="Rp." required min="1" max="{{$invoice->remaining_amount}}" data-invoice-id="{{$invoice->id}}">
                            </td>
                            <td>
                                <button class="btn btn-success w-100 btn-pay" type="button" data-invoice-id="{{$invoice->id}}" data-invoice-number="{{$invoice->invoice_number}}" data-customer-name="{{$invoice->customer_name}}" data-remaining-amount="{{$invoice->remaining_amount}}">Bayar</button>
                            </td>
                            
                            <!-- Modal konfirmasi pembayaran untuk invoice ini -->
                            <div class="modal fade" id="payModal{{$invoice->id}}" tabindex="-1" aria-labelledby="payModalLabel{{$invoice->id}}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="payModalLabel{{$invoice->id}}">Konfirmasi Pembayaran</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin melakukan pembayaran ini?</p>
                                            <div class="mb-3">
                                                <label class="form-label"><strong>No Invoice:</strong></label>
                                                <p class="form-control-plaintext">{{$invoice->invoice_number}}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Nama Konsumen:</strong></label>
                                                <p class="form-control-plaintext">{{$invoice->customer_name}}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Sisa Tagihan:</strong></label>
                                                <p class="form-control-plaintext" id="modal-remaining-{{$invoice->id}}">Rp. {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Jumlah Pembayaran:</strong></label>
                                                <p class="form-control-plaintext" id="modal-amount-{{$invoice->id}}">Rp. 0</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('piutang.pay', $invoice->id) }}" method="POST" id="payForm{{$invoice->id}}">
                                                @csrf
                                                <input type="hidden" name="amount" id="confirm-amount-{{$invoice->id}}">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check-circle me-1"></i>Ya, Simpan Pembayaran
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        
        // Update modal amount when input changes
        document.querySelectorAll('.amount-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const invoiceId = this.dataset.invoiceId;
                const modalAmount = document.getElementById('modal-amount-' + invoiceId);
                if (modalAmount) {
                    const formattedAmount = new Intl.NumberFormat('id-ID').format(this.value || 0);
                    modalAmount.textContent = 'Rp. ' + formattedAmount;
                }
            });
        });

        // Handle pay button click
        document.querySelectorAll('.btn-pay').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const invoiceId = this.dataset.invoiceId;
                const amountInput = document.querySelector(`.amount-input[data-invoice-id="${invoiceId}"]`);
                
                if (!amountInput.value) {
                    alert('Harap isi jumlah pembayaran terlebih dahulu.');
                    return;
                }
                
                // Validate amount doesn't exceed remaining amount
                const remainingAmount = parseFloat(this.dataset.remainingAmount);
                const paymentAmount = parseFloat(amountInput.value);
                
                if (paymentAmount > remainingAmount) {
                    alert('Jumlah pembayaran tidak boleh melebihi sisa tagihan.');
                    return;
                }

                const modalAmount = document.getElementById('modal-amount-' + invoiceId);
                if (modalAmount) {
                    const formattedAmount = new Intl.NumberFormat('id-ID').format(amountInput.value || 0);
                    modalAmount.textContent = 'Rp. ' + formattedAmount;
                    
                    // Set the hidden input value for the form
                    document.getElementById('confirm-amount-' + invoiceId).value = amountInput.value;
                }

                // Check if Bootstrap is available
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(document.getElementById('payModal' + invoiceId));
                    modal.show();
                } else {
                    // Fallback: submit the form directly if Bootstrap is not available
                    console.error('Bootstrap modal is not available');
                    alert('Bootstrap modal is not available. Form will be submitted directly.');
                    document.getElementById('payForm' + invoiceId).submit();
                }
            });
        });
    });
</script>
@endpush