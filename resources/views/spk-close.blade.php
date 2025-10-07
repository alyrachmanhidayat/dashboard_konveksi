@extends('layouts.app')

@section('content')

{{-- SPK-Close --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja (SPK) - Closed Admin</h3>
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

{{-- Table --}}
<div id="spk-close-list">
    <div class="card shadow">
        <div class="card-header"></div>
        <div class="card-body">
            {{-- KONTROL UNTUK LIST.JS (SEARCH & SORT) --}}
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
                            <th>Harga @meter</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    {{-- Beri class="list" pada tbody --}}
                    <tbody class="list">
                        @php
                        $filteredSpkList = $closedSpkList->filter(function($spk) {
                        return empty($spk->price_per_meter);
                        });
                        @endphp
                        @forelse ($filteredSpkList as $spk)
                        <tr id="spk-row-{{ $spk->id }}">
                            {{-- Tambahkan class untuk valueNames List.js --}}
                            <td class="kode-order">{{ $spk->spk_number }}</td>
                            <td class="tanggal" data-tanggal="{{ $spk->closed_date }}">{{ $spk->closed_date ? \Carbon\Carbon::parse($spk->closed_date)->format('d M Y') : 'N/A' }}</td>
                            <td class="konsumen">{{ $spk->customer_name }}</td>
                            <td>{{ $spk->order_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            <td class="text-white text-center {{ $spk->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">{{ $spk->status }}</td>
                            <td>
                                <input type="number" step="1" class="form-control price-input" name="price_per_meter" data-spk-id="{{ $spk->id }}" placeholder="Rp." value="{{ old('price_per_meter', $spk->price_per_meter) }}">
                            </td>
                            <td class="text-center">
                                <button class="btn {{ $spk->status == 'Closed' ? 'btn-success' : 'btn-danger' }} form-control btn-save" type="button" data-spk-id="{{ $spk->id }}">Save</button>

                                <!-- Modal save price for this specific SPK (KODE ANDA TETAP ADA) -->
                                <div class="modal fade" id="saveModal{{ $spk->id }}" tabindex="-1" aria-labelledby="saveModalLabel{{ $spk->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="saveModalLabel{{ $spk->id }}">Konfirmasi Harga Kain</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menyimpan harga kain ini dan <strong>tidak bisa</strong> dirubah lagi?</p>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Kode Order:</strong></label>
                                                    <p class="form-control-plaintext">{{ $spk->spk_number }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Nama Konsumen:</strong></label>
                                                    <p class="form-control-plaintext">{{ $spk->customer_name }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Nama Order:</strong></label>
                                                    <p class="form-control-plaintext">{{ $spk->order_name }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Harga Per-Meter Kain:</strong></label>
                                                    <p class="form-control-plaintext" id="modal-price-{{ $spk->id }}">{{ $spk->price_per_meter ?: 'Belum diisi' }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="button" class="btn btn-success btn-confirm-save" data-spk-id="{{ $spk->id }}" id="confirm-save-{{ $spk->id }}">
                                                    <i class="fas fa-check-circle me-1"></i>Ya, Simpan Harga Kain
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada SPK yang perlu diisi harganya.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- KONTENER UNTUK PAGINATION LIST.JS --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <p id="listjs-info"></p>
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
{{-- KODE SCRIPT ANDA TIDAK SAYA UBAH --}}
<script>
    // Inisialisasi List.js
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            valueNames: [
                'kode-order',
                'konsumen',
                {
                    name: 'tanggal',
                    attr: 'data-tanggal'
                }
            ],
            page: 5,
            pagination: {
                paginationClass: "pagination",
            },
        };

        var spkCloseList = new List('spk-close-list', options);

        // Fungsi untuk update info pagination
        function updateListInfo() {
            const info = document.getElementById('listjs-info');
            if (info) {
                const total = spkCloseList.items.length;
                const page = spkCloseList.page;
                const i = spkCloseList.i;
                const showing = total === 0 ? 0 : i;
                info.textContent = `Menampilkan ${showing} dari ${total} data`;
            }
        }

        // Panggil saat pertama kali dan setiap kali list diupdate
        updateListInfo();
        spkCloseList.on('updated', updateListInfo);

        // Styling pagination List.js agar sesuai Bootstrap
        spkCloseList.on('updated', function(list) {
            const paginationItems = document.querySelectorAll('.pagination li');
            paginationItems.forEach(function(item) {
                item.classList.add('page-item');
                const link = item.querySelector('a');
                if (link) link.classList.add('page-link');
            });
        });

        // Function to show notification using the same style as project alerts
        function showNotification(message, type) {
            // ... (kode notifikasi Anda tetap sama)
            const existingNotifications = document.querySelectorAll('.temp-notification');
            existingNotifications.forEach(notification => notification.remove());
            const notificationDiv = document.createElement('div');
            notificationDiv.className = `alert temp-notification alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
            notificationDiv.setAttribute('role', 'alert');
            notificationDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            const contentContainer = document.querySelector('.d-sm-flex')?.parentElement;
            if (contentContainer) {
                contentContainer.insertBefore(notificationDiv, contentContainer.firstChild);
            } else {
                document.body.insertBefore(notificationDiv, document.body.firstChild);
            }
            setTimeout(() => {
                if (notificationDiv.classList.contains('show')) {
                    const bsAlert = bootstrap.Alert.getInstance(notificationDiv);
                    if (bsAlert) {
                        bsAlert.close();
                    } else {
                        notificationDiv.classList.remove('show');
                        setTimeout(() => {
                            notificationDiv.remove();
                        }, 150);
                    }
                }
            }, 5000);
        }

        // Update modal price when input changes
        document.querySelectorAll('.price-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const spkId = this.dataset.spkId;
                const modalPrice = document.getElementById('modal-price-' + spkId);
                if (modalPrice) {
                    modalPrice.textContent = this.value || 'Belum diisi';
                }
            });
        });

        // Handle save button click
        document.querySelectorAll('.btn-save').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const spkId = this.dataset.spkId;
                const priceInput = document.querySelector(`.price-input[data-spk-id="${spkId}"]`);

                if (!priceInput.value) {
                    showNotification('Harap isi harga per meter terlebih dahulu.', 'error');
                    return;
                }

                const modalPrice = document.getElementById('modal-price-' + spkId);
                if (modalPrice) {
                    modalPrice.textContent = priceInput.value;
                }

                const modal = new bootstrap.Modal(document.getElementById('saveModal' + spkId));
                modal.show();
            });
        });

        // Handle confirm save button click
        document.querySelectorAll('.btn-confirm-save').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const spkId = this.dataset.spkId;
                const priceInput = document.querySelector(`.price-input[data-spk-id="${spkId}"]`);

                if (!priceInput || !priceInput.value) {
                    showNotification('Harga per meter tidak ditemukan atau kosong.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('price_per_meter', priceInput.value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');

                fetch(`{{ route('invoice.save_price', ':spkId') }}`.replace(':spkId', spkId), {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(text => {
                        // Asumsi controller mengembalikan JSON, jadi kita parse
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                const row = document.getElementById('spk-row-' + spkId);
                                if (row) {
                                    row.style.transition = 'opacity 0.5s';
                                    row.style.opacity = '0';
                                    setTimeout(() => {
                                        row.remove();
                                        // Update list.js setelah menghapus elemen
                                        spkCloseList.reIndex();
                                        updateListInfo();
                                    }, 500);
                                }
                                const modal = bootstrap.Modal.getInstance(document.getElementById('saveModal' + spkId));
                                if (modal) {
                                    modal.hide();
                                }
                                showNotification('Harga kain berhasil disimpan.', 'success');
                            } else {
                                showNotification(data.message || 'Gagal menyimpan harga kain dari server.', 'error');
                            }
                        } catch (e) {
                            // Jika bukan JSON, mungkin ada error redirect atau HTML
                            showNotification('Terjadi respons tidak terduga dari server.', 'error');
                            console.error("Response is not JSON:", text);
                        }
                    })
                    .catch(error => {
                        showNotification('Terjadi kesalahan saat menyimpan harga kain: ' + error.message, 'error');
                    });
            });
        });
    });
</script>
@endpush