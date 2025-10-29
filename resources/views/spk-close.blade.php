@extends('layouts.app')

@section('content')

{{-- SPK-Close --}}
<!-- <div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja (SPK) - Closed Admin</h3>
</div> -->

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
<div>
    <div class="card shadow">
        <div class="card-header py-3">
            <h4 class="text-primary m-0 fw-bold">Surat Perintah Kerja (SPK) - Closed Admin</h4>
        </div>
        
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table id="spk-close-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Order</th>
                            <th>Tanggal Closed</th>
                            <th>Nama Konsumen</th>
                            <th>Nama Order</th>
                            <th>QTY</th>
                            <th>Meter</th>
                            <th>Status</th>
                            <th>Harga @pieces</th>
                            <th>Harga @meter</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $filteredSpkList = $closedSpkList->filter(function($spk) {
                        return empty($spk->price_per_meter) && empty($spk->harga_per_piece);
                        });
                        @endphp
                        @forelse ($filteredSpkList as $spk)
                        <tr id="spk-row-{{ $spk->id }}">
                            <td>{{ $spk->spk_number }}</td>
                            <td>{{ $spk->closed_date ? \Carbon\Carbon::parse($spk->closed_date)->format('d M Y') : 'N/A' }}</td>
                            <td>{{ $spk->customer_name }}</td>
                            <td>{{ $spk->order_name }}</td>
                            <td>{{ $spk->total_qty }}</td>
                            <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                            <td class="text-white text-center {{ $spk->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">{{ $spk->status }}</td>
                            <td>
                                <input type="number" step="0.01" class="form-control price-input-pieces" name="harga_per_piece" data-spk-id="{{ $spk->id }}" placeholder="Rp." value="{{ old('harga_per_piece', $spk->harga_per_piece) }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control price-input-meter" name="price_per_meter" data-spk-id="{{ $spk->id }}" placeholder="Rp." value="{{ old('price_per_meter', $spk->price_per_meter) }}">
                            </td>
                            <td class="text-center">
                                <button class="btn {{ $spk->status == 'Closed' ? 'btn-success' : 'btn-danger' }} form-control btn-save" type="button" data-spk-id="{{ $spk->id }}">Save</button>

                                <!-- Modal save price for this specific SPK-->
                                <div class="modal fade" id="saveModal{{ $spk->id }}" tabindex="-1" aria-labelledby="saveModalLabel{{ $spk->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="saveModalLabel{{ $spk->id }}">Konfirmasi Harga</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menyimpan harga-harga ini dan <strong>tidak bisa</strong> dirubah lagi?</p>
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
                                                    <p class="form-control-plaintext" id="modal-price-meter-{{ $spk->id }}">{{ $spk->price_per_meter ?: 'Belum diisi' }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Harga Per-Piece Baju:</strong></label>
                                                    <p class="form-control-plaintext" id="modal-price-pieces-{{ $spk->id }}">{{ $spk->harga_per_piece ?: 'Belum diisi' }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="button" class="btn btn-success btn-confirm-save" data-spk-id="{{ $spk->id }}" id="confirm-save-{{ $spk->id }}">
                                                    <i class="fas fa-check-circle me-1"></i>Ya, Simpan Harga
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="10">Tidak ada SPK yang perlu diisi harganya.</td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
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
        <div class="card-footer"></div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        $('#spk-close-table').DataTable({
            "pageLength": 10,
            "lengthChange": true,
            "searching": true,
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

        // Function to show notification using the same style as project alerts
        function showNotification(message, type) {
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

        // Update modal price when input changes for both fields
        document.querySelectorAll('.price-input-pieces').forEach(function(input) {
            input.addEventListener('input', function() {
                const spkId = this.dataset.spkId;
                const modalPrice = document.getElementById('modal-price-pieces-' + spkId);
                if (modalPrice) {
                    modalPrice.textContent = this.value || 'Belum diisi';
                }
            });
        });
        
        document.querySelectorAll('.price-input-meter').forEach(function(input) {
            input.addEventListener('input', function() {
                const spkId = this.dataset.spkId;
                const modalPrice = document.getElementById('modal-price-meter-' + spkId);
                if (modalPrice) {
                    modalPrice.textContent = this.value || 'Belum diisi';
                }
            });
        });

        // Handle save button click
        document.querySelectorAll('.btn-save').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const spkId = this.dataset.spkId;
                const priceInputPieces = document.querySelector(`.price-input-pieces[data-spk-id="${spkId}"]`);
                const priceInputMeter = document.querySelector(`.price-input-meter[data-spk-id="${spkId}"]`);
                
                // Check if at least one price is filled
                if (!priceInputPieces.value && !priceInputMeter.value) {
                    showNotification('Harap isi setidaknya satu harga (per piece atau per meter).', 'error');
                    return;
                }

                // Update modal with current values
                const modalPriceMeter = document.getElementById('modal-price-meter-' + spkId);
                if (modalPriceMeter) {
                    modalPriceMeter.textContent = priceInputMeter.value || 'Belum diisi';
                }
                
                const modalPricePieces = document.getElementById('modal-price-pieces-' + spkId);
                if (modalPricePieces) {
                    modalPricePieces.textContent = priceInputPieces.value || 'Belum diisi';
                }

                const modal = new bootstrap.Modal(document.getElementById('saveModal' + spkId));
                modal.show();
            });
        });

        // Handle confirm save button click
        document.querySelectorAll('.btn-confirm-save').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const spkId = this.dataset.spkId;
                const priceInputPieces = document.querySelector(`.price-input-pieces[data-spk-id="${spkId}"]`);
                const priceInputMeter = document.querySelector(`.price-input-meter[data-spk-id="${spkId}"]`);
                
                // Check if at least one price is filled
                if (!priceInputPieces.value && !priceInputMeter.value) {
                    showNotification('Setidaknya satu harga (per piece atau per meter) harus diisi.', 'error');
                    return;
                }

                const formData = new FormData();
                if (priceInputPieces.value) {
                    formData.append('harga_per_piece', priceInputPieces.value);
                }
                if (priceInputMeter.value) {
                    formData.append('price_per_meter', priceInputMeter.value);
                }
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
                                        location.reload(); // reload the page to update the table
                                    }, 500);
                                }
                                const modal = bootstrap.Modal.getInstance(document.getElementById('saveModal' + spkId));
                                if (modal) {
                                    modal.hide();
                                }
                                showNotification('Harga berhasil disimpan.', 'success');
                            } else {
                                showNotification(data.message || 'Gagal menyimpan harga dari server.', 'error');
                            }
                        } catch (e) {
                            // Jika bukan JSON, mungkin ada error redirect atau HTML
                            showNotification('Terjadi respons tidak terduga dari server.', 'error');
                            console.error("Response is not JSON:", text);
                        }
                    })
                    .catch(error => {
                        showNotification('Terjadi kesalahan saat menyimpan harga: ' + error.message, 'error');
                    });
            });
        });
    });
</script>
@endpush