@extends('layouts.app')

@section('title', 'Laporan Medis Dokter - Paw Center')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .toolbar { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; margin-bottom: 18px; }
    .field { display:flex; flex-direction:column; gap:6px; }
    .label-soft { color:#6b7280; font-size:12px; }
    input[type="date"], button, select {
        padding: 10px 12px; border: 1px solid #ddd; border-radius: 10px;
    }
    .btn-primary { background:#800080; color:white; border:none; cursor:pointer; font-weight:700; }
    .btn-secondary { background:#eee; color:#333; border:none; cursor:pointer; font-weight:700; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 18px; }
    .stat-card { background:#fff; padding: 18px; border-radius: 12px; border: 1px solid #E6E6FA; box-shadow: 0 2px 6px rgba(0,0,0,0.02); }
    .stat-title { color:#6b7280; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: 900; color:#111827; }
    .table-wrap { overflow-x:auto; background:#fff; border-radius: 12px; border: 1px solid #E6E6FA; }
    table { width:100%; border-collapse: collapse; }
    th, td { border-bottom: 1px solid #f3f4f6; padding: 12px; text-align:left; font-size: 13px; }
    th { color:#6b7280; font-size:12px; text-transform:uppercase; letter-spacing:.02em; }
    .card-box { background:#fff; border-radius: 12px; border: 1px solid #E6E6FA; box-shadow: 0 2px 6px rgba(0,0,0,0.02); padding: 16px; margin-bottom: 16px; }
    .no-data { color:#9ca3af; text-align:center; padding: 20px 10px; }
    .print-actions { display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 16px; }
    .print-actions a { text-decoration:none; }
</style>

<div class="toolbar">
    <form method="GET" action="{{ route('dokter.laporan.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
        <div class="field">
            <div class="label-soft">Tanggal mulai</div>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}">
        </div>
        <div class="field">
            <div class="label-soft">Tanggal selesai</div>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}">
        </div>
        <button class="btn-primary" type="submit">Tampilkan</button>
        <a href="{{ route('dokter.laporan.index') }}" class="btn-secondary" style="display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:10px;">
            Reset
        </a>
    </form>
</div>

<div class="print-actions">
    <a href="{{ route('dokter.laporan.print', ['tanggal_mulai' => $tanggalMulai ?? null, 'tanggal_selesai' => $tanggalSelesai ?? null]) }}" class="btn-primary" style="padding:10px 14px; border-radius:10px;">
        🖨️ Cetak PDF
    </a>
</div>

<div class="grid">
    <div class="stat-card">
        <div class="stat-title">Jumlah Pasien</div>
        <div class="stat-value">{{ $jumlahPasien ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Penyakit Terbanyak</div>
        <div class="stat-value" style="font-size:18px; line-height:1.2;">
            {{ $penyakitTerbanyak ?? '-' }}
            <div style="font-size:12px; color:#6b7280; font-weight:700; margin-top:6px;">
                {{ $penyakitTerbanyakJumlah ?? 0 }} kasus
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Penggunaan Obat (Top)</div>
        <div class="stat-value" style="font-size:18px; line-height:1.2;">
            {{ isset($obatTeratas) && count($obatTeratas) ? count($obatTeratas) : 0 }} item
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Rekam Medis</div>
        <div class="stat-value">{{ $rekamMedisCount ?? 0 }}</div>
    </div>
</div>

<div class="card-box">
    <h3 style="margin:0 0 10px 0; font-size:16px; color:#111827;">📈 Diagram</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
        <div>
            <div style="color:#6b7280; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.02em; margin-bottom:8px;">
                Tren Rekam Medis per Hari
            </div>
            <canvas id="trendChart" height="120"></canvas>
        </div>
        <div>
            <div style="color:#6b7280; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.02em; margin-bottom:8px;">
                Top Diagnosa (5)
            </div>
            <canvas id="topDiagnosaChart" height="120"></canvas>
        </div>
    </div>
</div>

<div class="card-box">
    <h3 style="margin:0 0 10px 0; font-size:16px; color:#111827;">💊 Penggunaan Obat</h3>
    <div class="table-wrap">
        @if(isset($obatTeratas) && $obatTeratas->count())
            <table>
                <thead>
                    <tr>
                        <th>Obat</th>
                        <th>Jumlah Pemakaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($obatTeratas as $item)
                        <tr>
                            <td>{{ $item['obat'] ?? '-' }}</td>
                            <td>{{ $item['jumlah'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">Belum ada data obat pada rentang waktu ini.</div>
        @endif
    </div>
</div>

<div class="card-box">
    <h3 style="margin:0 0 10px 0; font-size:16px; color:#111827;">📝 Riwayat Tindakan</h3>
    <div class="table-wrap">
        @if(isset($riwayatTindakan) && count($riwayatTindakan))
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hewan</th>
                        <th>Pemilik</th>
                        <th>Diagnosa</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatTindakan as $row)
                        <tr>
                            <td style="white-space:nowrap;">{{ $row['tanggal'] ?? '-' }}</td>
                            <td>{{ $row['hewan'] ?? 'N/A' }}</td>
                            <td>{{ $row['pemilik'] ?? 'N/A' }}</td>
                            <td>{{ $row['diagnosa'] ?? '-' }}</td>
                            <td>{{ $row['tindakan'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td colspan="5" style="color:#6b7280;">
                                Resep: {{ $row['resep_obat'] ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">Belum ada riwayat tindakan pada rentang waktu ini.</div>
        @endif
    </div>
</div>

<script>
    (function(){
        const trendLabels = {!! json_encode($chartTanggalLabels ?? []) !!};
        const trendData = {!! json_encode($chartTrenData ?? []) !!};
        const topDiagLabels = {!! json_encode($chartTopDiagnosaLabels ?? []) !!};
        const topDiagData = {!! json_encode($chartTopDiagnosaData ?? []) !!};

        const trendCtx = document.getElementById('trendChart');
        const topCtx = document.getElementById('topDiagnosaChart');

        if (trendCtx) {
            new Chart(trendCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Jumlah Rekam Medis',
                        data: trendData,
                        borderColor: '#800080',
                        backgroundColor: 'rgba(128, 0, 128, 0.1)',
                        borderWidth: 2,
                        tension: 0.2,
                        fill: true
                    }]
                },
                options: { responsive:true, plugins:{ legend:{ display:false } } }
            });
        }

        if (topCtx) {
            new Chart(topCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: topDiagLabels,
                    datasets: [{
                        label: 'Kasus',
                        data: topDiagData,
                        backgroundColor: '#800080',
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive:true,
                    plugins:{ legend:{ display:false } },
                    scales: { y: { beginAtZero:true } }
                }
            });
        }
    })();
</script>
@endsection
