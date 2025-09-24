{{-- Menggunakan layout utama dari app.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- spk --}}
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="text-dark mb-0">Surat Perintah Kerja (SPK)</h3>
</div>

<form method="POST" action="{{ route('spk.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-4">
            {{-- Bagian upload foto --}}
            <div class="card mb-3">
                <div class="card-body text-center shadow">
                    <img class="mb-3 img-fluid" src="https://via.placeholder.com/269x356" alt="Design Preview">
                    <div class="mb-3">
                        <input type="file" name="design_image" class="btn btn-primary btn-sm" type="button">
                    </div>
                </div>
            </div>
            {{-- Bagian keterangan --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary fw-bold m-0">Keterangan</h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="description" rows="4" placeholder="Tambahkan keterangan..."></textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary fw-bold m-0">Informasi Order</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="nomor"><strong>Nomor SPK</strong></label>
                            <input class="form-control" type="text" id="nomor" value="SEP/15/0027/2025 (auto generated)" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tanggal-masuk"><strong>Tanggal Masuk</strong></label>
                            <input class="form-control" type="text" id="tanggal-masuk" value="{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}" readonly>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="konsumen"><strong>Nama Konsumen</strong></label>
                            <input class="form-control" type="text" id="konsumen" name="customer_name" placeholder="Nama Konsumen">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="order"><strong>Nama Order</strong></label>
                            <input class="form-control" type="text" id="order" name="order_name" placeholder="Nama Order">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tanggal-kirim"><strong>Tanggal Kirim</strong></label>
                            <input class="form-control" type="date" id="tanggal-kirim" name="delivery_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="bahan"><strong>Bahan</strong></label>
                            <input class="form-control" type="text" id="bahan" name="material" placeholder="Bahan">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Ukuran & Jumlah</strong></label>
                        <div class="row g-2">
                            <div class="col-md-2 col-6">
                                <label class="form-label">S</label>
                                <input class="form-control" type="number" name="sizes[S]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">M</label>
                                <input class="form-control" type="number" name="sizes[M]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">L</label>
                                <input class="form-control" type="number" name="sizes[L]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">XL</label>
                                <input class="form-control" type="number" name="sizes[XL]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">2XL</label>
                                <input class="form-control" type="number" name="sizes[2XL]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">3XL</label>
                                <input class="form-control" type="number" name="sizes[3XL]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">4XL</label>
                                <input class="form-control" type="number" name="sizes[4XL]" min="0">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label">5XL</label>
                                <input class="form-control" type="number" name="sizes[5XL]" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="total"><strong>Total Order</strong></label>
                            <input class="form-control" type="number" id="total" placeholder="0 (auto calculated)" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="text-primary fw-bold m-0">Aksi</h6>
        </div>
        <div class="card-body">
            <div class="row mt-4">
                <div class="col-md-6 mb-2">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fas fa-save me-1"></i>Simpan SPK
                    </button>
                </div>
                {{-- Tombol Close/Reject dan Print akan diimplementasikan belakangan untuk fungsi edit --}}
            </div>
        </div>
    </div>
</form>


<div class="card shadow mb-5">
    <div class="card-header py-3">
        <h6 class="text-primary fw-bold m-0">Progress Pengerjaan</h6>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-center mb-3">
            
            <!-- Design -->
            <div class="col-md-2 col-6">
                <div class="text-center p-2 border rounded">
                    <label class="form-check-label d-block mb-2" for="designSwitch"><strong>Design</strong></label>
                    <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                        <input class="form-check-input" type="checkbox" id="designSwitch">
                    </div>
                </div>
            </div>
            
            <!-- Print -->
            <div class="col-md-2 col-6">
                <div class="text-center p-2 border rounded">
                    <label class="form-check-label d-block mb-2 " for="printSwitch"><strong>Print</strong></label>
                    <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                        <input class="form-check-input" type="checkbox" id="printSwitch">
                    </div>
                    <input type="text" class="form-control form-control-sm mt-2" placeholder="Qty">
                </div>
            </div>
            
            <!-- Press -->
            <div class="col-md-2 col-6">
                <div class="text-center p-2 border rounded">
                    <label class="form-check-label d-block mb-2" for="pressSwitch"><strong>Press</strong></label>
                    <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                        <input class="form-check-input" type="checkbox" id="pressSwitch">
                    </div>
                </div>
            </div>
            
            <!-- Delivery -->
            <div class="col-md-2 col-6">
                <div class="text-center p-2 border rounded">
                    <label class="form-check-label d-block mb-2" for="deliverySwitch"><strong>Delivery</strong></label>
                    <div class="form-check form-switch d-flex justify-content-center" style="font-size: 1.5rem;">
                        <input class="form-check-input" type="checkbox" id="deliverySwitch">
                    </div>
                </div>
            </div>
            
            <!-- Close Button -->
            <div class="col-md-2 col-6 d-flex">
                <button class="btn btn-warning w-100" type="button">
                    <i class="fas fa-times-circle me-1"></i>Close
                </button>
            </div>
            
            <!-- Reject Button -->
            <div class="col-md-2 col-6 d-flex">
                <button class="btn btn-danger w-100" type="button">
                    <i class="fas fa-ban me-1"></i>Reject
                </button>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6 mb-2 mb-md-0">
                <button class="btn btn-primary w-100" type="button">
                    <i class="fas fa-print me-1"></i>Print SPK
                </button>
            </div>
            <div class="col-md-6">
                <button class="btn btn-success w-100" type="button">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
