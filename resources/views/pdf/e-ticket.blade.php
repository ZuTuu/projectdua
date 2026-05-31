<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Ticket Reservasi - Paw Center</title>
    <style>
        /* CSS internal tetap di sini agar PDF bisa membaca gaya desainnya */
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; margin: 0; padding: 0; }
        .container { padding: 30px; }

        .header { border-bottom: 2px solid #36005E; padding-bottom: 15px; margin-bottom: 25px; }
        .title { font-size: 22px; font-weight: 800; color: #36005E; text-transform: uppercase; }
        .brand { font-size: 14px; color: #6b7280; margin-top: 5px; }

        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; background: #F7F1FF; color: #36005E; font-size: 12px; font-weight: 700; margin-top: 10px; }

        .info-grid { width: 100%; margin-top: 20px; border-spacing: 10px; margin-left: -10px; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 15px; background: #ffffff; }
        .k { font-size: 11px; color: #6b7280; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .v { font-size: 15px; font-weight: bold; color: #111827; }

        .table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .table th { background: #36005E; color: #ffffff; padding: 12px; text-align: left; font-size: 13px; }
        .table td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }

        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #eee; padding-top: 20px; }
        .footer strong { color: #36005E; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">E-Ticket Reservasi</div>
            <div class="brand">Paw Center - D&F Pet Shop & Clinic</div>
            <div class="badge">Status: TERKONFIRMASI</div>
        </div>

        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; padding-right: 10px;">
                    <div class="card">
                        <div class="k">Layanan Klinik</div>
                        <div class="v">{{ $reservasi->nama_layanan }}</div>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 10px;">
                    <div class="card">
                        <div class="k">Waktu Kedatangan</div>
                        <div class="v">
                            {{ \Carbon\Carbon::parse($reservasi->tanggal_reservasi)->format('d M Y') }}<br>
                            {{ $reservasi->waktu_reservasi }} WIB
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding-right: 10px; padding-top: 15px;">
                    <div class="card">
                        <div class="k">Nama Pemilik</div>
                        <div class="v">{{ $user->name ?? '-' }}</div>
                        <div style="margin-top:5px; font-size:12px; color:#6b7280;">No HP: {{ $user->no_hp ?? '-' }}</div>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 10px; padding-top: 15px;">
                    <div class="card">
                        <div class="k">Data Hewan (Anabul)</div>
                        <div class="v">{{ $reservasi->nama_hewan ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Rincian Pembayaran</th>
                    <th style="text-align:right;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Harga Total Layanan</td>
                    <td style="text-align:right; font-weight:700;">Rp {{ number_format($reservasi->harga_total ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Uang Muka / DP (Sudah Bayar)</td>
                    <td style="text-align:right; font-weight:700; color: #16a34a;">- Rp {{ number_format($reservasi->harga_dp ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr style="background: #f9fafb;">
                    <td style="font-weight: bold;">Sisa Bayar (Di Kasir Klinik)</td>
                    <td style="text-align:right; font-weight:800; color: #36005E; font-size: 16px;">
                        Rp {{ number_format(($reservasi->harga_total - $reservasi->harga_dp), 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Harap tunjukkan E-Ticket ini kepada petugas kasir saat Anda tiba di klinik.<br>
            Terima kasih telah mempercayakan kesehatan anabul Anda kepada <strong>Paw Center</strong>.
        </div>
    </div>
</body>
</html>
