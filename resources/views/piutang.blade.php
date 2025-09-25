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

<div class="card shadow">
    <div class="card-header"></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 text-nowrap">
                <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable"><label class="form-label">Show&nbsp;<select class="d-inline-block form-select form-select-sm">
                            <option value="10" selected="">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>&nbsp;</label></div>
            </div>
            <div class="col-md-6">
                <div class="text-md-end dataTables_filter" id="dataTable_filter"><label class="form-label"><input type="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search"></label></div>
            </div>
        </div>
        <div class="table-responsive mt-2 table" id="dataTable" role="grid" aria-describedby="dataTable_info">
            <table class="table my-0" id="dataTable">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Nama Konsumen</th>
                        <th>Nominal</th>
                        <th>Nominal terbayar</th>
                        <th>Nominal Bayar</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                    <tr>
                        <form action="{{ route('piutang.pay', $invoice->id) }}" method="POST">
                            @csrf
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer_name }}</td>
                            <td>Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp. {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td>
                            <td>
                                <input type="number" class="form-control" name="amount" placeholder="Rp." required min="1" max="{{$invoice->remaining_amount}}">
                            </td>
                            <td>
                                <div style="text-align: center;"><button class="btn btn-success form-control" type="submit">Bayar</button></div>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada piutang</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr></tr>
                </tfoot>
            </table>
        </div>
        <div class="row">
            <div class="col-md-6 align-self-center">
                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">Showing 1 to 5 of 20</p>
            </div>
            <div class="col-md-6">
                <nav class="d-lg-flex justify-content-lg-end dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        <li class="page-item disabled"><a class="page-link" aria-label="Previous" href="#"><span aria-hidden="true">«</span></a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" aria-label="Next" href="#"><span aria-hidden="true">»</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="card-footer"></div>
</div>

@endsection