@extends('layouts.app')

@section('content')

{{-- Piutang --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Piutang</h3>
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
                        <th>No Invoice</th>
                        <th>Nama Konsumen</th>
                        <th>Nominal</th>
                        <th>Nominal tebayar</th>
                        <th>Nominal Bayar</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>AUG/01/0009/2025</td>
                        <td>Mr. A</td>
                        <td>Rp. 1.500.000,-</td>
                        <td>
                            <div style="text-align: center;"><input type="text" class="form-control" placeholder="Rp."></div>
                        </td>
                        <td>Rp. 1.000.000,-</td>
                        
                        <td>
                            <div style="text-align: center;"><button class="btn btn-success form-control" type="button">Bayar</button></div>
                        </td>
                        
                    </tr>
                    <tr>
                        <td>AUG/09/0019/2025</td>
                        <td>Mr. B</td>
                        <td>Rp. 3.500.000,-</td>
                        <td>
                            <div style="text-align: center;"><input type="text" class="form-control" placeholder="Rp."></div>
                        </td>
                        
                        <td>Rp. 1.000.000,-</td>
                        
                        <td>
                            <div style="text-align: center;"><button class="btn btn-success form-control" type="button">Bayar</button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>AUG/18/0022/2025</td>
                        <td>Mr. B</td>
                        <td>Rp. 2.000.000,-</td>
                        <td>
                            <div style="text-align: center;"><input type="text" class="form-control" placeholder="Rp."></div>
                        </td>
                        <td>Rp. 1.000.000,-</td>
                        <td>
                            <div style="text-align: center;"><button class="btn btn-success form-control" type="button">Bayar</button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>AUG/27/0030/2025</td>
                        <td>Mr. A</td>
                        <td>Rp. 1.500.000,-</td>
                        <td>
                            <div style="text-align: center;"><input type="text" class="form-control" placeholder="Rp."></div>
                        </td>
                        <td>Rp. 1.000.000,-</td>
                        <td>
                            <div style="text-align: center;"><button class="btn btn-success form-control" type="button">Bayar</button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>SEP/01/0002/2025</td>
                        <td>Mr. C</td>
                        <td>Rp. 1.000.000,-</td>
                        <td>
                            <div style="text-align: center;"><input type="text" class="form-control" placeholder="Rp."></div>
                        </td>
                        <td>Rp. 1.000.000,-</td>
                        <td>
                            <div style="text-align: center;"><button class="btn btn-success form-control" type="button">Bayar</button></div>
                        </td>
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
    <div class="card-footer"></div>
</div>

@endsection