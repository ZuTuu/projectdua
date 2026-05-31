@extends('layouts.app')

@section('title', 'Dashboard Owner - Paw Center')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #4A148C; font-weight: 700;">📊 Dashboard Owner - Laporan Operasional</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="{{ route('dashboard.owner') }}" style="display: flex; gap: 10px;">
                <select name="mode" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <option value="day" {{ $mode === 'day' ? 'selected' : '' }}>Harian</option>
                    <option value="month" {{ $mode === 'month' ? 'selected' : '' }}>Bulanan</option>
                    <option value="year" {{ $mode === 'year' ? 'selected' : '' }}>Tahunan</option>
                </select>
                <select name="count" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    @if($mode === 'day')
                        <option value="7" {{ (int)$count === 7 ? 'selected' : '' }}>7 Hari</option>
                        <option value="14" {{ (int)$count === 14 ? 'selected' : '' }}>14 Hari</option>
                        <option value="30" {{ (int)$count === 30 ? 'selected' : '' }}>30 Hari</option>
                    @elseif($mode === 'month')
                        <option value="6" {{ (int)$count === 6 ? 'selected' : '' }}>6 Bulan</option>
                        <option value="12" {{ (int)$count === 12 ? 'selected' : '' }}>12 Bulan</option>
                    @else
                        <option value="5" {{ (int)$count === 5 ? 'selected' : '' }}>5 Tahun</option>
                    @endif
                </select>
            </form>
            <a href="{{ route('owner.laporan.print', ['mode' => $mode, 'count' => $count]) }}" target="_blank" class="btn btn-primary" style="background: #8E24AA; border: none; font-weight: 600;">
                🖨️ Print Laporan
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #8E24AA;">
                <div class="card-body">
                    <h6 class="text-muted">Total Pendapatan</h6>
                    <h3 class="fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #4A148C;">
                <div class="card-body">
                    <h6 class="text-muted">Omzet Bersih</h6>
                    <h3 class="fw-bold">Rp {{ number_format($omzetBersih, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #2E7D32;">
                <div class="card-body">
                    <h6 class="text-muted">Transaksi Selesai</h6>
                    <h3 class="fw-bold">{{ $totalTransaksiSelesai }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #F57C00;">
                <div class="card-body">
                    <h6 class="text-muted">Produk Terjual</h6>
                    <h3 class="fw-bold">{{ $totalQtySold }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white"><strong>Tren Pendapatan ({{ $count }} {{ $modeDisplay }})</strong></div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white"><strong>Komposisi Penjualan Produk</strong></div>
                <div class="card-body">
                    <canvas id="produkChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white"><strong>Komposisi Reservasi Layanan</strong></div>
                <div class="card-body">
                    <canvas id="layananChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Line Chart Pendapatan
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($revenueData) !!},
                borderColor: '#8E24AA',
                backgroundColor: 'rgba(142, 36, 170, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });

    // 2. Pie Chart Produk Terlaris
    const ctxProd = document.getElementById('produkChart').getContext('2d');
    new Chart(ctxProd, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($pieLabels) !!},
            datasets: [{
                data: {!! json_encode($pieData) !!},
                backgroundColor: ['#4A148C', '#8E24AA', '#AB47BC', '#CE93D8', '#F3E5F5']
            }]
        }
    });

    // 3. Pie Chart Layanan Terlaris
    const ctxLay = document.getElementById('layananChart').getContext('2d');
    new Chart(ctxLay, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($topLayananPieLabels) !!},
            datasets: [{
                data: {!! json_encode($topLayananPieData) !!},
                backgroundColor: ['#1B5E20', '#388E3C', '#4CAF50', '#81C784', '#C8E6C9']
            }]
        }
    });
</script>
@endsection