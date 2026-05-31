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
                        <th>Rincian Produk</th>
                        <th>Pengambilan</th>
                        <th>Bukti Bayar</th>
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
                        </td>
                        <td>
                            <div style="margin-top: 8px; font-size: 11px; background: #e0f2fe; padding: 6px; border-radius: 6px; color: #0369a1; font-weight: bold; display: inline-block;">
                                @if(isset($pesanan->metode_pengiriman) && $pesanan->metode_pengiriman == 'pickup')
                                    📍 Ambil di Toko: {{ !empty($pesanan->tanggal_ambil) ? \Carbon\Carbon::parse($pesanan->tanggal_ambil)->format('d M Y') : '-' }}
                                @elseif(isset($pesanan->metode_pengiriman) && $pesanan->metode_pengiriman == 'Transaksi Offline')
                                    🏪 Pembelian Langsung (Kasir)
                                @else
                                    🚚 Dikirim Kurir (Delivery)
                                @endif
                            </div>

                            {{-- Menampilkan total harga --}}
                            <div style="margin-top: 8px; font-size: 12px; color: #16a34a; font-weight: bold;">
                                Total: Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </div>

                            {{-- Notif Alasan Batal --}}
                            @if($pesanan->status == 'Dibatalkan' && !empty($pesanan->alasan_tolak))
                                <div style="margin-top: 8px; font-size: 11px; background: #fee2e2; border-left: 3px solid #ef4444; padding: 6px; border-radius: 0 6px 6px 0; color: #b91c1c;">
                                    <strong>❌ Dibatalkan:</strong><br>
                                    {{ $pesanan->alasan_tolak }}
                                </div>
                            @endif
                        </td>

                        <td>
                            @if($pesanan->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" target="_blank" class="btn btn-sm text-white" style="background-color: #2    e1065;">Lihat Struk</a>
                            @elseif($pesanan->status == 'Menunggu Pembayaran')
                                <span class="badge bg-warning text-dark">Cash/Belum Bayar</span>
                            @else
                                <span class="badge bg-secondary">Tidak Ada</span>
                            @endif
                        </td>

                        <td>
                            <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 5px;">
                                @csrf
                                <input type="hidden" name="tipe" value="transaksi">
                                <div style="display: flex; gap: 5px;">
                                    <select name="status" class="status-select" onchange="toggleAlasanInput(this)">
                                        <option value="Menunggu Pembayaran" {{ $pesanan->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                        <option value="Menunggu Konfirmasi Admin" {{ $pesanan->status == 'Menunggu Konfirmasi Admin' ? 'selected' : '' }}>Menunggu Konfirmasi (Dicek)</option>
                                        <option value="Dikonfirmasi" {{ $pesanan->status == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi / Diproses</option>
                                        <option value="Menunggu Kurir" {{ $pesanan->status == 'Menunggu Kurir' ? 'selected' : '' }}>Menunggu Kurir</option>
                                        <option value="Pesanan Diantar" {{ $pesanan->status == 'Pesanan Diantar' ? 'selected' : '' }}>Pesanan Diantar</option>
                                        <option value="Selesai" {{ $pesanan->status == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                        <option value="Dibatalkan" {{ $pesanan->status == 'Dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                    </select>
                                    <button type="submit" class="btn-sm btn-acc">Update</button>
                                </div>

                                {{-- Input alasan dibatalkan INSIDE the form --}}
                                <div class="alasan-tolak-container" style="display: {{ $pesanan->status == 'Dibatalkan' ? 'block' : 'none' }};">
                                    <input type="text" name="alasan_tolak" placeholder="Tulis alasan dibatalkan..." value="{{ $pesanan->alasan_tolak ?? '' }}" style="border: 1px solid #dc3545; padding: 6px; border-radius: 4px; width: 100%; font-size: 12px;">
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; color: #888;">Tidak ada pesanan produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $pembelianProduk->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>

<script>
    function toggleAlasanInput(selectElement) {
        const form = selectElement.closest('form');
        const alasanContainer = form.querySelector('.alasan-tolak-container');
        const alasanInput = form.querySelector('input[name="alasan_tolak"]');

        if (selectElement.value === 'Dibatalkan') {
            alasanContainer.style.display = 'block';
            if (alasanInput) {
                alasanInput.setAttribute('required', 'required');
            }
        } else {
            alasanContainer.style.display = 'none';
            if (alasanInput) {
                alasanInput.removeAttribute('required');
                alasanInput.value = '';
            }
        }
    }
</script>
@endsection
