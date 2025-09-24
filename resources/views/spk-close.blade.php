@extends('layouts.app')

@section('content')

{{-- SPK-Close --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja(SPK)-Close</h3>
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
        <div class="table-responsive mt-2 table" id="dataTable" role="grid" aria-describedby="dataTable_info">
            <table class="table my-0" id="dataTable">
                <thead>
                    <tr>
                        <th>Tanggal Closed</th>
                        <th>Nama Konsumen</th>
                        <th>Nama Order</th>
                        <th>QTY</th>
                        <th>Meter</th>
                        <th>Status</th>
                        <th>Harga @meter</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($closedSpkList as $spk)
                    <tr>
                        <td style="text-align: left;">{{ \Carbon\Carbon::parse($spk->closed_date)->format('d M Y') }}</td>
                            <td>{{ $spk->customer_name }}</td>
                            <td>{{ $spk->order_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            <td style="background: {{ $spk->status == 'Closed' ? 'var(--bs-success)' : 'var(--bs-danger)' }};text-align: center;">{{ $spk->status }}</td>
                            <td>
                                <form action="{{ route('spk.save_price', $spk->id) }}" method="POST">
                                    @csrf
                                    <div style="text-align: center;">
                                        <input type="text" class="form-control" name="price_per_meter" placeholder="Rp." value="{{ old('price_per_meter', $spk->price_per_meter) }}">
                                    </div>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <button class="btn {{ $spk->status == 'Closed' ? 'btn-success' : 'btn-danger' }} form-control" type="submit" form="save-form-{{$spk->id}}">Save</button>
                            </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr></tr>
                </tfoot>
            </table>
        </div>
        <div class="row">
            <div class="col-md-6 align-self-center">
                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">Showing 1 to 5 of 10</p>
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