@extends('layouts.app')

@section('content')

{{-- Rekap Omzet --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Rekap Omzet</h3>
</div>
<div class="card shadow">
    <div class="card-header"></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 text-nowrap">
                <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable"></div>
                <div class="row">
                    <div class="col"><label class="col-form-label">Filter&nbsp;</label></div>
                    <div class="col"><input class="form-control" type="date" style="margin-left: 9px;"></div>
                    <div class="col">
                        <div style="padding-top: 6px;"><span style="margin-left: 9px;">s/d</span></div>
                    </div>
                    <div class="col"><input class="form-control" type="date" style="margin-left: 16px;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-md-end dataTables_filter" id="dataTable_filter"><label class="form-label"><input type="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search"></label></div>
            </div>
        </div>
        <div class="table-responsive mt-2 table" id="dataTable" role="grid" aria-describedby="dataTable_info">
            <table class="table my-0" id="dataTable">
                <thead>
                    <tr>
                        <th>Nomor Invoice</th>
                        <th>Nama Konsumen</th>
                        <th>QTY</th>
                        <th>Meter</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>JUL/19/0009/2025</td>
                        <td>Mr. A</td>
                        <td>15</td>
                        <td>100</td>
                        <td>Rp. 1.000.000-,</td>
                    </tr>
                    <tr>
                        <td>JUL/22/0009/2025</td>
                        <td>Mr. B</td>
                        <td>25</td>
                        <td>150</td>
                        <td>Rp. 2.500.000-,</td>
                    </tr>
                    <tr>
                        <td>AUG/10/0009/2025</td>
                        <td>Mr. C</td>
                        <td>30</td>
                        <td>110</td>
                        <td>Rp. 900.000-,</td>
                    </tr>
                    <tr>
                        <td>AUG/17/0009/2025</td>
                        <td>Mr. C</td>
                        <td>10</td>
                        <td>85</td>
                        <td>Rp. 1.500.000-,</td>
                    </tr>
                    <tr>
                        <td>SEP/05/0009/2025</td>
                        <td>Mr. D</td>
                        <td>20</td>
                        <td>95</td>
                        <td>Rp. 2.000.000-,</td>
                    </tr>
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
    <div class="card-footer">
        <div style="text-align: right;padding: 19px;"><button class="btn btn-primary" type="button">Print</button></div>
    </div>
</div>
@endsection