@extends('layouts.app')

@section('content')

{{-- Rekap Reject --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Rekap Reject</h3>
</div>

{{-- Cards --}}
<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow py-2 border-left-danger">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-uppercase text-danger mb-1 fw-bold text-xs"><span>Order Reject</span></div>
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
                        <div class="text-uppercase text-secondary mb-1 fw-bold text-xs"><span>Nominal Kerugian</span></div>
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
                        <div class="text-uppercase text-dark mb-1 fw-bold text-xs"><span>QTY</span></div>
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
                        <div class="text-uppercase text-warning mb-1 fw-bold text-xs"><span>Meter</span></div>
                        <div class="text-dark mb-0 fw-bold h5"><span>{{ $totalMeterReject }}</span></div>
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
                <h6 class="text-primary m-0 fw-bold">Grafik Reject (12 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="rejectChart"></canvas></div>
            </div>
        </div>
    </div>
</div>


<div class="card shadow">
    <div class="card-header">
        <h6 class="text-primary fw-bold m-0">Detail Reject</h6>
    </div>
    <div class="card-body">
        {{-- Filter Form --}}
        <form method="GET" action="{{ route('rekap-reject') }}">
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
                        <td>Rp. {{ number_format($spk->total_meter * $spk->price_per_meter, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data reject pada rentang tanggal yang dipilih.</td>
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
        // Ambil data dari variabel PHP langsung ke JavaScript dengan penanganan kesalahan
        const chartLabels = @json($chartLabels ?? []);
        const chartValues = @json($chartValues ?? []);

        // Pastikan elemen canvas tersedia dan data valid
        const chartElement = document.getElementById('rejectChart');
        if (chartElement && chartLabels && chartValues) {
            var ctx = chartElement.getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar', // Bar chart lebih cocok untuk jumlah kasus
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah SPK Ditolak',
                        data: chartValues,
                        backgroundColor: 'rgba(231, 74, 59, 0.5)',
                        borderColor: 'rgba(231, 74, 59, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // Hanya angka bulat
                            }
                        }
                    }
                }
            });
        } else {
            console.error("Gagal membuat chart: data tidak valid atau elemen tidak ditemukan", {
                labels: chartLabels,
                values: chartValues,
                elementExists: !!chartElement
            });
        }
    });
</script>
@endpush