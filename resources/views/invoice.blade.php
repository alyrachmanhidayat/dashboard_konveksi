@extends('layouts.app')

@section('content')
{{-- invoice --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Invoice</h3>
</div>
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
        {{-- resources/views/invoice.blade.php --}}

<form action="{{ route('invoice.publish') }}" method="POST">
    @csrf
    <div class="card shadow">
        <div class="card-header"></div>
        <div class="card-body">
            <div class="table-responsive mt-2 table" id="dataTable" role="grid" aria-describedby="dataTable_info">
                <table class="table my-0" id="dataTable">
                    <thead>
                        <tr>
                            <th>No Order</th>
                            <th>Nama Konsumen</th>
                            <th>Nama Order</th>
                            <th>QTY</th>
                            <th>Nilai</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($spkList as $spk)
                            <tr>
                                <td>{{ $spk->spk_number }}</td>
                                <td>{{ $spk->customer_name }}</td>
                                <td>{{ $spk->order_name }}</td>
                                <td>{{ $spk->total_qty }}</td>
                                <td>Rp. {{ number_format($spk->total_meter * $spk->price_per_meter, 2, ',', '.') }},-</td>
                                <td>
                                    <div style="text-align: center;">
                                        <input type="checkbox" class="form-check-input" name="selected_spk_ids[]" value="{{ $spk->id }}">
                                    </div>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada SPK yang siap diterbitkan invoice.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        <div class="card-footer">
            <div style="text-align: right;padding: 19px;">
                <button class="btn btn-success" type="submit">Terbitkan Invoice</button>
            </div>
        </div>
    </div>
</form>
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
</div>
@endsection