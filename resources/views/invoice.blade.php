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
    <div>
        <div class="card shadow">
            <div class="card-header"></div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <table id="invoice-table" class="table table-striped">
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
                        <tbody>
                            @forelse($spkList as $spk)
                                @if($spk->status == 'Closed' && $spk->price_per_meter && $spk->total_meter)
                            <tr>
                                <td>{{ $spk->spk_number }}</td>
                                <td>{{ $spk->customer_name }}</td>
                                <td>{{ $spk->order_name }}</td>
                                <td>{{ $spk->total_qty }}</td>
                                <td>Rp. {{ number_format($spk->total_meter * $spk->price_per_meter, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input" name="selected_spk_ids" value="{{ $spk->id }}" onchange="toggleSubmitButton()">
                                </td>
                            </tr>
                                @endif
                            @empty
                                @php
                                    $filteredSpkList = $spkList->filter(function($spk) {
                                        return $spk->status == 'Closed' && $spk->price_per_meter && $spk->total_meter;
                                    });
                                @endphp
                                @if($filteredSpkList->count() == 0)
                            <tr>
                                <td class="text-center" colspan="6">Belum ada SPK yang siap diterbitkan invoice.</td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                            </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-end py-2">
                    <!-- <button class="btn btn-success" type="submit">Terbitkan Invoice Terpilih</button> -->

                    {{-- Changed from inline JavaScript to form submission with redirect_to_print flag and open in new tab --}}
                    <button type="submit" class="btn btn-info" name="redirect_to_print" value="1" formtarget="_blank" id="submit-btn" disabled>
                        Publish & Print Selected Invoices
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        $('#invoice-table').DataTable({
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
        
        // Initialize button state on page load
        toggleSubmitButton();
    });
    
    // Function to toggle submit button based on radio selection
    function toggleSubmitButton() {
        const selectedRadio = document.querySelector('input[name="selected_spk_ids"]:checked');
        const submitButton = document.getElementById('submit-btn');
        
        if (selectedRadio) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    // Handle the "Publish & Print Selected Invoices" button
    function publishAndPrint() {
        const form = document.getElementById('publish-form');

        // Cek apakah ada SPK yang dipilih (for radio button)
        const selectedRadio = form.querySelector('input[name="selected_spk_ids"]:checked');
        if (!selectedRadio) {
            // Create and show Bootstrap alert
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
                <div id="selectSpkAlert" class="alert alert-warning alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Silakan pilih satu SPK untuk diterbitkan.
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
                
                // Show success message
                const successAlert = document.createElement('div');
                successAlert.innerHTML = `
                    <div id="successAlert" class="alert alert-success alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>Invoice berhasil diterbitkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(successAlert);

                // Refresh the page after 3 seconds to update the table
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else if (data.message) {
                const messageAlert = document.createElement('div');
                messageAlert.innerHTML = `
                    <div id="messageAlert" class="alert alert-success alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>${data.message || 'Invoice berhasil diterbitkan!'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(messageAlert);

                // Refresh the page after 3 seconds to update the table
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                // Handle other responses
                const successAlert2 = document.createElement('div');
                successAlert2.innerHTML = `
                    <div id="successAlert2" class="alert alert-success alert-dismissible fade show fixed-top mt-3" role="alert" style="left: 50%; transform: translateX(-50%); max-width: 500px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>Invoice berhasil diterbitkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(successAlert2);

                // Refresh the page after 3 seconds to update the table
                setTimeout(() => {
                    location.reload();
                }, 3000);
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