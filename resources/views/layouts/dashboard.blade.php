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

<div id="spk-table-list">
    <div class="card shadow">
        <div class="card-header py-3">
            <p class="text-primary m-0 fw-bold">Dashboard</p>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    {{-- Input Pencarian --}}
                    <input type="text" class="form-control search" placeholder="Cari konsumen atau nama order...">
                </div>
                <div class="col-md-8 text-md-end">
                    {{-- Tombol Sort --}}
                    <span class="me-2">Urutkan berdasarkan:</span>
                    <button class="btn btn-sm btn-outline-primary sort" data-sort="deadline">Deadline</button>
                    <button class="btn btn-sm btn-outline-primary sort" data-sort="konsumen">Konsumen</button>
                    <button class="btn btn-sm btn-outline-primary sort" data-sort="progress">Progress</button>
                </div>
            </div>

            <div class="table-responsive mt-2">
                <table class="table my-0">
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
                    {{-- Beri class="list" pada tbody --}}
                    <tbody class="list">
                        @forelse($spkList as $spk)
                        <tr>
                            {{-- Beri class pada elemen yang ingin di-sort/search --}}
                            <td>
                                {{-- Atribut data-deadline digunakan untuk sorting --}}
                                <div class="{{ $spk->bgColor }} text-white p-2 rounded deadline" data-deadline="{{ $spk->delivery_date }}">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $spk->formatted_delivery_date }}</span>
                                </div>
                            </td>
                            <td class="konsumen">{{ $spk->customer_name }}</td>
                            <td class="nama-order">{{ $spk->order_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            {{-- Atribut data-progress digunakan untuk sorting --}}
                            <td class="progress" data-progress="{{ $spk->progressPercentage }}" style="min-height:max-content">
                                <div class="progress mb-3 progress-sm" style="height: 25px; min-width: 100px; max-width: 150px;">
                                    <div class="progress-bar {{ $spk->progressBarColor }}" role="progressbar"
                                        data-width="{{ $spk->progressPercentage }}"
                                        aria-valuenow="{{ $spk->progressPercentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $spk->progressPercentage }}%
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;"><a class="nav-link" href="{{ route('spk.edit', $spk->id) }}">Opsi</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada order yang tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- List.js akan otomatis menangani pagination jika diaktifkan --}}
            <div id="pagination-container" class="mt-3"> </div>
        </div>
        <div class="card-footer"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Opsi untuk List.js
        var options = {
            // valueNames adalah array berisi class dari elemen yang ingin kita cari/sort
            valueNames: [
                'konsumen',
                'nama-order',
                {
                    name: 'deadline',
                    attr: 'data-deadline'
                }, // Sort berdasarkan atribut data-deadline
                {
                    name: 'progress',
                    attr: 'data-progress'
                } // Sort berdasarkan atribut data-progress
            ],
            // Aktifkan pagination
            page: 10, // Tampilkan 10 item per halaman
            pagination: [{
                name: "pagination",
                item: '<li class="page-item"><a class="page-link" href="#"></a></li>'
            }]
        };

        // Inisialisasi List.js
        var spkList = new List('spk-table-list', options);

        // Tambahkan class Bootstrap ke pagination yang digenerate List.js
        spkList.on('updated', function(list) {
            const paginationUl = document.querySelector('.pagination');
            if (paginationUl) {
                paginationUl.classList.add('justify-content-end');
            }
        });

        // Set progress bar widths using data attributes
        function setProgressBarWidths() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(function(bar) {
                const width = bar.getAttribute('data-width');
                bar.style.width = width + '%';
            });
        }

        // Set initial widths when page loads
        setProgressBarWidths();
    });
</script>
@endpush