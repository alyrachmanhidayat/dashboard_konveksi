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

<div>
    <div class="card shadow">
        <div class="card-header py-3">
            <p class="text-primary m-0 fw-bold">Dashboard</p>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table id="spk-table" class="table table-striped">
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
                        @forelse($spkList as $spk)
                        <tr>
                            <td data-order="{{ $spk->formatted_delivery_date }}">
                                <span class="{{ $spk->bgColor }} text-white p-2 rounded">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $spk->formatted_delivery_date }}</span>
                                </span>
                            </td>
                            <td>{{ $spk->customer_name }}</td>
                            <td>{{ $spk->order_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            <td data-order="{{ $spk->progressPercentage }}">
                                <div class="progress mb-3 progress-sm" style="height: 25px; min-width: 100px; max-width: 150px;">
                                    <div class="progress-bar {{ $spk->progressBarColor }}" role="progressbar"
                                        data-bs-toggle="tooltip"
                                        title="{{ $spk->progressPercentage }}%"
                                        style="width: {{ $spk->progressPercentage }}%"
                                        aria-valuenow="{{ $spk->progressPercentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $spk->progressPercentage }}%
                                    </div>
                                </div>
                            </td>
                            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('spk.edit', $spk->id) }}">Opsi</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="7">Belum ada order yang tersedia.</td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
    // Auto-refresh functionality - refresh the page every 30 seconds
    // setInterval(function() {
    //     location.reload();
    // }, 30000); // 30 seconds (30000 milliseconds)

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable with proper column configuration
        $('#spk-table').DataTable({
            "pageLength": 10,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Set progress bar widths
        function setProgressBarWidths() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(function(bar) {
                const width = bar.getAttribute('aria-valuenow');
                bar.style.width = width + '%';
            });
        }

        // Set initial widths when page loads
        setProgressBarWidths();
    });
</script>
@endpush