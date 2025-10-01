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
                    @forelse ($closedSpkList as $spk)
                    <tr>
                        <td>{{ $spk->spk_number }}</td>
                        <td>{{ $spk->closed_date ? \Carbon\Carbon::parse($spk->closed_date)->format('d M Y') : 'N/A' }}</td>
                        <td>{{ $spk->customer_name }}</td>
                        <td>{{ $spk->order_name }}</td>
                        <td>{{ $spk->total_qty }}</td>
                        <td>{{ $spk->total_meter ?? 'N/A' }}</td>
                        <!-- <td class="text-white text-center" style="background: {{ $spk->status == 'Closed' ? 'var(--bs-success)' : 'var(--bs-danger)' }};">{{ $spk->status }}</td> -->
                        <td class="text-white text-center {{ $spk->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">{{ $spk->status }}</td>
                        <td>
                            {{-- Form diberi ID unik sesuai dengan ID SPK --}}
                            <form action="{{ route('invoice.save_price', $spk->id) }}" method="POST" id="save-form-{{$spk->id}}">
                                @csrf
                                <input type="number" step="1" class="form-control" name="price_per_meter" placeholder="Rp." value="{{ old('price_per_meter', $spk->price_per_meter) }}">
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- Tombol ini sekarang akan show the modal for this specific SPK --}}
                            <button class="btn {{ $spk->status == 'Closed' ? 'btn-success' : 'btn-danger' }} form-control btn-modal-trigger" type="button" data-bs-toggle="modal" data-bs-target="#saveModal{{ $spk->id }}">Save</button>
                            
                            <!-- Modal save price for this specific SPK -->
                            <div class="modal fade" id="saveModal{{ $spk->id }}" tabindex="-1" aria-labelledby="saveModalLabel{{ $spk->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="saveModalLabel{{ $spk->id }}">Konfirmasi Harga Kain</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin simpan harga kain ini dan <strong>tidak bisa</strong> dirubah lagi?</p>
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Harga Per-Meter Kain (wajib diisi)</strong></label>
                                                <input type="number" step="1" class="form-control" name="price_per_meter_modal" id="price_per_meter_{{ $spk->id }}" placeholder="Rp." value="{{ $spk->price_per_meter }}">
                                                <div class="form-text">Nilai ini akan digunakan untuk menghitung total harga order.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('invoice.save_price', $spk->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="price_per_meter" id="hidden_price_per_meter_{{ $spk->id }}" value="{{ $spk->price_per_meter }}">
                                                <button type="submit" class="btn {{ $spk->status == 'Closed' ? 'btn-success' : 'btn-danger' }}" onclick="setPricePerMeter({{ $spk->id }});">
                                                    <i class="fas fa-check-circle me-1"></i>Ya, Simpan Harga Kain
                                                </button>
                                            </form>
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
    // Function to synchronize the price per meter value between the modal input and hidden form input
    function setPricePerMeter(spkId) {
        const modalInput = document.getElementById('price_per_meter_' + spkId);
        const hiddenInput = document.getElementById('hidden_price_per_meter_' + spkId);
        if (modalInput && hiddenInput) {
            hiddenInput.value = modalInput.value;
        }
    }
    
    // Update modal input when main form input changes
    document.addEventListener('DOMContentLoaded', function() {
        // Get all forms that have the price_per_meter input
        const allPriceInputs = document.querySelectorAll('input[name="price_per_meter"]');
        
        allPriceInputs.forEach(function(input) {
            // Get the form ID to identify which SPK this belongs to
            const formId = input.closest('form').id; // This will be something like "save-form-123"
            const spkId = formId.replace('save-form-', '');
            
            // Add event listener to update the corresponding modal input when the main form input changes
            input.addEventListener('input', function() {
                const modalInput = document.getElementById('price_per_meter_' + spkId);
                if (modalInput) {
                    modalInput.value = this.value;
                }
            });
        });
    });
</script>
@endpush