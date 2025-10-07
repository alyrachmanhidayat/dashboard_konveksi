        @vite(['resources/css/print.css'])

    <div class="print-container">
        <div class="periode-title">
            PERIODE {{ \Carbon\Carbon::parse($spk->entry_date)->format('Y') }}
        </div>
        <table class="layout-table">
            <!-- ======================= HEADER ======================= -->
            <thead>
                <tr>
                    <th colspan="2">
                        <table class="header-table">
                            <tr>
                                <td class="spk-title">SPK</td>
                                <td class="no-spk">NO : {{ substr($spk->spk_number, -4) }}</td>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>

            <!-- ======================= BODY ======================= -->
            <tbody>
                <tr>
                    <!-- Kolom kiri -->
                    <td class="left-column">
                        <div>
                            <div class="spk-title">SPK</div>
                        </div>
                        <div class="image-section">
                            <div class="image-header">{{ $spk->customer_name }}</div>
                            @if($spk->design_image_path)
                            <img src="{{ asset('storage/' . $spk->design_image_path) }}" alt="Design">
                            @else
                            <div class="no-image">Tidak Ada Gambar</div>
                            @endif
                        </div>
                        <!-- Keterangan -->
                        <div class="keterangan-section">
                            <div class="title">KETERANGAN</div>
                            <div class="content">{{ $spk->description }}</div>
                        </div>
                    </td>

                    <!-- Kolom kanan -->
                    <td class="right-column">
                        <!-- Info Pesanan -->
                        <table class="info-table">
                            <tr>
                                <td>NAMA FILE</td>
                                <td>{{ $spk->order_name }}</td>
                            </tr>
                            <tr>
                                <td>NAMA PEMESAN</td>
                                <td>{{ $spk->customer_name }}</td>
                            </tr>
                            <tr>
                                <td>TANGGAL MASUK</td>
                                <td>{{ \Carbon\Carbon::parse($spk->entry_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>TANGGAL KIRIM</td>
                                <td>{{ \Carbon\Carbon::parse($spk->delivery_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>BAHAN</td>
                                <td>{{ $spk->material }}</td>
                            </tr>
                        </table>

                        <!-- Size -->
                        <div class="size-section">
                            <div class="title">SIZE</div>
                            <table class="size-table">
                                <tr>
                                    @foreach(['S','M','L','XL'] as $size)
                                    <td>
                                        <div class="size-label">{{ $size }}</div>
                                        <div class="size-qty">
                                            {{ $spk->spkSizes->where('size',$size)->first()->quantity ?? 0 }}
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach(['2XL','3XL'] as $size)
                                    <td>
                                        <div class="size-label">{{ $size }}</div>
                                        <div class="size-qty">
                                            {{ $spk->spkSizes->where('size',$size)->first()->quantity ?? 0 }}
                                        </div>
                                    </td>
                                    @endforeach
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Total Order -->
                        <div class="total-order-section">
                            TOTAL ORDER: {{ $spk->total_qty }}
                        </div>


                    </td>
                </tr>

                <!-- Baca dan pahami -->
                <tr>
                    <td colspan="2" class="baca-dan-pahami">BACA DAN PAHAMI SEBELUM BEKERJA</td>
                </tr>

                <!-- Progress Table -->
                <tr>
                    <td colspan="2">
                        <table class="progress-table">
                            <tr>
                                <td>CUTTING</td>
                                <td>PRINT</td>
                                <td>PRESS</td>
                                <td>JAHIT</td>
                                <td>PACKING</td>
                                <td>KIRIM</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>

            <!-- ======================= FOOTER ======================= -->
            <tfoot>
                <tr>
                    <td colspan="2">
                        <table class="footer-table">
                            <tr>
                                <td class="mengetahui">
                                    <p>Mengetahui:</p>
                                    <p class="admin">ADMIN</p>
                                    <div class="signature-line">...............................</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="footer-no">NO : {{ substr($spk->spk_number, -4) }}</td>
                                <td></td>
                                <td class="perhatian">Mohon diperhatikan TELITI dan CEK ULANG</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
