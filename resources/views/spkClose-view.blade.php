@extends('layouts.app')

@section('content')

{{-- SPK-Close --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja (SPK) - Closed</h3>
</div>

{{-- Alert untuk notifikasi --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
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
                        <th>Kode Order</th>
                        <th>Tanggal Closed</th>
                        <th>Nama Konsumen</th>
                        <th>Nama Order</th>
                        <th>QTY</th>
                        <th>Meter</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($closedSpkList as $spk)
                    <tr>
                        <td>{{ $spk->spk_number }}</td>
                        <td>{{ $spk->closed_date ? \Carbon\Carbon::parse($spk->closed_date)->format('d M Y') : 'N/A' }}</td>
                        <td>{{ $spk->customer_name }}</td>
                        <td>{{ $spk->order_name }}</td>
                        <td>{{ $spk->total_qty }}</td>
                        <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                        <!-- <td class="text-white text-center" style="background: {{ $spk->status == 'Closed' ? 'var(--bs-success)' : 'var(--bs-danger)' }};">{{ $spk->status }}</td> -->
                        <td class="text-white text-center {{ $spk->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">{{ $spk->status }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Belum ada SPK yang ditutup atau ditolak.</td>
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
                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">Showing {{ $closedSpkList->count() }} data</p>
            </div>
            <div class="col-md-6">
                {{-- Pagination bisa ditambahkan di sini nanti jika perlu --}}
            </div>
        </div>
    </div>
    <div class="card-footer"></div>
</div>

@endsection