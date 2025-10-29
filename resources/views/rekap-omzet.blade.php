@extends('layouts.app')

@section('content')
{{-- Rekap Omzet --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Rekap Omzet</h3>
</div>

{{-- Cards --}}
<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-primary">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-primary mb-1 fw-bold text-xs"><span>Order Selesai (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $orderSelesai }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-success">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-success mb-1 fw-bold text-xs"><span>Omzet (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>Rp {{ number_format($totalOmzet, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-info">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-info mb-1 fw-bold text-xs"><span>QTY (Bulan Ini)</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $totalQty }}</span></div>
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
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $totalMeter }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-ruler fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="row">
    <div class="col">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="text-primary m-0 fw-bold">Grafik Omzet (12 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="omzetChart"></canvas></div>
            </div>
        </div>
    </div>
</div>


{{-- Table --}}
<div>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="text-primary fw-bold m-0">Detail Omzet Keseluruhan</h6>
        </div>
        <div class="card-body">

            {{-- Filter Form --}}
            <div class="row mb-3 d-flex justify-content-end">
                <div class="col-md-6 text-md-start mt-2">
                    {{-- Date Range Filter (Client-side with DataTables) --}}
                    <div class="input-group">
                        <input type="date" id="min-date" class="form-control form-control-sm" placeholder="Dari Tanggal" title="Dari Tanggal">
                        <input type="date" id="max-date" class="form-control form-control-sm" placeholder="Sampai Tanggal" title="Sampai Tanggal">
                        <button type="button" id="clear-filter" class="btn btn-sm btn-outline-primary" title="Hapus Filter">Clear</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-2">
                <table id="rekap-omzet-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Invoice</th>
                            <th>Nama Konsumen</th>
                            <th>QTY</th>
                            <th>Meter</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                        <tr>
                            <td data-order="{{ $invoice->updated_at->format('Y-m-d') }}">{{ $invoice->updated_at->format('d/m/Y') }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer_name }}</td>
                            <td>{{ $invoice->total_qty }}</td>
                            <td>{{ $invoice->spk ? $invoice->spk->total_meter : 'N/A' }}</td>
                            <td>Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="6">Tidak ada data omzet pada rentang tanggal yang dipilih.</td>
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
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js & DataTables --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Chart.js code
        const chartLabels = @json($chartLabels ?? []);
        const chartValues = @json($chartValues ?? []);

        const chartElement = document.getElementById('omzetChart');
        if (chartElement) {
            var ctx = chartElement.getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Omzet',
                        data: chartValues,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
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

        // Custom date range filtering function for DataTables
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var min = $('#min-date').val();
                var max = $('#max-date').val();
                var date = data[0]; // Date column is now index 0 (first column)
                
                // Convert date from dd/mm/yyyy to yyyy-mm-dd for comparison
                var dateParts = date.split('/');
                if (dateParts.length === 3) {
                    var dateStr = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]; // yyyy-mm-dd
                } else {
                    return true; // If date format is invalid, show the row
                }
                
                if (
                    (min === '' && max === '') ||
                    (min === '' && dateStr <= max) ||
                    (min <= dateStr && max === '') ||
                    (min <= dateStr && dateStr <= max)
                ) {
                    return true;
                }
                return false;
            }
        );

        // Initialize DataTable
        var table = $('#rekap-omzet-table').DataTable({
            "pageLength": 10,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[0, 'desc']], // Sort by date column (descending)
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

        // Event listener for date inputs - redraw table when dates change
        $('#min-date, #max-date').on('change', function() {
            table.draw();
        });

        // Clear filter button
        $('#clear-filter').on('click', function() {
            $('#min-date').val('');
            $('#max-date').val('');
            table.draw();
        });
    });
</script>
@endpush