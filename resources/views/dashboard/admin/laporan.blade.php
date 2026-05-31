@extends('layouts.app')

@section('title', 'Laporan Penjualan - Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content">
    <div class="admin-header">
        <h2>Laporan Penjualan - Admin</h2>
    </div>

    <div class="admin-card">
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
            <canvas id="revenueChart" height="120"></canvas>
        </div>
        <div class="admin-card">
            <h3>Pie Produk Terjual (Top 10)</h3>
            <canvas id="productPie" height="120"></canvas>
        </div>
    </div>

    <div class="grid-2">
        <div class="admin-card">
            <h3>Perbandingan Pemasukan: Produk vs Layanan</h3>
            <canvas id="comparisonChart" height="120"></canvas>
        </div>
        <div class="admin-card">
            <h3>Pie Layanan Terpopuler (Top 10)</h3>
            <canvas id="servicePie" height="120"></canvas>
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

<script>
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
                borderColor: '#36005E', // Warna ungu utama Paw Center
                backgroundColor: 'rgba(54, 0, 94, 0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
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

    // Grafik Perbandingan Produk vs Layanan
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
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Pie Chart Layanan Terpopuler
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
            responsive: true
        }
    });
</script>
@endsection
