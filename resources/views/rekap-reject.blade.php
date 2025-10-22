@extends('layouts.app')

@section('content')

{{-- Rekap Reject --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Rekap Reject</h3>
</div>

{{-- Cards (Menampilkan data bulan ini) --}}
<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-danger">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-danger mb-1 fw-bold text-xs"><span>Order Reject (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $orderReject }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-secondary">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-secondary mb-1 fw-bold text-xs"><span>Nominal Kerugian (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>Rp {{ number_format($totalNominalReject, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-dark">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-dark mb-1 fw-bold text-xs"><span>QTY (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $totalQtyReject }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-warning">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span>Meter (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $totalMeterReject }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-ruler fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart (Menampilkan nominal kerugian per bulan) --}}
<div class="row">
    <div class="col">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="text-primary m-0 fw-bold">Grafik Nominal Kerugian (12 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="rejectChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="text-primary fw-bold m-0">Detail Reject Keseluruhan</h6>
        </div>
        <div class="card-body">

            {{-- Filter Form --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="search-input-reject" class="form-control" placeholder="Cari no SPK atau nama konsumen...">
                </div>
                <div class="col-md-6 text-md-end">
                    {{-- Form Filter Tanggal --}}
                    <form method="GET" action="{{ route('rekap-reject') }}" class="d-inline-block me-2">
                        <div class="input-group">
                            <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" title="Dari Tanggal">
                            <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" title="Sampai Tanggal">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            <a href="{{ route('rekap-reject') }}" class="btn btn-sm btn-outline-secondary" title="Hapus Filter">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive mt-2">
                <table id="rekap-reject-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nomor SPK</th>
                            <th>Nama Konsumen</th>
                            <th>QTY</th>
                            <th>Meter</th>
                            <th>Nominal Kerugian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rejectedSpks as $spk)
                        <tr>
                            <td>{{ $spk->spk_number }}</td>
                            <td>{{ $spk->customer_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            @php
                            $nominal = $spk->price_per_meter ? ($spk->total_meter * $spk->price_per_meter) : 0;
                            @endphp
                            <td>Rp. {{ number_format($nominal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="5">Tidak ada data reject pada rentang tanggal yang dipilih.</td>
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
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ... (Kode Chart.js Anda tetap sama dan tidak diubah)
        const chartLabels = @json($chartLabels ?? []);
        const chartValues = @json($chartValues ?? []);

        const chartElement = document.getElementById('rejectChart');
        if (chartElement) {
            var ctx = chartElement.getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Nominal Kerugian',
                        data: chartValues,
                        backgroundColor: 'rgba(231, 74, 59, 0.05)',
                        borderColor: 'rgba(231, 74, 59, 1)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(231, 74, 59, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize DataTable
        var table = $('#rekap-reject-table').DataTable({
            "pageLength": 10,
            "lengthChange": true,
            "searching": false,
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

        // Update search when typing in the custom search input
        $('#search-input-reject').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush