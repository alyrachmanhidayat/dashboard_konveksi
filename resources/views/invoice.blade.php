@extends('layouts.app')

@section('content')
{{-- invoice --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Terbitkan Invoice</h3>
</div>

{{-- notif alert --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form id="publish-form" action="{{ route('invoice.publish') }}" method="POST">
    @csrf
    <div id="invoice-list">
        <div class="card shadow">
            <div class="card-header"></div>
            <div class="card-body">
                {{-- Kontrol untuk List.js --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control search" placeholder="Cari no order atau nama konsumen...">
                    </div>
                    <div class="col-md-8 text-md-end">
                        <span class="me-2">Urutkan berdasarkan:</span>
                        <button class="btn btn-sm btn-outline-primary sort" data-sort="no-order">No Order</button>
                        <button class="btn btn-sm btn-outline-primary sort" data-sort="konsumen">Konsumen</button>
                        <button class="btn btn-sm btn-outline-primary sort" data-sort="nilai">Nilai</button>
                    </div>
                </div>

                <div class="table-responsive mt-2">
                    <table class="table my-0">
                        <thead>
                            <tr>
                                <th>No Order</th>
                                <th>Nama Konsumen</th>
                                <th>Nama Order</th>
                                <th>QTY</th>
                                <th>Nilai</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        {{-- Beri class="list" pada tbody --}}
                        <tbody class="list">
                            @forelse($spkList as $spk)
                            <tr>
                                {{-- Tambahkan class untuk valueNames List.js --}}
                                <td class="no-order">{{ $spk->spk_number }}</td>
                                <td class="konsumen">{{ $spk->customer_name }}</td>
                                <td>{{ $spk->order_name }}</td>
                                <td>{{ $spk->total_qty }}</td>
                                <td class="nilai" data-nilai="{{ $spk->total_meter * $spk->price_per_meter }}">Rp. {{ number_format($spk->total_meter * $spk->price_per_meter, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input" name="selected_spk_ids[]" value="{{ $spk->id }}">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada SPK yang siap diterbitkan invoice.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Kontainer untuk pagination List.js --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p id="listjs-info-invoice"></p>
                    </div>
                    <div class="col-md-6">
                        <ul class="pagination justify-content-end"></ul>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-end py-2">
                    <button class="btn btn-success" type="submit">Terbitkan Invoice Terpilih</button>
                    |
                    {{-- hanya memicu fungsi JavaScript sederhana --}}
                    <button type="button" class="btn btn-info" onclick="publishAndPrint()">
                        Publish & Print Selected Invoices
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Opsi untuk List.js
        var options = {
            valueNames: [
                'no-order',
                'konsumen',
                {
                    name: 'nilai',
                    attr: 'data-nilai'
                }
            ],
            page: 10,
            pagination: {
                paginationClass: "pagination",
            },
        };

        // Inisialisasi List.js
        var invoiceList = new List('invoice-list', options);

        // Fungsi untuk update info pagination
        function updateListInfo() {
            const info = document.getElementById('listjs-info-invoice');
            if (info) {
                const total = invoiceList.items.length;
                const page = invoiceList.page;
                const i = invoiceList.i;
                const showing = total === 0 ? 0 : Math.min((i + page - 1), total);
                const start = total === 0 ? 0 : i;
                info.textContent = `Menampilkan ${start} sampai ${showing} dari ${total} data`;
            }
        }

        // Panggil saat pertama kali dan setiap kali list diupdate
        updateListInfo();
        invoiceList.on('updated', updateListInfo);

        // Styling pagination List.js agar sesuai Bootstrap
        invoiceList.on('updated', function(list) {
            const paginationItems = document.querySelectorAll('.pagination li');
            paginationItems.forEach(function(item) {
                item.classList.add('page-item');
                const link = item.querySelector('a');
                if (link) link.classList.add('page-link');
            });
        });
    });

    // Handle the "Publish & Print Selected Invoices" button
    function publishAndPrint() {
        const form = document.getElementById('publish-form');

        // Cek apakah ada SPK yang dipilih
        const checkedCount = form.querySelectorAll('input[name="selected_spk_ids[]"]:checked').length;
        if (checkedCount === 0) {
            alert('Silakan pilih setidaknya satu SPK untuk diterbitkan.');
            return;
        }

        // 1. Buat sebuah input hidden untuk menandakan kita mau print setelah publish
        const printInput = document.createElement('input');
        printInput.type = 'hidden';
        printInput.name = 'redirect_to_print';
        printInput.value = '1';

        // 2. Tambahkan input ini ke dalam form
        form.appendChild(printInput);

        // 3. Submit form-nya
        form.submit();
    }

    // If there's a redirect_to_print parameter in session success, handle it
    // This is done by modifying the controller, which we already did
</script>
@endpush