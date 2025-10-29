@extends('layouts.app')

@section('content')

{{-- SPK-Close --}}
<!-- <div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja (SPK) - Closed</h3>
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

<div>
    <div class="card shadow">
        <div class="card-header py-3">
            <h4 class="text-primary m-0 fw-bold">Surat Perintah Kerja (SPK) - Closed</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table id="spk-close-view-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Order</th>
                            <th>Tanggal Closed</th>
                            <th>Nama Konsumen</th>
                            <th>Nama Order</th>
                            <th>QTY</th>
                            <th>Meter</th>
                            <th>Status</th>
                            <!-- <th>Harga @pieces</th>
                            <th>Harga @meter</th> -->
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
                            <td class="text-white text-center {{ $spk->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">{{ $spk->status }}</td>
                            <!-- <td>
                                {{ $spk->harga_per_piece ? 'Rp. ' . number_format($spk->harga_per_piece, 0, ',', '.') : 'Belum diisi' }}
                            </td>
                            <td>
                                {{ $spk->price_per_meter ? 'Rp. ' . number_format($spk->price_per_meter, 0, ',', '.') : 'Belum diisi' }}
                            </td> -->
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="7">Belum ada SPK yang ditutup atau ditolak.</td>
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
        $('#spk-close-view-table').DataTable({
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
    });
</script>
@endpush