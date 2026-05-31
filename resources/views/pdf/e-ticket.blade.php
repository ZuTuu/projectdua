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
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 15px; background: #ffffff; min-height: 90px; }
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

            {{-- Badge Status Reservasi --}}
            <div class="badge">Status: {{ strtoupper($reservasi->status) }}</div>

            {{-- Badge Status Pembayaran Dinamis --}}
            @if($reservasi->status === 'Selesai')
                <div class="badge" style="background: #edfcf2; color: #15803d; margin-left: 5px;">🔥 LUNAS</div>
            @else
                <div class="badge" style="background: #fef2f2; color: #b91c1c; margin-left: 5px;">⚠️ BELUM LUNAS</div>
            @endif
        </div>

        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; padding-right: 10px;">
                    <div class="card">
                        <div class="k">
                            @if(str_contains(strtolower($reservasi->nama_layanan), 'hotel') || str_contains(strtolower($reservasi->nama_layanan), 'penitipan'))
                                Kamar / Paket
                            @else
                                Layanan Klinik
                            @endif
                        </div>
                        <div class="v">{{ $reservasi->nama_layanan }}</div>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 10px;">
                    <div class="card">
                        <div class="k">
                            @if(str_contains(strtolower($reservasi->nama_layanan), 'hotel') || str_contains(strtolower($reservasi->nama_layanan), 'penitipan'))
                                Waktu Check-in
                            @else
                                Waktu Kedatangan
                            @endif
                        </div>
                        <div class="v">
                            {{ \Carbon\Carbon::parse($reservasi->tanggal)->format('d M Y') }}<br>
                            {{ substr($reservasi->waktu, 0, 5) }} WIB
                        </div>

                        @if(!empty($reservasi->tanggal_keluar))
                            <div class="k" style="margin-top: 10px; border-top: 1px dashed #e5e7eb; padding-top: 5px;">Tanggal Checkout</div>
                            <div class="v" style="color: #008080;">{{ \Carbon\Carbon::parse($reservasi->tanggal_keluar)->format('d M Y') }}</div>
                        @endif
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
                        <div class="v">{{ $reservasi->pet_name ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Logic Perhitungan Biaya --}}
        @php
            $harga_base = $reservasi->harga_total ?? 0;
            // Tembak data biaya tambahan lewat relasi rekamMedis
            // Pakai fungsi optional() biar sistem nggak error/crash kalau e-tiketnya dicetak sebelum dokter ngisi rekam medis
            $biaya_tambahan = optional($reservasi->rekamMedis)->biaya_tambahan ?? 0;

            $grand_total = $harga_base + $biaya_tambahan;
            $dp = $reservasi->harga_dp > 0 ? $reservasi->harga_dp : ($harga_base * 0.2);
            $sisa_bayar = $grand_total - $dp;
        @endphp

        <table class="table">
            <thead>
                <tr>
                    <th>Rincian Pembayaran</th>
                    <th style="text-align:right;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Harga Layanan Dasar</td>
                    <td style="text-align:right; font-weight:700;">Rp {{ number_format($harga_base, 0, ',', '.') }}</td>
                </tr>

                @if($biaya_tambahan > 0)
                <tr>
                    <td>Biaya Tambahan (Tindakan/Obat Dokter)</td>
                    <td style="text-align:right; font-weight:700; color: #d97706;">+ Rp {{ number_format($biaya_tambahan, 0, ',', '.') }}</td>
                </tr>
                @endif

                <tr>
                    <td>Uang Muka / DP (Sudah Bayar)</td>
                    <td style="text-align:right; font-weight:700; color: #16a34a;">- Rp {{ number_format($dp, 0, ',', '.') }}</td>
                </tr>

                <tr style="background: #f9fafb;">
                    <td style="font-weight: bold;">Sisa Bayar (Di Kasir Klinik)</td>
                    <td style="text-align:right; font-weight:800; color: #36005E; font-size: 16px;">
                        Rp {{ number_format($sisa_bayar, 0, ',', '.') }}
                        @if($reservasi->status === 'Selesai')
                            <span style="color: #15803d; font-size: 13px; font-weight: bold; margin-left: 5px;">(LUNAS)</span>
                        @endif
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
