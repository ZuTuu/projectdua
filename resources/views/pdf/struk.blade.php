<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Belanja - Paw Center</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Courier New', Courier, monospace; /* Pake font monospace biar vibe struk kasirnya dapet banget */
            color: #111827; 
            margin: 0; 
            padding: 0; 
            background-color: #f3f4f6;
        }
        
        /* Membuat wrapper kertas struk memanjang di tengah halaman A4 */
        .receipt-wrapper {
            max-width: 420px;
            margin: 20px auto;
            background: #ffffff;
            padding: 25px 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            position: relative;
        }

        .brand-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .brand-name {
            font-size: 24px;
            font-weight: bold;
            color: #36005E; /* Warna ungu utama Paw Center */
            margin: 0;
            letter-spacing: 1px;
        }
        .brand-sub {
            font-size: 12px;
            color: #4b5563;
            margin: 3px 0 0 0;
        }

        /* Garis putus-putus khas struk belanja kasir */
        .divider {
            border-top: 2px dashed #36005E;
            margin: 15px 0;
            height: 0;
            width: 100%;
        }

        .info-table {
            width: 100%;
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .items-section {
            width: 100%;
            margin-top: 10px;
        }
        .item-row {
            margin-bottom: 12px;
            font-size: 13px;
        }
        .item-name {
            font-weight: bold;
            color: #111827;
        }
        .item-details {
            display: flex;
            justify-content: space-between;
            color: #4b5563;
            margin-top: 2px;
            padding-left: 5px;
        }

        .total-section {
            width: 100%;
            font-size: 14px;
            margin-top: 10px;
        }
        .flex-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #36005E;
            border-top: 1px dashed #6b7280;
            border-bottom: 1px dashed #6b7280;
            padding: 8px 0;
            margin-top: 5px;
        }

        .footer-section {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 30px;
            line-height: 1.4;
        }
        .footer-section strong {
            color: #36005E;
        }
    </style>
</head>
<body>

    <div class="receipt-wrapper">
        
        <div class="brand-section">
            <h1 class="brand-name">PAW CENTER</h1>
            <p class="brand-sub">D&F Pet Shop & Clinic</p>
            <p style="font-size: 11px; color: #9ca3af; margin: 2px 0 0 0;">Subang, West Java</p>
        </div>

        <div class="divider"></div>

        <table class="info-table">
            <tr>
                <td style="width: 35%;">No. Struk</td>
                <td>: #TRX-{{ $transaksi->id }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $transaksi->created_at->format('d M Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>: {{ $user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Pengiriman</td>
                <td>: {{ $transaksi->metode_pengiriman == 'delivery' ? 'Kirim ke Rumah' : 'Ambil di Toko' }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="items-section">
            @foreach($transaksi->detilProduk as $detil)
            <div class="item-row">
                <div class="item-name">{{ strtoupper($detil->produk->nama_produk ?? 'Produk Terhapus') }}</div>
                <div class="item-details">
                    <span>{{ $detil->jumlah }} x Rp {{ number_format($detil->harga_satuan ?? 0, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format(($detil->harga_satuan * $detil->jumlah), 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <div class="total-section">
            <div class="flex-row">
                <span>TOTAL ITEM</span>
                <span>{{ $transaksi->detilProduk->sum('jumlah') }} Pcs</span>
            </div>
            
            {{-- 👉 FITUR UTAMA: Menampilkan Metode Pembayaran --}}
            <div class="flex-row" style="color: #4b5563;">
                <span>METODE BAYAR</span>
                <span style="font-weight: bold; color: #36005E;">
                    {{ strtoupper($transaksi->metode_pembayaran ?? 'QRIS / TRANSFER') }}
                </span>
            </div>
            
            <div class="flex-row" style="color: #15803d; font-weight: bold;">
                <span>STATUS</span>
                <span>PAID (LUNAS)</span>
            </div>

            <div class="flex-row grand-total">
                <span>TOTAL AKHIR</span>
                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer-section">
            *** TERIMA KASIH ***<br>
            telah berbelanja di <strong>Paw Center</strong><br>
            Semoga anabul sehat selalu ya! 🐾
        </div>

    </div>

</body>
</html>