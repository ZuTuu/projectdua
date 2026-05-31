@extends('layouts.app')

@section('title', 'Dashboard Pelanggan')

@section('content')
@if(session('success'))
    <div style="background-color: #d4edda; border-left: 5px solid #28a745; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
        ✅ <strong>Berhasil!</strong> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background-color: #f8d7da; border-left: 5px solid #dc3545; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
        ❌ <strong>Waduh!</strong> {{ session('error') }}
    </div>
@endif

<style>
    .grid-dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
    .stat-card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 5px solid #800080; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .stat-card h3 { color: #555; font-size: 16px; margin-bottom: 10px; margin-top: 0; }
    .stat-card .value { font-size: 28px; font-weight: bold; color: #800080; }
    .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow-x: auto; margin-bottom: 30px; }
    .table-custom { width: 100%; border-collapse: collapse; min-width: 600px; }
    .table-custom th { padding: 12px; border-bottom: 2px solid #E6E6FA; text-align: left; color: #555; }
    .table-custom td { padding: 12px; border-bottom: 1px solid #eee; color: #333; }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: bold; text-decoration: none; border: none; cursor: pointer; display: inline-block; }
</style>

<div style="margin-bottom: 25px;">
    <h2 style="color: #800080; margin: 0;">👋 Selamat Datang, {{ Auth::user()->name }}!</h2>
    <p style="color: #666; margin: 5px 0 0 0;">Berikut adalah ringkasan aktivitas dan riwayat transaksi anabul kesayanganmu.</p>
</div>

{{-- GRID STATISTIK COUNTER --}}
<div class="grid-dashboard">
    <div class="stat-card">
        <h3>🐶 Jumlah Anabul Saya</h3>
        <div class="value">{{ $petCount }} Ekor</div>
    </div>
    <div class="stat-card" style="border-left-color: #008080;">
        <h3>📅 Reservasi Aktif</h3>
        <div class="value" style="color: #008080;">{{ $activeReservationsCount }} Transaksi</div>
    </div>
    <div class="stat-card" style="border-left-color: #ffc107;">
        <h3>🛒 Keranjang Belanja</h3>
        <div class="value" style="color: #ffc107;">{{ $cartCount }} Produk</div>
    </div>
</div>

{{-- TABEL 1: RIWAYAT PENITIPAN KAMAR (PET HOTEL) --}}
<div style="margin-top: 30px; margin-bottom: 10px;">
    <h3 style="color: #800080; margin: 0; display: flex; align-items: center; gap: 8px;">🏨 Riwayat Penitipan Kamar (Pet Hotel)</h3>
</div>
<div class="table-container">
    <table class="table-custom">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kamar/Paket</th>
                <th>Nama Anabul</th>
                <th>Tgl Check-in</th>
                <th style="color: #008080;">Tgl Checkout</th>
                <th>Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($petHotel as $key => $hotel)
            <tr>
                <td>{{ ($petHotel->currentPage() - 1) * $petHotel->perPage() + $key + 1 }}</td>
                <td style="font-weight: bold; color: #800080;">{{ $hotel->nama_layanan }}</td>
                <td>{{ $hotel->pet_name }}</td>
                <td>{{ \Carbon\Carbon::parse($hotel->tanggal)->format('d M Y') }}</td>
                <td style="font-weight: bold; color: #008080;">
                    {{ $hotel->tanggal_keluar ? date('d M Y', strtotime($hotel->tanggal_keluar)) : '-' }}
                </td>
                <td>
                    @if($hotel->status == 'Menunggu Pembayaran')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #fff3cd; color: #856404;">⏳ {{ $hotel->status }}</span>
                    @elseif($hotel->status == 'Dikonfirmasi' || $hotel->status == 'Selesai')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d4edda; color: #155724;">✅ {{ $hotel->status }}</span>
                    @elseif($hotel->status == 'Dibatalkan')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #f8d7da; color: #721c24;">❌ {{ $hotel->status }}</span>
                    @else
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #e2e8f0; color: #475569;">🔔 {{ $hotel->status }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 5px;">
                        @if($hotel->status == 'Menunggu Pembayaran')
                            <a href="{{ route('reservasi.bayar', $hotel->id) }}" class="btn-action" style="background: #007bff; color: white;">💳 Bayar DP</a>
                        @endif

                        @if($hotel->status == 'Selesai')
                            <a href="{{ route('reservasi.e-ticket.download', $hotel->id) }}" class="btn-action" style="background: #28a745; color: white;">📄 E-Ticket</a>
                        @endif

                        @if(!in_array($hotel->status, ['Dibatalkan', 'Selesai']))
                            <form action="{{ route('reservasi.batal', $hotel->id) }}" method="POST" onsubmit="return mintaAlasanDP(event, this, '{{ $hotel->nama_layanan }}', '{{ $hotel->status }}')">
                                @csrf
                                <button type="submit" class="btn-action" style="background: #dc3545; color: white;">🗑️ Batalkan</button>
                            </form>
                        @else
                            <span style="color: #aaa; font-size: 13px;">Tidak ada aksi</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; padding: 30px; color: #9ca3af;">Belum ada riwayat pemesanan Pet Hotel.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4" style="margin-top: 15px;">
        {{ $petHotel->appends(request()->except('hotel_page'))->links() }}
    </div>
</div>

{{-- TABEL 2: RIWAYAT LAYANAN LAIN (GROOMING & KLINIK) --}}
<div style="margin-top: 30px; margin-bottom: 10px;">
    <h3 style="color: #800080; margin: 0; display: flex; align-items: center; gap: 8px;">✂️ Riwayat Layanan (Grooming & Klinik)</h3>
</div>
<div class="table-container">
    <table class="table-custom">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Layanan</th>
                <th>Nama Anabul</th>
                <th>Tanggal Kunjungan</th>
                <th>Jam Kunjungan</th>
                <th>Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($layananLain as $key => $layanan)
            <tr>
                <td>{{ ($layananLain->currentPage() - 1) * $layananLain->perPage() + $key + 1 }}</td>
                <td style="font-weight: bold; color: #800080;">{{ $layanan->nama_layanan }}</td>
                <td>{{ $layanan->pet_name }}</td>
                <td>{{ \Carbon\Carbon::parse($layanan->tanggal)->format('d M Y') }}</td>
                <td>{{ substr($layanan->waktu, 0, 5) }} WIB</td>
                <td>
                    @if($layanan->status == 'Menunggu Pembayaran')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #fff3cd; color: #856404;">⏳ {{ $layanan->status }}</span>
                    @elseif($layanan->status == 'Dikonfirmasi' || $layanan->status == 'Selesai')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d4edda; color: #155724;">✅ {{ $layanan->status }}</span>
                    @elseif($layanan->status == 'Dibatalkan')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #f8d7da; color: #721c24;">❌ {{ $layanan->status }}</span>
                    @else
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #e2e8f0; color: #475569;">🔔 {{ $layanan->status }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 5px;">
                        @if($layanan->status == 'Menunggu Pembayaran')
                            <a href="{{ route('reservasi.bayar', $layanan->id) }}" class="btn-action" style="background: #007bff; color: white;">💳 Bayar DP</a>
                        @endif

                        @if($layanan->status == 'Selesai')
                            <a href="{{ route('reservasi.e-ticket.download', $layanan->id) }}" class="btn-action" style="background: #28a745; color: white;">📄 E-Ticket</a>
                        @endif

                        @if(!in_array($layanan->status, ['Dibatalkan', 'Selesai']))
                            <form action="{{ route('reservasi.batal', $layanan->id) }}" method="POST" onsubmit="return mintaAlasanDP(event, this, '{{ $layanan->nama_layanan }}', '{{ $layanan->status }}')">
                                @csrf
                                <button type="submit" class="btn-action" style="background: #dc3545; color: white;">🗑️ Batalkan</button>
                            </form>
                        @else
                            <span style="color: #aaa; font-size: 13px;">Tidak ada aksi</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; padding: 30px; color: #9ca3af;">Belum ada riwayat layanan medis atau grooming.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4" style="margin-top: 15px;">
        {{ $layananLain->appends(request()->except('layanan_page'))->links() }}
    </div>
</div>

{{-- TABEL 3: RIWAYAT PEMBELIAN PRODUK --}}
<div style="margin-top: 30px; margin-bottom: 10px;">
    <h3 style="color: #800080; margin: 0; display: flex; align-items: center; gap: 8px;">🛍️ Riwayat Pembelian Produk</h3>
</div>
<div class="table-container">
    <table class="table-custom">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Daftar Belanja</th>
                <th>Total Harga</th>
                <th>Metode Pengiriman</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembelianProduk as $key => $trx)
            <tr>
                <td>{{ ($pembelianProduk->currentPage() - 1) * $pembelianProduk->perPage() + $key + 1 }}</td>
                <td style="font-weight: bold; color: #666;">#{{ $trx->id }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 15px; font-size: 13px;">
                        @foreach($trx->detilProduk as $detil)
                            <li>{{ $detil->produk->nama_produk ?? 'Produk Terhapus' }} (x{{ $detil->jumlah }})</li>
                        @endforeach
                    </ul>
                </td>
                <td style="font-weight: bold; color: #800080;">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                <td>
                    <span style="text-transform: capitalize; font-size: 13px;">
                        🚗 {{ $trx->metode_pengiriman == 'delivery' ? 'Kirim ke Rumah' : 'Ambil di Toko' }}
                    </span>
                </td>
                <td>
                    @if($trx->status == 'Selesai')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d4edda; color: #155724;">✅ {{ $trx->status }}</span>
                    @elseif($trx->status == 'Dibatalkan')
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #f8d7da; color: #721c24;">❌ {{ $trx->status }}</span>
                    @else
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #fff3cd; color: #856404;">📦 {{ $trx->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">Belum ada riwayat pembelian produk.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4" style="margin-top: 15px;">
        {{ $pembelianProduk->appends(request()->except('produk_page'))->links() }}
    </div>
</div>

<script>
    function mintaAlasanDP(event, form, namaLayanan, status) {
        event.preventDefault();
        let pesan = `Apakah kamu yakin ingin membatalkan jadwal ${namaLayanan} ini?\n\n`;
        if (status === 'Disetujui' || status === 'Dikonfirmasi' || status === 'Menunggu Konfirmasi Admin' || status === 'Menunggu Jadwal') {
            pesan += "⚠️ PERINGATAN: Karena reservasi sudah masuk tahap proses, uang DP yang sudah dibayarkan TIDAK DAPAT DIKEMBALIKAN (Hangus).\n\n";
        }
        pesan += "Silakan masukkan alasan pembatalan:";
        let alasan = prompt(pesan);
        if (alasan !== null && alasan.trim() !== '') {
            let inputAlasan = document.createElement('input');
            inputAlasan.type = 'hidden';
            inputAlasan.name = 'alasan_batal';
            inputAlasan.value = alasan;
            form.appendChild(inputAlasan);
            form.submit();
        } else if (alasan !== null) {
            alert('Gagal membatalkan. Alasan pembatalan wajib diisi ya!');
        }
        return false;
    }
</script>
@endsection
