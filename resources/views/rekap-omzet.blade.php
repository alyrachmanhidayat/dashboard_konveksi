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
<div id="rekap-omzet-list">
    <div class="card shadow">
        <div class="card-header">
            <h6 class="text-primary fw-bold m-0">Detail Omzet Keseluruhan</h6>
        </div>
        <div class="card-body">

            {{-- Filter Form & List.js Controls --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control search" placeholder="Cari no invoice atau nama konsumen...">
                </div>
                <div class="col-md-8 text-md-end">
                    {{-- Form Filter Tanggal --}}
                    <form method="GET" action="{{ route('rekap-omzet') }}" class="d-inline-block me-2">
                        <div class="input-group">
                            <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" title="Dari Tanggal">
                            <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" title="Sampai Tanggal">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            <a href="{{ route('rekap-omzet') }}" class="btn btn-sm btn-outline-secondary" title="Hapus Filter">Clear</a>
                        </div>
                    </form>

                    {{-- Tombol Sort List.js --}}
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary sort" data-sort="no-invoice">No Invoice</button>
                        <button class="btn btn-sm btn-outline-primary sort" data-sort="konsumen">Konsumen</button>
                        <button class="btn btn-sm btn-outline-primary sort" data-sort="nominal">Nominal</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-2">
                <table class="table my-0">
                    <thead>
                        <tr>
                            <th>Nomor Invoice</th>
                            <th>Nama Konsumen</th>
                            <th>QTY</th>
                            <th>Meter</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    {{-- Beri class="list" pada tbody --}}
                    <tbody class="list">
                        @forelse ($invoices as $invoice)
                        <tr>
                            {{-- Tambahkan class untuk valueNames List.js --}}
                            <td class="no-invoice">{{ $invoice->invoice_number }}</td>
                            <td class="konsumen">{{ $invoice->customer_name }}</td>
                            <td>{{ $invoice->total_qty }}</td>
                            <td>{{ $invoice->spk ? $invoice->spk->total_meter : 'N/A' }}</td>
                            <td class="nominal" data-nominal="{{ $invoice->total_amount }}">Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data omzet pada rentang tanggal yang dipilih.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Kontainer untuk pagination List.js --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <p id="listjs-info-omzet"></p>
                </div>
                <div class="col-md-6">
                    <ul class="pagination justify-content-end"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js & List.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ... (Kode Chart.js Anda tetap sama dan tidak diubah)
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

        // Inisialisasi List.js untuk tabel detail omzet
        var options = {
            valueNames: [
                'no-invoice',
                'konsumen',
                {
                    name: 'nominal',
                    attr: 'data-nominal'
                }
            ],
            page: 10,
            pagination: {
                paginationClass: "pagination",
            },
        };

        var omzetList = new List('rekap-omzet-list', options);

        // Fungsi untuk update info pagination
        function updateListInfo() {
            const info = document.getElementById('listjs-info-omzet');
            if (info) {
                const total = omzetList.items.length;
                const page = omzetList.page;
                const i = omzetList.i;
                const showing = total === 0 ? 0 : Math.min((i + page - 1), total);
                const start = total === 0 ? 0 : i;
                info.textContent = `Menampilkan ${start} sampai ${showing} dari ${total} data`;
            }
        }

        // Panggil saat pertama kali dan setiap kali list diupdate
        updateListInfo();
        omzetList.on('updated', updateListInfo);

        // Styling pagination List.js agar sesuai Bootstrap
        omzetList.on('updated', function(list) {
            const paginationItems = document.querySelectorAll('.pagination li');
            paginationItems.forEach(function(item) {
                item.classList.add('page-item');
                const link = item.querySelector('a');
                if (link) link.classList.add('page-link');
            });
        });
    });
</script>
@endpush