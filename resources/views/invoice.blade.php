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
                    <!-- <button class="btn btn-success" type="submit">Terbitkan Invoice Terpilih</button> -->

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
            // Create and show Bootstrap alert
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
                <div id="selectSpkAlert" class="alert alert-warning alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Silakan pilih setidaknya satu SPK untuk diterbitkan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertContainer);

            // Auto remove alert after 3 seconds
            setTimeout(() => {
                const alertElement = document.getElementById('selectSpkAlert');
                if (alertElement) {
                    const bsAlert = bootstrap.Alert.getInstance(alertElement) || new bootstrap.Alert(alertElement);
                    bsAlert.close();
                    setTimeout(() => {
                        if (alertElement.parentNode) {
                            alertElement.parentNode.removeChild(alertElement);
                        }
                    }, 150); // Match the fade out duration
                }
            }, 3000);
            return;
        }

        // Submit the form via AJAX to create the invoices
        const formData = new FormData(form);
        
        // Add a flag to indicate we want to print after publishing
        formData.append('redirect_to_print', '1');
        
        fetch('{{ route("invoice.publish") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                // Open the print page in a new window
                window.open(data.redirect, '_blank');
                
                // Show success message and refresh current page
                const successAlert = document.createElement('div');
                successAlert.innerHTML = `
                    <div id="successAlert" class="alert alert-success alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>Invoice berhasil diterbitkan! Halaman akan dimuat ulang.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(successAlert);

                // Reload page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else if (data.message) {
                const messageAlert = document.createElement('div');
                messageAlert.innerHTML = `
                    <div id="messageAlert" class="alert alert-success alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>${data.message || 'Invoice berhasil diterbitkan!'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(messageAlert);

                // Auto remove after 2 seconds
                setTimeout(() => {
                    const alertElement = document.getElementById('messageAlert');
                    if (alertElement) {
                        const bsAlert = bootstrap.Alert.getInstance(alertElement) || new bootstrap.Alert(alertElement);
                        bsAlert.close();
                        setTimeout(() => {
                            if (alertElement.parentNode) {
                                alertElement.parentNode.removeChild(alertElement);
                            }
                        }, 150);
                        location.reload();
                    }
                }, 2000);
            } else {
                // Handle other responses by reloading the page
                const successAlert2 = document.createElement('div');
                successAlert2.innerHTML = `
                    <div id="successAlert2" class="alert alert-success alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>Invoice berhasil diterbitkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(successAlert2);

                // Auto remove after 2 seconds
                setTimeout(() => {
                    const alertElement = document.getElementById('successAlert2');
                    if (alertElement) {
                        const bsAlert = bootstrap.Alert.getInstance(alertElement) || new bootstrap.Alert(alertElement);
                        bsAlert.close();
                        setTimeout(() => {
                            if (alertElement.parentNode) {
                                alertElement.parentNode.removeChild(alertElement);
                            }
                        }, 150);
                        location.reload();
                    }
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorAlert = document.createElement('div');
            errorAlert.innerHTML = `
                <div id="errorAlert" class="alert alert-danger alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Terjadi kesalahan saat menerbitkan invoice. Silakan coba lagi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(errorAlert);

            // Auto remove after 3 seconds
            setTimeout(() => {
                const alertElement = document.getElementById('errorAlert');
                if (alertElement) {
                    const bsAlert = bootstrap.Alert.getInstance(alertElement) || new bootstrap.Alert(alertElement);
                    bsAlert.close();
                    setTimeout(() => {
                        if (alertElement.parentNode) {
                            alertElement.parentNode.removeChild(alertElement);
                        }
                    }, 150);
                }
            }, 3000);
        });
    }
</script>
@endpush