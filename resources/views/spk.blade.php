@php
// Menentukan apakah form dalam mode edit atau create. Ini adalah kunci utama.
$isEdit = isset($spk);
@endphp

@extends('layouts.app')

@section('content')
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    {{-- Judul halaman dinamis --}}
    <h3 class="text-dark mb-0">{{ $isEdit ? 'Edit Surat Perintah Kerja (SPK)' : 'Buat Surat Perintah Kerja (SPK)' }}</h3>
</div>

{{-- Notifikasi untuk success atau error --}}
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
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Form utama untuk data SPK --}}
{{-- Action form dinamis, akan mengarah ke 'store' atau 'update' --}}
<form method="POST" action="{{ $isEdit ? route('spk.update', $spk->id) : route('spk.store') }}" enctype="multipart/form-data">
    @csrf
    {{-- Menambahkan method PUT khusus untuk mode edit --}}
    @if($isEdit)
    @method('PUT')
    @endif
    <div class="row">
        <div class="col-lg-4">
            {{-- Bagian upload foto --}}
            <div class="card mb-3">
                <div class="card-body text-center shadow">
                    {{-- Menampilkan gambar yang ada jika mode edit, atau placeholder jika mode create --}}
                    <img id="image-preview" class="mb-3 img-fluid" src="{{ $isEdit && $spk->design_image_path ? asset('storage/' . $spk->design_image_path) : 'https://via.placeholder.com/269x356' }}" alt="Design Preview" style="max-height: 356px; object-fit: cover;">
                    <div class="mb-3">
                        <input type="file" name="design_image" class="form-control" onchange="previewImage(event)">
                    </div>
                </div>
            </div>
            {{-- Bagian keterangan --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary fw-bold m-0">Keterangan</h6>
                </div>
                <div class="card-body">
                    {{-- Mengisi value secara dinamis --}}
                    <textarea class="form-control" name="description" rows="7" placeholder="Tambahkan keterangan...">{{ old('description', $spk->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="text-primary fw-bold m-0">Informasi Order</h6>
                    {{-- Menampilkan status hanya di mode edit --}}
                    @if($isEdit)
                    <span class="badge bg-info text-dark">Status: {{ $spk->status }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="nomor"><strong>Nomor SPK</strong></label>
                            <input class="form-control" type="text" id="nomor" value="{{ $isEdit ? $spk->spk_number : 'Otomatis' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tanggal-masuk"><strong>Tanggal Masuk</strong></label>
                            <input class="form-control" type="text" id="tanggal-masuk" value="{{ $isEdit ? \Carbon\Carbon::parse($spk->entry_date)->translatedFormat('l, d F Y') : \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="konsumen"><strong>Nama Konsumen</strong></label>
                            <input class="form-control" type="text" id="konsumen" name="customer_name" placeholder="Nama Konsumen" value="{{ old('customer_name', $spk->customer_name ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="order"><strong>Nama Order</strong></label>
                            <input class="form-control" type="text" id="order" name="order_name" placeholder="Nama Order" value="{{ old('order_name', $spk->order_name ?? '') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tanggal-kirim"><strong>Tanggal Kirim</strong></label>
                            <input class="form-control" type="date" id="tanggal-kirim" name="delivery_date" value="{{ old('delivery_date', $spk->delivery_date ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="bahan"><strong>Bahan</strong></label>
                            <input class="form-control" type="text" id="bahan" name="material" placeholder="Bahan" value="{{ old('material', $spk->material ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Ukuran & Jumlah</strong></label>
                        <div class="row g-2" id="size-inputs">
                            @php
                            $sizes = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
                            // Mengambil data ukuran yang tersimpan jika mode edit
                            $spkSizes = $isEdit ? $spk->spkSizes->pluck('quantity', 'size') : collect();
                            @endphp
                            @foreach ($sizes as $size)
                            <div class="col-md-2 col-6">
                                <label class="form-label">{{ $size }}</label>
                                <input class="form-control size-input" type="number" name="sizes[{{ $size }}]" min="0" value="{{ old('sizes.'.$size, $spkSizes[$size] ?? 0) }}">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="total"><strong>Total Order</strong></label>
                            <input class="form-control" type="number" id="total" name="total_qty" placeholder="0" value="{{ old('total_qty', $spk->total_qty ?? 0) }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-5">
                <div class="card-header py-3">
                    <h6 class="text-primary fw-bold m-0">Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            {{-- Teks tombol dinamis --}}
                            <button class="btn btn-info w-100" type="submit">
                                <i class="fas fa-save me-1"></i> {{ $isEdit ? 'Update SPK' : 'Simpan SPK Baru' }}
                            </button>
                        </div>
                        @if($isEdit)
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-success w-100" type="button" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print SPK
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Form Progress Pengerjaan (HANYA MUNCUL DI MODE EDIT) --}}
@if($isEdit)
<div class="card shadow mb-5">
    <div class="card-header py-3">
        <h6 class="text-primary fw-bold m-0">Progress Pengerjaan</h6>
    </div>
    <div class="card-body">
        <form id="progressForm" action="{{ route('spk.update_status', $spk->id) }}" method="POST">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-2 col-6">
                    <div class="text-center p-2 border rounded">
                        <label class="form-check-label d-block mb-2"><strong>Design</strong></label>
                        <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                            <input class="form-check-input" type="checkbox" name="is_design_done" {{ $spk->is_design_done ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="text-center p-2 border rounded">
                        <label class="form-check-label d-block mb-2"><strong>Print</strong></label>
                        <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                            <input class="form-check-input" type="checkbox" name="is_print_done" {{ $spk->is_print_done ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="text-center p-2 border rounded">
                        <label class="form-check-label d-block mb-2"><strong>Press</strong></label>
                        <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                            <input class="form-check-input" type="checkbox" name="is_press_done" {{ $spk->is_press_done ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="text-center p-2 border rounded">
                        <label class="form-check-label d-block mb-2"><strong>Delivery</strong></label>
                        <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                            <input class="form-check-input" type="checkbox" name="is_delivery_done" {{ $spk->is_delivery_done ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><strong>Total Meter Kain</strong></label>
                    <input type="number" step="0.01" class="form-control" name="total_meter" placeholder="Contoh: 150.5" value="{{ $spk->total_meter }}">
                </div>
            </div>
            <hr>
            <div class="row mt-4">
                <div class="col-md-4 mb-2">
                    <button class="btn btn-info w-100" type="submit" name="action" value="update_progress">
                        <i class="fas fa-sync-alt me-1"></i>Update Progress
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-warning w-100" type="submit" name="action" value="close_order">
                        <i class="fas fa-check-circle me-1"></i>Close Order
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-danger w-100" type="submit" name="action" value="reject_order">
                        <i class="fas fa-ban me-1"></i>Reject Order
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Script untuk kalkulasi total order otomatis
    document.addEventListener('DOMContentLoaded', function() {
        const sizeInputsContainer = document.getElementById('size-inputs');
        if (sizeInputsContainer) {
            const totalInput = document.getElementById('total');

            function calculateTotal() {
                let total = 0;
                const inputs = sizeInputsContainer.querySelectorAll('.size-input');
                inputs.forEach(function(input) {
                    total += Number(input.value) || 0;
                });
                totalInput.value = total;
            }

            sizeInputsContainer.addEventListener('input', calculateTotal);
            calculateTotal(); // Panggil saat halaman dimuat
        }
    });

    // Script untuk preview gambar
    function previewImage(event) {
        if (event.target.files && event.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('image-preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush