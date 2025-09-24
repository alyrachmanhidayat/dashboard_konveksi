@extends('layouts.app')

@section('content')

{{-- Rekap Reject --}}
<div class="container-fluid">
    
    <div class="row">
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card shadow py-2 border-left-primary">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-uppercase text-primary mb-1 fw-bold text-xs"><span style="font-size: 18px;">order Reject</span></div>
                            <div class="text-dark mb-0 fw-bold h5"><span style="text-align: left;">13</span></div>
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
                            <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span style="color: var(--bs-danger);font-size: 18px;">Reject</span></div>
                            <div class="text-dark mb-0 fw-bold h5"><span>Rp. 15.500.000,-</span></div>
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
                            <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span style="color: var(--bs-warning);font-size: 18px;">QTY</span></div>
                            <div class="text-dark mb-0 fw-bold h5"><span>56</span></div>
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
                            <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span style="color: var(--bs-warning);font-size: 18px;">METER</span></div>
                            <div class="text-dark mb-0 fw-bold h5"><span>260</span></div>
                        </div>
                        <div class="col-auto"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-clock-fill fa-2x text-gray-300">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"></path>
                            </svg></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary m-0 fw-bold">Reject</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area"><canvas data-bss-chart="{&quot;type&quot;:&quot;line&quot;,&quot;data&quot;:{&quot;labels&quot;:[&quot;Jan&quot;,&quot;Feb&quot;,&quot;Mar&quot;,&quot;Apr&quot;,&quot;May&quot;,&quot;Jun&quot;,&quot;Jul&quot;,&quot;Aug&quot;],&quot;datasets&quot;:[{&quot;label&quot;:&quot;Earnings&quot;,&quot;fill&quot;:true,&quot;data&quot;:[&quot;0&quot;,&quot;10000&quot;,&quot;5000&quot;,&quot;15000&quot;,&quot;10000&quot;,&quot;20000&quot;,&quot;15000&quot;,&quot;25000&quot;],&quot;backgroundColor&quot;:&quot;rgba(78, 115, 223, 0.05)&quot;,&quot;borderColor&quot;:&quot;rgba(78, 115, 223, 1)&quot;}]},&quot;options&quot;:{&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false,&quot;labels&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;}},&quot;title&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;],&quot;drawOnChartArea&quot;:false},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;fontStyle&quot;:&quot;normal&quot;,&quot;padding&quot;:20}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;]},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;fontStyle&quot;:&quot;normal&quot;,&quot;padding&quot;:20}}]}}}"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <h3 class="text-dark mb-4">Rekap Reject</h3>
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
                            <th>Nilai</th>
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
</div>
    
@endsection