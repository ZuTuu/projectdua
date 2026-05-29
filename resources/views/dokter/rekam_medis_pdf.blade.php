<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekam Medis Dokter</title>
    <style>
        /* PENTING: Gunakan font DejaVu Sans agar karakter Unicode/Emoji tidak jadi ? */
        body { font-family: DejaVu Sans, sans-serif; }

        .card-dokter { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #E6E6FA; box-shadow: 0 4px 6px rgba(0,0,0,0.02); margin-bottom: 20px; }
        .section-title { color: #800080; font-size: 16px; font-weight: bold; margin-bottom: 15px; border-bottom: 2px solid #f3e8ff; padding-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        td.k { width: 180px; color:#6b7280; font-size:13px; font-weight: 500; text-transform: uppercase; }
        .v { font-weight: 600; color: #1e1b4b; }
        .badge-status { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; }
        .btn-secondary { display:inline-block; padding:10px 16px; border-radius:10px; background:#f1f5f9; color:#334155; text-decoration:none; font-weight: bold; border: 1px solid #cbd5e1; }
        .btn-primary { display:inline-block; padding:10px 16px; border-radius:10px; background:#800080; color:#fff; text-decoration:none; font-weight: bold; }
        .grid-billing { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; background: #faf5ff; padding: 15px; border-radius: 8px; border: 1px solid #e9d5ff; margin-top: 10px; }
        .billing-box { text-align: left; }
        .billing-label { font-size: 11px; color: #6d28d9; text-transform: uppercase; font-weight: bold; }
        .billing-value { font-size: 15px; font-weight: 700; color: #2e1065; margin-top: 4px; }
    </style>
</head>
<body>
    <h2>Rekam Medis Dokter</h2>
    <div class="container">

    <div class="card-dokter">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px; border-bottom: 2px dashed #e2e8f0; padding-bottom: 15px;">
            <div>
                <h2 style="margin:0 0 6px 0; color: #1e1b4b;">🧾 Resume Lengkap Kunjungan Pasien</h2>
                <div style="color: #6b7280; font-size: 13px;">ID Rekam Medis: <strong>#RM-{{ $rekamMedis->id }}</strong></div>
            </div>
            <div>
                @php
                    $status = $rekamMedis->reservasi->status ?? 'Selesai';
                    $badgeStyle = 'background: #e0f2fe; color: #0284c7;';
                    if($status == 'Selesai') $badgeStyle = 'background: #dcfce7; color: #16a34a;';
                @endphp
                <span class="badge-status" style="{{ $badgeStyle }}">Status Reservasi: {{ $status }}</span>
            </div>
        </div>

        <div class="section-block" style="margin-bottom: 35px;">
            <div class="section-title">1. Detail Layanan & Billing</div>
            <table>
                <tr>
                    <td class="k">Layanan Unit</td>
                    <td><span class="v">{{ $rekamMedis->reservasi->nama_layanan ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td class="k">Jadwal Kedatangan</td>
                    <td>
                        @if(isset($rekamMedis->reservasi->tanggal))
                            <span class="v">{{ \Carbon\Carbon::parse($rekamMedis->reservasi->tanggal)->format('d M Y') }}</span>
                            | Jam {{ \Carbon\Carbon::parse($rekamMedis->reservasi->waktu ?? now())->format('H:i') }} WIB
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            <div class="grid-billing">
                <div class="billing-box">
                    <div class="billing-label">Tarif Layanan Dasar</div>
                    <div class="billing-value">Rp {{ number_format($rekamMedis->reservasi->harga_total ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="billing-box">
                    <div class="billing-label">Biaya Tambahan (Obat, dll)</div>
                    <div class="billing-value" style="color: #b45309;">Rp {{ number_format($rekamMedis->biaya_tambahan ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="billing-box">
                    <div class="billing-label">DP Lunas (20%)</div>
                    <div class="billing-value" style="color: #16a34a;">Rp {{ number_format($rekamMedis->reservasi->dp ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="billing-box">
                    <div class="billing-label">Sisa Wajib Bayar</div>
                    <div class="billing-value" style="color: #e11d48;">Rp {{ number_format(($rekamMedis->reservasi->harga_total ?? 0) + ($rekamMedis->biaya_tambahan ?? 0) - ($rekamMedis->reservasi->dp ?? 0), 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="section-block" style="margin-bottom: 35px;">
            <div class="section-title">2. Informasi Anabul & Keluhan</div>
            <table>
                <tr>
                    <td class="k">Nama Anabul</td>
                    <td><span class="v">{{$rekamMedis->reservasi->pet_name ?? 'N/A' }}</span></td>
                </tr>
                <tr>
                    <td class="k">Spesies</td>
                    <td>{{ $rekamMedis->hewan->jenis_hewan ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="k">Pet Owner</td>
                    <td>{{ $rekamMedis->hewan->user->name ?? $rekamMedis->reservasi->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="k" style="color: #b45309; font-weight: bold;">Keluhan Utama</td>
                    <td style="white-space: pre-line; background: #fffbeb; padding: 14px; border-radius: 8px; border-left: 4px solid #f59e0b; color: #451a03; font-size: 14px;">{{ $rekamMedis->reservasi->keluhan ?? 'Tidak ada catatan.' }}</td>
                </tr>
            </table>
        </div>

        <div class="section-block" style="margin-bottom: 25px;">
            <div class="section-title">3. Hasil Rekam Medis & Diagnosis</div>
            <table>
                <tr>
                    <td class="k">Dokter Pemeriksa</td>
                    <td>drh. {{ $rekamMedis->nama_dokter ?? $rekamMedis->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="k">Diagnosa Medis</td>
                    <td><span style="background: #fef2f2; color: #991b1b; padding: 6px 10px; border-radius: 6px; font-size: 13px; font-weight: bold; border: 1px solid #fee2e2;">{{ $rekamMedis->diagnosa ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td class="k">Tindakan / Terapi</td>
                    <td style="white-space: pre-line; font-weight: 500;">{{ $rekamMedis->tindakan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="k">Catatan & Resep</td>
                    <td style="white-space: pre-line; background: #f0fdf4; padding: 14px; border-radius: 8px; border-left: 4px solid #22c55e; color: #14532d; font-size: 14px;">{{ $rekamMedis->catatan ?? $rekamMedis->catatan_dokter ?? $rekamMedis->detail ?? 'Tidak ada catatan tambahan.' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>
