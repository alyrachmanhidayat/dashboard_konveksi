{{-- pakai layout utama dari app.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- dashboard --}}
<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-primary">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-primary mb-1 fw-bold text-xs"><span style="font-size: 18px;">Total order</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span style="text-align: left;">{{ $totalOrders }}</span></div>
                    </div>
                    <div class="col-auto"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-menu-app-fill fa-2x text-gray-300">
                            <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h2A1.5 1.5 0 0 1 5 1.5v2A1.5 1.5 0 0 1 3.5 5h-2A1.5 1.5 0 0 1 0 3.5zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"></path>
                        </svg></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-danger">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span style="color: var(--bs-danger);font-size: 18px;">Dead line h-8</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $deadlineH8 }}</span></div>
                    </div>
                    <div class="col-auto"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-clock-fill fa-2x text-gray-300">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"></path>
                        </svg></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-warning">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span style="color: var(--bs-warning);font-size: 18px;">Deadline h-10</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $deadlineH10 }}</span></div>
                    </div>
                    <div class="col-auto"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-clock-fill fa-2x text-gray-300">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"></path>
                        </svg></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-success">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span style="color: var(--bs-success);font-size: 18px;">deadline h-12</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $deadlineH12 }}</span></div>
                    </div>
                    <div class="col-auto"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-clock-fill fa-2x text-gray-300">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"></path>
                        </svg></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <p class="text-primary m-0 fw-bold">Dashboard</p>
    </div>
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
        <div class="table-responsive mt-2 table" id="dataTable-1" role="grid" aria-describedby="dataTable_info">
            <table class="table my-0" id="dataTable">
                <thead>
                    <tr>
                        <th>Deadline</th>
                        <th>Nama Konsumen</th>
                        <th>Nama Order</th>
                        <th>QTY</th>
                        <th>Meter</th>
                        <th>Progress</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spkList as $spk)
                    <tr>
                        <td>
                            <div class="{{ $spk->bgColor }} text-white p-2 rounded">
                                <i class="far fa-clock"></i>
                                <span>{{ $spk->formatted_delivery_date }}</span>
                            </div>
                        </td>
                        <td>{{ $spk->customer_name }}</td>
                        <td>{{ $spk->order_name }}</td>
                        <td>{{ $spk->total_qty }}</td>
                        <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                        <td>
                            <div class="progress mb-3 progress-sm">
                                <div class="progress-bar {{ $spk->progressBarColor }}" style="width: {{ $spk->progressPercentage }}%;">
                                    {{ $spk->progressPercentage }}%
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center;"><a class="nav-link" href="{{ route('spk.edit', $spk->id) }}">Opsi</a></td>
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
                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">Showing 1 to 5 of 13</p>
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