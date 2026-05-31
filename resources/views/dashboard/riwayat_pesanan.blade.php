@extends('layouts.app')

@section('title', 'Riwayat Pesanan Produk')

@section('content')
<div class="content">
    <div class="admin-card" style="margin-top: 25px;">
        <h3 style="color: #0ea5e9;">🛍️ Semua Riwayat Pesanan Produk</h3>
        
        <form action="{{ route('admin.riwayat_pesanan') }}" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari ID, Pelanggan, atau Status..." style="padding: 8px 15px; border: 1px solid #cbd5e1; border-radius: 8px; width: 300px; outline: none;">
            <button type="submit" class="btn-sm btn-acc" style="padding: 8px 20px; font-size: 14px;">Cari</button>
            @if(!empty($search))
                <a href="{{ route('admin.riwayat_pesanan') }}" style="padding: 8px; color: #dc2626; text-decoration: none; font-size: 14px; font-weight: bold;">Reset</a>
            @endif
        </form>

        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tgl Dibuat</th>
                        <th>Pelanggan</th>
                        <th>Rincian Belanja</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembelianProduk as $pesanan)
                    <tr>
                        <td><strong>#TRX-{{ $pesanan->id }}</strong></td>
                        <td>
                            @if(!empty($pesanan->created_at))
                                <small>{{ \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y, H:i') }} WIB</small>
                            @else
                                <span style="color: #dc2626; font-size: 11px;">⚠️ Tanpa Tanggal</span>
                            @endif
                        </td>
                        <td>{{ $pesanan->user->name ?? 'User' }}</td>
                        <td>
                            <ul class="detail-list" style="margin: 0; padding-left: 15px;">
                                @forelse($pesanan->detilProduk ?? [] as $detail)
                                    <li>{{ $detail->nama_produk ?? $detail->produk->nama_produk ?? 'Produk' }} (x{{ $detail->jumlah }})</li>
                                @empty
                                    <li>Tidak ada detail produk</li>
                                @endforelse
                            </ul>
                            
                            <div style="margin-top: 8px; font-size: 11px; background: #e0f2fe; padding: 6px; border-radius: 6px; color: #0369a1; font-weight: bold; display: inline-block;">
                                @if(isset($pesanan->metode_pengiriman) && $pesanan->metode_pengiriman == 'pickup')
                                    📍 Ambil di Toko: {{ !empty($pesanan->tanggal_ambil) ? \Carbon\Carbon::parse($pesanan->tanggal_ambil)->format('d M Y') : '-' }}
                                @else
                                    🚚 Dikirim Kurir (Delivery)
                                @endif
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST" style="display: flex; gap: 5px;">
                                @csrf
                                <input type="hidden" name="tipe" value="transaksi">
                                <select name="status" class="status-select">
                                    <option value="Dikonfirmasi" {{ $pesanan->status == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="Menunggu Jadwal" {{ $pesanan->status == 'Menunggu Jadwal' ? 'selected' : '' }}>Menunggu Diambil</option>
                                    <option value="Menunggu Kurir" {{ $pesanan->status == 'Menunggu Kurir' ? 'selected' : '' }}>Menunggu Kurir</option>
                                    <option value="Pesanan Diantar" {{ $pesanan->status == 'Pesanan Diantar' ? 'selected' : '' }}>Diantar</option>
                                    <option value="Selesai" {{ $pesanan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <button type="submit" class="btn-sm btn-acc">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align: center; color: #888;">Tidak ada pesanan produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $pembelianProduk->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>
@endsection