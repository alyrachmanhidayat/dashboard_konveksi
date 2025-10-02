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

<div class="card shadow">
    <div class="card-header"></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 text-nowrap">
                <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable"><label class="form-label">Show&nbsp;<select class="d-inline-block form-select form-select-sm">
                            <option value="10" selected="">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>&nbsp;</label></div>
            </div>
            <div class="col-md-6">
                <div class="text-md-end dataTables_filter" id="dataTable_filter"><label class="form-label"><input type="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search"></label></div>
            </div>
        </div>
        <div class="table-responsive mt-2 table" id="dataTable" role="grid" aria-describedby="dataTable_info">
            <table class="table my-0" id="dataTable">
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
                <tbody>
                    @php
                        $filteredSpkList = $closedSpkList->filter(function($spk) {
                            return empty($spk->price_per_meter);
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
                            <input type="number" step="1" class="form-control price-input" name="price_per_meter" data-spk-id="{{ $spk->id }}" placeholder="Rp." value="{{ old('price_per_meter', $spk->price_per_meter) }}">
                        </td>
                        <td class="text-center">
                            <button class="btn {{ $spk->status == 'Closed' ? 'btn-success' : 'btn-danger' }} form-control btn-save" type="button" data-spk-id="{{ $spk->id }}">Save</button>

                            <!-- Modal save price for this specific SPK -->
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
                        <td colspan="9" class="text-center">Belum ada SPK yang ditutup atau ditolak.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr></tr>
                </tfoot>
            </table>
        </div>
        <div class="row">
            <div class="col-md-6 align-self-center">
                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">Showing {{ $closedSpkList->count() }} data</p>
            </div>
            <div class="col-md-6">
                {{-- Pagination bisa ditambahkan di sini nanti jika perlu --}}
            </div>
        </div>
    </div>
    <div class="card-footer"></div>
</div>

@endsection

@push('scripts')
<script>
    // Function to show notification using the same style as project alerts
    function showNotification(message, type) {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.temp-notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notificationDiv = document.createElement('div');
        notificationDiv.className = `alert temp-notification alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        notificationDiv.setAttribute('role', 'alert');
        notificationDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert notification at the top of the content
        const contentContainer = document.querySelector('.d-sm-flex')?.parentElement;
        if (contentContainer) {
            contentContainer.insertBefore(notificationDiv, contentContainer.firstChild);
        } else {
            // Fallback: add to body if specific container not found
            document.body.insertBefore(notificationDiv, document.body.firstChild);
        }
        
        // Auto-hide notification after 5 seconds
        setTimeout(() => {
            if (notificationDiv.classList.contains('show')) {
                const bsAlert = bootstrap.Alert.getInstance(notificationDiv);
                if (bsAlert) {
                    bsAlert.close();
                } else {
                    // Fallback if Bootstrap Alert instance doesn't exist
                    notificationDiv.classList.remove('show');
                    setTimeout(() => {
                        notificationDiv.remove();
                    }, 150);
                }
            }
        }, 5000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
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

                // Update modal price display
                const modalPrice = document.getElementById('modal-price-' + spkId);
                if (modalPrice) {
                    modalPrice.textContent = priceInput.value;
                }

                // Show the modal
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

                // Prepare form data for AJAX request
                const formData = new FormData();
                formData.append('price_per_meter', priceInput.value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');

                // Send AJAX request
                fetch(`{{ route('invoice.save_price', ':spkId') }}`.replace(':spkId', spkId), {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // Check if the response is ok
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        // Since we know there was a redirect, we should get the redirected URL's content
                        return response.text(); // Get response as text since it might be HTML
                    })
                    .then(text => {
                        // Check if the response contains success message
                        if (text && typeof text === 'string' && text.includes('berhasil disimpan')) {
                            // Remove the table row with fade-out effect
                            const row = document.getElementById('spk-row-' + spkId);
                            if (row) {
                                row.style.transition = 'opacity 0.5s';
                                row.style.opacity = '0';
                                setTimeout(() => {
                                    row.remove();
                                }, 500);
                            }

                            // Hide the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('saveModal' + spkId));
                            if (modal) {
                                modal.hide();
                            }

                            // Show success notification using the same style as the project
                            showNotification('Harga kain berhasil disimpan.', 'success');
                        } else {
                            // Try to extract error message from HTML if possible
                            // Look for alert messages in the HTML
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(text, 'text/html');
                            const alertDiv = doc.querySelector('.alert');
                            let errorMessage = 'Gagal menyimpan harga kain';

                            if (alertDiv) {
                                errorMessage = alertDiv.textContent.trim();
                            } else {
                                // Extract potential error messages from the HTML
                                const errorMatch = text.match(/alert-(?:danger|error)[^>]*>[\s\S]*?<\/div>/i);
                                if (errorMatch) {
                                    errorMessage = errorMatch[0].replace(/<[^>]*>/g, '').trim();
                                }
                            }

                            showNotification('Terjadi kesalahan: ' + errorMessage, 'error');
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