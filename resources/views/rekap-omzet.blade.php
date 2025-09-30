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
                        <div class="text-uppercase text-primary mb-1 fw-bold text-xs"><span>Order Selesai</span></div>
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
                        <div class="text-uppercase text-success mb-1 fw-bold text-xs"><span>Omzet</span></div>
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
                        <div class="text-uppercase text-info mb-1 fw-bold text-xs"><span>QTY</span></div>
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
                        <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span>Meter</span></div>
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
<div class="card shadow">
    <div class="card-header">
        <h6 class="text-primary fw-bold m-0">Detail Omzet</h6>
    </div>
    <div class="card-body">
        {{-- Filter Form --}}
        <form method="GET" action="{{ route('rekap-omzet') }}">
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
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
                    @forelse ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>{{ $invoice->total_qty }}</td>
                        <td>{{ $invoice->spk ? $invoice->spk->total_meter : 'N/A' }}</td>
                        <td>Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data omzet pada rentang tanggal yang dipilih.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Pastikan Chart.js sudah di-load di layout utama Anda --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
    });
</script>
@endpush