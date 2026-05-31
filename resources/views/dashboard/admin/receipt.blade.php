@extends('layouts.app')

@section('title', 'Struk Penjualan')

@section('content')
<style>
    /* Menghilangkan style default card bawaan dashboard khusus halaman ini */
    .card, .main-card, .dashboard-card {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Wajib untuk ukuran yang akurat, agar padding tidak menambah total width */
    .receipt-container * {
        box-sizing: border-box;
    }

    /* Wrapper khusus untuk layar */
    .receipt-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        width: 100%;
        background-color: transparent;
    }

    /* Container utama struk di layar */
    .receipt-container {
        max-width: 80mm;
        width: 80mm;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: block;
        color: #333;
    }

    .receipt-header {
        background: linear-gradient(135deg, #800080 0%, #a020f0 100%);
        color: white;
        padding: 15px 10px;
        text-align: center;
        border-bottom: 2px dashed rgba(255, 255, 255, 0.3);
    }

    .receipt-header h1 {
        font-size: 16px;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .receipt-header p {
        font-size: 11px;
        opacity: 0.95;
        margin-bottom: 3px;
    }

    .receipt-body {
        padding: 12px 10px;
    }

    .receipt-section {
        margin-bottom: 12px;
    }

    .receipt-section-title {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        color: #800080;
        margin-bottom: 8px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e0e0e0;
        letter-spacing: 0.5px;
    }

    .receipt-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        margin-bottom: 5px;
    }

    .receipt-info-row label {
        color: #666;
        font-weight: 500;
    }

    .receipt-info-row .value {
        font-weight: 600;
        text-align: right;
    }

    .receipt-items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 10px;
    }

    .receipt-items-table thead {
        border-bottom: 1px solid #e0e0e0;
    }

    .receipt-items-table th {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        color: #800080;
        padding: 5px 0;
        text-align: left;
    }

    .receipt-items-table th.right,
    .receipt-items-table td.right {
        text-align: right;
    }

    /* Menggunakan persen agar kolom tabel fleksibel dan tidak pecah saat diprint */
    .receipt-items-table th:nth-child(1) { width: 40%; }
    .receipt-items-table th:nth-child(2) { width: 15%; text-align: center; }
    .receipt-items-table td:nth-child(2) { text-align: center; }
    .receipt-items-table th:nth-child(3) { width: 20%; }
    .receipt-items-table th:nth-child(4) { width: 25%; }

    .receipt-items-table tbody tr {
        border-bottom: 1px dotted #f0f0f0;
    }

    .receipt-items-table td {
        padding: 5px 0;
        vertical-align: top;
    }

    .receipt-items-table .product-name {
        font-weight: 600;
        word-wrap: break-word;
    }

    .receipt-summary {
        background-color: #f9f9f9;
        padding: 10px;
        border-radius: 4px;
        border-left: 3px solid #800080;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 5px;
    }

    .summary-row label {
        color: #666;
        font-weight: 500;
    }

    .summary-row .value {
        font-weight: 600;
    }

    .summary-row.total {
        font-size: 13px;
        padding-top: 8px;
        margin-top: 8px;
        border-top: 1px solid #ddd;
    }

    .summary-row.total label,
    .summary-row.total .value {
        color: #800080;
        font-weight: 900;
    }

    .payment-method, .kembalian-section {
        padding: 8px;
        border-radius: 4px;
        margin-top: 8px;
        font-size: 11px;
    }

    .payment-method {
        background-color: #f0f0f0;
    }

    .kembalian-section {
        background-color: #e8f5e9;
        border-left: 3px solid #28a745;
    }

    .payment-method label, .kembalian-section label {
        display: block;
        margin-bottom: 3px;
        font-weight: 600;
    }

    .payment-method .value { color: #800080; font-weight: 700; font-size: 12px; }
    .kembalian-section .value { color: #28a745; font-weight: 700; font-size: 12px; }

    .receipt-footer {
        background-color: #f5f5f5;
        padding: 12px 10px;
        text-align: center;
        border-top: 1px dashed #ddd;
    }

    .receipt-footer p {
        font-size: 10px;
        color: #666;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .receipt-footer-text {
        font-size: 11px;
        color: #800080;
        font-weight: 700;
        margin-top: 6px;
        padding: 6px;
        background-color: white;
        border-radius: 4px;
        border: 1px solid #e0e0e0;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        width: 80mm;
    }

    .btn {
        flex: 1;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
    }

    .btn-print { background: linear-gradient(135deg, #800080 0%, #a020f0 100%); color: white; }
    .btn-print:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(128, 0, 128, 0.3); }
    .btn-next { background-color: #f0f0f0; color: #333; border: 1px solid #ddd; }
    .btn-next:hover { background-color: #e0e0e0; }


    /* =========================================
       CSS PRINT KHUSUS UNTUK PRINTER THERMAL
       ========================================= */
    @media print {
        /* Set standar margin kertas printer jadi 0 dan ukuran 80mm */
        @page {
            margin: 0;
            size: 80mm auto;
        }

        /* Sembunyikan elemen dashboard dan tombol */
        .sidebar, .sidebar-header, .menu-items, .logout-btn-sidebar,
        .header, [class*="topbar"], [class*="navbar"], nav, .action-buttons, .top-nav {
            display: none !important;
        }

        /* Paksa body dan html rata kiri atas tanpa spasi */
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            background-color: white !important;
            width: 80mm !important;
        }

        /* Hilangkan Flexbox yang menengahkan struk, geser ke pojok kiri */
        .main-content, .content, .wrapper, .receipt-dashboard-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            display: block !important;
            background: white !important;
        }

        /* Buat Container mengambil 100% dari body kertas (80mm) */
        .receipt-container {
            width: 100% !important;
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important; /* Biar kalau panjang ga kepotong */
        }

        /* --- OPTIMASI MONOKROM UNTUK PRINTER THERMAL --- */
        .receipt-header {
            background: none !important;
            color: black !important;
            border-bottom: 2px dashed black !important;
            padding: 10px 5px !important;
        }

        .receipt-section-title,
        .receipt-items-table th,
        .summary-row.total label,
        .summary-row.total .value,
        .receipt-footer-text,
        .payment-method .value,
        .kembalian-section .value {
            color: black !important;
        }

        .receipt-summary, .payment-method, .kembalian-section, .receipt-footer {
            background: none !important;
            border: none !important;
            border-top: 1px dashed black !important;
            border-radius: 0 !important;
        }

        .receipt-footer-text {
            border: 1px dotted black !important;
        }

        /* Pertajam font agar lebih jelas di thermal */
        * {
            font-family: 'Courier New', Courier, monospace !important;
            color: black !important;
        }
    }
</style>

<div class="receipt-dashboard-wrapper">
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>🧾 STRUK PENJUALAN</h1>
            <p>D&F Pet Shop</p>
            <p>Struk #{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="receipt-body">
            <div class="receipt-section">
                <div class="receipt-section-title">Informasi Transaksi</div>
                <div class="receipt-info-row">
                    <label>No. Struk</label>
                    <span class="value">#{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="receipt-info-row">
                    <label>Tanggal & Jam</label>
                    <span class="value">{{ $transaksi->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-info-row">
                    <label>Kasir</label>
                    <span class="value">{{ $transaksi->user->name ?? 'Admin' }}</span>
                </div>
            </div>

            <div class="receipt-section">
                <div class="receipt-section-title">Detail Barang</div>
                <table class="receipt-items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th class="right">Harga</th>
                            <th class="right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi->detilProduk as $detail)
                        <tr>
                            <td class="product-name">{{ $detail->produk->nama_produk ?? 'N/A' }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td class="right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format($detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="receipt-section">
                <div class="receipt-summary">
                    <div class="summary-row">
                        <label>Subtotal</label>
                        <span class="value">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row total">
                        <label>Total Pembayaran</label>
                        <span class="value">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <div class="payment-method">
                    <label>💳 Metode Pembayaran</label>
                    <div class="value">
                        @if($transaksi->metode_pembayaran === 'cash')
                            💵 Tunai / COD
                        @elseif($transaksi->metode_pembayaran === 'transfer')
                            💳 Transfer / QRIS
                        @else
                            {{ ucfirst($transaksi->metode_pembayaran) }}
                        @endif
                    </div>
                </div>

                @if($transaksi->metode_pembayaran === 'cash' && $transaksi->kembalian)
                <div class="kembalian-section">
                    <label>💰 Kembalian</label>
                    <div class="value">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="receipt-footer">
            <p>✨ Terima kasih sudah berbelanja di D&F ✨</p>
            <div class="receipt-footer-text">
                Semoga hewan kesayangan Anda sehat dan bahagia!
            </div>
            <p style="margin-top: 15px; font-size: 11px;">
                {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>

    <div class="action-buttons">
        <button type="button" class="btn btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
        <a href="{{ route('admin.riwayat_pesanan') }}" class="btn btn-next">Kelola Transaksi →</a>
    </div>
</div>
@endsection
