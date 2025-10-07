@extends('layouts.app')

@section('content')

{{-- SPK-Close --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja (SPK) - Closed</h3>
</div>

{{-- Alert untuk notifikasi --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div id="spk-close-view-list">
    <div class="card shadow">
        <div class="card-header"></div>
        <div class="card-body">
            {{-- Kontrol untuk List.js --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control search" placeholder="Cari kode order atau nama konsumen...">
                </div>
                <div class="col-md-8 text-md-end">
                    <span class="me-2">Urutkan berdasarkan:</span>
                    <button class="btn btn-sm btn-outline-primary sort" data-sort="tanggal">Tanggal Close</button>
                    <button class="btn btn-sm btn-outline-primary sort" data-sort="konsumen">Konsumen</button>
                </div>
            </div>

            <div class="table-responsive mt-2">
                <table class="table my-0">
                    <thead>
                        <tr>
                            <th>Kode Order</th>
                            <th>Tanggal Closed</th>
                            <th>Nama Konsumen</th>
                            <th>Nama Order</th>
                            <th>QTY</th>
                            <th>Meter</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    {{-- Beri class="list" pada tbody --}}
                    <tbody class="list">
                        @forelse ($closedSpkList as $spk)
                        <tr>
                            {{-- Tambahkan class untuk valueNames List.js --}}
                            <td class="kode-order">{{ $spk->spk_number }}</td>
                            <td class="tanggal" data-tanggal="{{ $spk->closed_date }}">{{ $spk->closed_date ? \Carbon\Carbon::parse($spk->closed_date)->format('d M Y') : 'N/A' }}</td>
                            <td class="konsumen">{{ $spk->customer_name }}</td>
                            <td>{{ $spk->order_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            <td class="text-white text-center {{ $spk->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">{{ $spk->status }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada SPK yang ditutup atau ditolak.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Kontainer untuk pagination List.js --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <p id="listjs-info-public"></p>
                </div>
                <div class="col-md-6">
                    <ul class="pagination justify-content-end"></ul>
                </div>
            </div>
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
            valueNames: [
                'kode-order',
                'konsumen',
                {
                    name: 'tanggal',
                    attr: 'data-tanggal'
                }
            ],
            page: 10,
            pagination: {
                paginationClass: "pagination",
            },
        };

        // Inisialisasi List.js
        var spkCloseViewList = new List('spk-close-view-list', options);

        // Fungsi untuk update info pagination
        function updateListInfo() {
            const info = document.getElementById('listjs-info-public');
            if (info) {
                const total = spkCloseViewList.items.length;
                const page = spkCloseViewList.page;
                const i = spkCloseViewList.i;
                const showing = total === 0 ? 0 : (i + page - 1);
                const start = total === 0 ? 0 : i;
                info.textContent = `Menampilkan ${start} sampai ${showing} dari ${total} data`;
            }
        }

        // Panggil saat pertama kali dan setiap kali list diupdate
        updateListInfo();
        spkCloseViewList.on('updated', updateListInfo);

        // Styling pagination List.js agar sesuai Bootstrap
        spkCloseViewList.on('updated', function(list) {
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