@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    #area-laporan {
    height: auto !important;
    overflow: visible !important;
    }
    .pdf-section {
    break-inside: avoid;
    page-break-inside: avoid;
    }
    @media print {
        /* 1. Sembunyikan SEMUA kemungkinan sidebar, navbar, dan header */
        aside, nav, header, footer, .sidebar, #sidebar, .main-sidebar, .sidenav, .navbar, .topbar, .d-print-none, form {
            display: none !important;
        }

        /* 2. Reset paksa margin & padding yang nahan content ke tengah/kanan */
        html, body, #app, main, .main-content, .content-wrapper, .content, .container, .container-fluid {
            background-color: white !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
            position: relative !important;
            left: 0 !important;
            box-sizing: border-box !important;
        }

        /* 3. Biar layout grid natural tapi card ga kepotong pas ganti halaman */
        .admin-card, .stat-card, tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        /* 4. Pastikan warna chart & background badge solid */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }

    .pdf-mode .grid-2 {
    display: flex !important;
    flex-direction: column !important;
    gap: 20px !important;
}

.pdf-mode .grid-2 > .admin-card {
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    flex: none !important;
}

.pdf-mode canvas {
    width: 100% !important;
    max-width: 100% !important;
}

.pdf-mode table {
    width: 100% !important;
}

.pdf-mode .admin-card {
    overflow: visible !important;
}
</style>

<div class="content" id="area-laporan">
    <div class="admin-header">
        <h2>Laporan Penjualan</h2>
        <button
            id="btn-download-pdf"
            onclick="downloadLaporanPDF()"
            class="d-print-none"
            style="background: #36005E; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 8px; cursor: pointer;"
        >
            📄 Download PDF
        </button>
    </div>

    <div class="admin-card d-print-none">
        <h3>Filter Periode</h3>
        <form method="GET" action="{{ route('admin.laporan') }}">
            <div class="filter-row">
                <div>
                    <label>Mode</label>
                    <select name="mode" class="form-select" style="min-width: 200px;" onchange="this.form.submit()">
                        <option value="day" {{ $mode === 'day' ? 'selected' : '' }}>Harian (last {{ $count }} hari)</option>
                        <option value="month" {{ $mode === 'month' ? 'selected' : '' }}>Bulanan (last {{ $count }} bulan)</option>
                        <option value="year" {{ $mode === 'year' ? 'selected' : '' }}>Tahunan (last {{ $count }} tahun)</option>
                    </select>
                </div>

                <div>
                    <label>Jumlah periode</label>
                    <select name="count" class="form-select" style="min-width: 200px;">
                        <option value="7" {{ (int)$count === 7 ? 'selected' : '' }}>7</option>
                        <option value="14" {{ (int)$count === 14 ? 'selected' : '' }}>14</option>
                        <option value="30" {{ (int)$count === 30 ? 'selected' : '' }}>30</option>
                        <option value="6" {{ (int)$count === 6 ? 'selected' : '' }}>6</option>
                        <option value="12" {{ (int)$count === 12 ? 'selected' : '' }}>12</option>
                        <option value="5" {{ (int)$count === 5 ? 'selected' : '' }}>5</option>
                    </select>
                </div>

                <button class="btn-acc" type="submit">Tampilkan</button>
            </div>
        </form>
    </div>

    <div class="grid-stats">
        <div class="stat-card">
            <span class="title">Total Pendapatan</span>
            <span class="angka">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
        </div>
        <div class="stat-card">
            <span class="title">Total Produk Terjual</span>
            <span class="angka">{{ $totalQtySold }} Item</span>
        </div>
        <div class="stat-card">
            <span class="title">Top Produk</span>
            <span class="angka" style="font-size:20px;">{{ $topProductName ?? '-' }}</span>
        </div>
        <div class="stat-card">
            <span class="title">Total Layanan Terjual</span>
            <span class="angka">{{ $totalLayananTerjual }} Layanan</span>
        </div>
        <div class="stat-card">
            <span class="title">Top Layanan</span>
            <span class="angka" style="font-size:20px;">{{ $topServiceName ?? '-' }}</span>
        </div>
        <div class="stat-card">
            <span class="title">Pemasukan Produk</span>
            <span class="angka">Rp {{ number_format($pemasukkanProduk, 0, ',', '.') }}</span>
        </div>
        <div class="stat-card">
            <span class="title">Pemasukan Layanan</span>
            <span class="angka">Rp {{ number_format($pemasukkanLayanan, 0, ',', '.') }}</span>
        </div>
    </div>

<div class="grid-2">
        <div class="admin-card">
            <h3>Grafik Pendapatan (Roll Up: {{ $modeDisplay }})</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="admin-card">
            <h3>Pie Produk Terjual (Top 10)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="productPie"></canvas>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="admin-card">
            <h3>Perbandingan Pemasukan: Produk vs Layanan</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
        <div class="admin-card">
            <h3>Pie Layanan Terpopuler (Top 10)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="servicePie"></canvas>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h3>Top Produk</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Qty Terjual</th>
                        <th class="text-end">Pendapatan Produk (perkiraan)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $row)
                        <tr>
                            <td><strong>{{ $row->nama_produk }}</strong></td>
                            <td class="text-end">{{ $row->qty_sold }}</td>
                            <td class="text-end">Rp {{ number_format($row->revenue_est ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="muted" style="text-align:center;">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card">
        <h3>Top Layanan</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th class="text-end">Qty Terjual</th>
                        <th class="text-end">Pendapatan Layanan (perkiraan)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topServices as $row)
                        <tr>
                            <td><strong>{{ $row->nama_layanan }}</strong></td>
                            <td class="text-end">{{ $row->qty_sold }}</td>
                            <td class="text-end">Rp {{ number_format($row->revenue_est ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="muted" style="text-align:center;">Belum ada data layanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card">
        <h3>Ringkasan Pemasukan</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tipe Pemasukan</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Pemasukan Produk</strong></td>
                        <td class="text-end">Rp {{ number_format($pemasukkanProduk, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pemasukan Layanan</strong></td>
                        <td class="text-end">Rp {{ number_format($pemasukkanLayanan, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background-color: rgba(54, 0, 94, 0.1);">
                        <td><strong>Total Pemasukan</strong></td>
                        <td class="text-end"><strong>Rp {{ number_format($pemasukkanProduk + $pemasukkanLayanan, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
    {{-- AREA KHUSUS CETAK PDF (Tersembunyi di web, Muncul di PDF) --}}
    {{-- ======================================================== --}}
    <div id="cetak-pdf" style="display: none; padding: 20px; font-family: Helvetica, Arial, sans-serif; color: #333; background: #fff;">
        <div style="text-align: center; border-bottom: 2px solid #36005E; padding-bottom: 15px; margin-bottom: 20px;">
            <h1 style="color: #36005E; margin: 0; font-size: 24px; text-transform: uppercase;">Laporan Penjualan Paw Center</h1>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Periode: <strong>{{ $modeDisplay }} (Last {{ $count }})</strong></p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }} WIB</p>
        </div>

        {{-- 1. KOTAK RINGKASAN PENDAPATAN --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 15px; border: 1px solid #ddd; background: #faf5ff; text-align: center; width: 33%;">
                    <div style="font-size: 11px; color: #666; text-transform: uppercase; font-weight: bold;">Total Pendapatan</div>
                    <div style="font-size: 18px; font-weight: bold; color: #36005E; margin-top: 5px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </td>
                <td style="padding: 15px; border: 1px solid #ddd; background: #f0fdf4; text-align: center; width: 33%;">
                    <div style="font-size: 11px; color: #666; text-transform: uppercase; font-weight: bold;">Pemasukan Produk ({{ $totalQtySold }} Item)</div>
                    <div style="font-size: 18px; font-weight: bold; color: #16a34a; margin-top: 5px;">Rp {{ number_format($pemasukkanProduk, 0, ',', '.') }}</div>
                </td>
                <td style="padding: 15px; border: 1px solid #ddd; background: #eff6ff; text-align: center; width: 33%;">
                    <div style="font-size: 11px; color: #666; text-transform: uppercase; font-weight: bold;">Pemasukan Layanan ({{ $totalLayananTerjual }} Trx)</div>
                    <div style="font-size: 18px; font-weight: bold; color: #2563eb; margin-top: 5px;">Rp {{ number_format($pemasukkanLayanan, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        {{-- 2. GRAFIK CHART (Dikonversi otomatis jadi gambar saat cetak) --}}
        <div style="page-break-inside: avoid; margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 10px; text-align: center; border: 1px solid #eee;">
                        <h3 style="font-size: 13px; color: #36005E; margin-bottom: 10px;">📈 Grafik Pendapatan</h3>
                        <img id="pdf-revenueChart" style="width: 100%; max-height: 180px; object-fit: contain;" />
                    </td>
                    <td style="width: 50%; padding: 10px; text-align: center; border: 1px solid #eee;">
                        <h3 style="font-size: 13px; color: #36005E; margin-bottom: 10px;">📊 Perbandingan Produk vs Layanan</h3>
                        <img id="pdf-comparisonChart" style="width: 100%; max-height: 180px; object-fit: contain;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 10px; text-align: center; border: 1px solid #eee;">
                        <h3 style="font-size: 13px; color: #36005E; margin-bottom: 10px;">🍩 Pie Produk Terjual</h3>
                        <img id="pdf-productPie" style="width: 100%; max-height: 200px; object-fit: contain;" />
                    </td>
                    <td style="width: 50%; padding: 10px; text-align: center; border: 1px solid #eee;">
                        <h3 style="font-size: 13px; color: #36005E; margin-bottom: 10px;">🍩 Pie Layanan Terpopuler</h3>
                        <img id="pdf-servicePie" style="width: 100%; max-height: 200px; object-fit: contain;" />
                    </td>
                </tr>
            </table>
        </div>

        {{-- 3. TABEL TOP PRODUK & LAYANAN --}}
        <div style="page-break-inside: avoid;">
            <table style="width: 100%; border-collapse: collapse;" border="0">
                <tr>
                    <td style="width: 48%; vertical-align: top; padding-right: 10px;">
                        <h3 style="font-size: 14px; color: #36005E; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">📦 Top 10 Produk Terjual</h3>
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama Produk</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $row)
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->nama_produk }}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $row->qty_sold }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: center;">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 4%;"></td> {{-- Spacer --}}
                    <td style="width: 48%; vertical-align: top; padding-left: 10px;">
                        <h3 style="font-size: 14px; color: #36005E; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">🩺 Top 10 Layanan Klinik/Hotel</h3>
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama Layanan</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServices as $row)
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->nama_layanan }}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $row->qty_sold }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: center;">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

<script>
    // --- SETUP CHART.JS ---
    const revenueLabels = {!! json_encode($revenueLabels) !!};
    const revenueSeries = {!! json_encode($revenueData) !!};

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: revenueSeries,
                borderColor: '#36005E',
                backgroundColor: 'rgba(54, 0, 94, 0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const pieLabels = {!! json_encode($pieLabels) !!};
    const pieData = {!! json_encode($pieData) !!};
    const pieCtx = document.getElementById('productPie').getContext('2d');

    const colors = [
        '#36005E','#47127e','#5d1aa8','#7f2fff','#9a61ff',
        '#c59aff','#dcc2ff','#ede1ff','#7c3aed','#6d28d9'
    ];

    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: colors.slice(0, pieLabels.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true
        }
    });

    const comparisonLabels = {!! json_encode($comparisonLabels) !!};
    const comparisonData = {!! json_encode($comparisonData) !!};
    const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');

    new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: comparisonLabels,
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: comparisonData,
                backgroundColor: ['#7f2fff', '#06b6d4'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const serviceLabels = {!! json_encode($serviceLabels) !!};
    const serviceData = {!! json_encode($serviceData) !!};
    const serviceCtx = document.getElementById('servicePie').getContext('2d');

    const serviceColors = [
        '#06b6d4','#0891b2','#0e7490','#164e63','#155e75',
        '#1e3a8a','#1e40af','#1e3a8a','#0369a1','#0c4a6e'
    ];

    new Chart(serviceCtx, {
        type: 'pie',
        data: {
            labels: serviceLabels,
            datasets: [{
                data: serviceData,
                backgroundColor: serviceColors.slice(0, serviceLabels.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

// --- SETUP HTML2PDF ---
function downloadLaporanPDF() {
    const element = document.getElementById('cetak-pdf');
    const btn = document.getElementById('btn-download-pdf');

    // 1. FOTO SEMUA GRAFIK (CANVAS -> IMAGE)
    const chartIds = ['revenueChart', 'comparisonChart', 'productPie', 'servicePie'];
    chartIds.forEach(id => {
        const canvas = document.getElementById(id);
        const img = document.getElementById('pdf-' + id);
        
        // Kalau grafiknya ada, ubah jadi base64 image dan masukin ke src
        if (canvas && img) {
            img.src = canvas.toDataURL('image/png');
        }
    });

    // 2. MUNCULIN AREA CETAK SEMENTARA
    element.style.display = 'block';
    btn.innerHTML = '⏳ Sedang menyiapkan PDF...';
    btn.disabled = true;

    // 3. PROSES BIKIN PDF
    setTimeout(() => {
        html2pdf().set({
            margin: [10, 10, 10, 10],
            filename: 'Laporan-Penjualan-PawCenter.pdf',
            image: { type: 'jpeg', quality: 1.0 },
            html2canvas: { 
                scale: 2, 
                useCORS: true, 
                scrollY: 0 
            },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(element).save().then(() => {
            
            // 4. KEMBALIKAN KE KONDISI AWAL (SEMBUNYIKAN LAGI)
            element.style.display = 'none';
            btn.innerHTML = '📄 Download PDF';
            btn.disabled = false;
        });
    }, 500); // Tunggu setengah detik biar gambarnya ke-load sempurna
}
</script>
@endsection
