<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Medis Dokter - Paw Center</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111827; line-height: 1.35; }
        h2 { margin: 0 0 10px 0; font-size: 18px; }
        .meta { margin-bottom: 14px; color:#374151; font-size: 12px; }
        .grid { display:flex; gap:12px; flex-wrap:wrap; margin-bottom: 12px; }
        .box { border:1px solid #e5e7eb; padding:10px 12px; border-radius: 6px; flex: 1 1 220px; }
        .k { color:#6b7280; font-size: 11px; margin-bottom: 6px; text-transform: uppercase; letter-spacing:.02em; font-weight: 800; }
        .v { font-weight: 900; font-size: 18px; }
        .v-small { font-weight: 800; font-size: 12px; margin-top: 6px; color:#111827; }
        table { width:100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #f3f4f6; padding: 10px 10px; text-align:left; vertical-align: top; word-break: break-word; }
        th { background: #f9fafb; color:#6b7280; font-size: 11px; text-transform: uppercase; letter-spacing:.02em; }
        .section { margin-top: 14px; }
        .pre { white-space: pre-line; }
        .nowrap { white-space: nowrap; }
        .small { color:#6b7280; font-size: 11px; font-weight: 700; }
    </style>
</head>
<body>
    <h2>📊 Laporan Medis Dokter</h2>
    <div class="meta">
        Rentang waktu: <b>{{ $tanggalMulai ?? '-' }}</b> s/d <b>{{ $tanggalSelesai ?? '-' }}</b>
        &nbsp;|&nbsp; Total rekam medis: <b>{{ $rekamMedisCount ?? 0 }}</b>
    </div>

    <div class="grid">
        <div class="box">
            <div class="k">Jumlah Pasien</div>
            <div class="v">{{ $jumlahPasien ?? 0 }}</div>
        </div>

        <div class="box">
            <div class="k">Penyakit Terbanyak</div>
            <div class="v" style="font-size:14px; line-height:1.2;">{{ $penyakitTerbanyak ?? '-' }}</div>
            <div class="v-small">{{ $penyakitTerbanyakJumlah ?? 0 }} kasus</div>
        </div>

        <div class="box">
            <div class="k">Penggunaan Obat</div>
            <div class="v" style="font-size:14px; line-height:1.2;">
                @if(isset($obatTeratas) && count($obatTeratas)) {{ count($obatTeratas) }} item @else 0 item @endif
            </div>
            <div class="v-small">Top 10</div>
        </div>
    </div>

    <div class="section">
        <div class="k">💊 Penggunaan Obat (Top 10)</div>
        @if(isset($obatTeratas) && count($obatTeratas))
            <table>
                <thead>
                    <tr>
                        <th style="width:70%;">Obat</th>
                        <th style="width:30%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($obatTeratas as $item)
                    <tr>
                        <td>{{ $item['obat'] ?? '-' }}</td>
                        <td class="nowrap">{{ $item['jumlah'] ?? 0 }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="small">Belum ada data obat pada rentang waktu ini.</div>
        @endif
    </div>

    <div class="section">
        <div class="k">📝 Riwayat Tindakan</div>
        @if(isset($riwayatTindakan) && count($riwayatTindakan))
            <table>
                <thead>
                    <tr>
                        <th style="width:16%;">Tanggal</th>
                        <th style="width:16%;">Hewan</th>
                        <th style="width:18%;">Pemilik</th>
                        <th style="width:20%;">Diagnosa</th>
                        <th style="width:30%;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($riwayatTindakan as $row)
                    <tr>
                        <td class="nowrap">{{ $row['tanggal'] ?? '-' }}</td>
                        <td>{{ $row['hewan'] ?? 'N/A' }}</td>
                        <td>{{ $row['pemilik'] ?? 'N/A' }}</td>
                        <td>{{ $row['diagnosa'] ?? '-' }}</td>
                        <td>
                            <div>{{ $row['tindakan'] ?? '-' }}</div>
                            <div class="small" style="margin-top:6px;">Resep: {{ $row['resep_obat'] ?? '-' }}</div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="small">Belum ada riwayat tindakan pada rentang waktu ini.</div>
        @endif
    </div>
</body>
</html>
