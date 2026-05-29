@extends('layouts.app')

@section('title', 'Dashboard Admin & Kasir')

@section('content')
<div class="content">
    <div class="admin-header">
        <h2>Dashboard Utama (Admin & Kasir)</h2>
    </div>

    <div class="grid-stats">
        <div class="stat-card">
            <a href="{{ route('admin.laporan') }}" style="text-decoration:none; color: inherit;" aria-label="Buka Laporan Penjualan">
                <span class="title">Total Pemasukan</span>
            </a>
            <span class="angka">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
        </div>

        <div class="stat-card">
            <span class="title">Pemasukan Produk</span>
            <span class="angka">Rp {{ number_format($totalPemasukanProduk, 0, ',', '.') }}</span>
        </div>
        <div class="stat-card">
            <span class="title">Pemasukan Layanan</span>
            <span class="angka">Rp {{ number_format($totalPemasukanLayanan, 0, ',', '.') }}</span>
        </div>

        <div class="stat-card"><span class="title">Menunggu Konfirmasi</span><span class="angka" style="color: #e67e22;">{{ $menungguKonfirmasi }}</span></div>
        <div class="stat-card"><span class="title">Pesanan Aktif</span><span class="angka" style="color: #3498db;">{{ $pesananDiproses }}</span></div>
        <div class="stat-card"><span class="title">Total Pengguna</span><span class="angka">{{ $totalPengguna }}</span></div>
    </div>

    <div class="admin-card">
        <h3 style="color: #6d28d9;">🩺 Antrean Konfirmasi Reservasi Layanan</h3>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Tgl Dibuat</th>
                        <th>Nama Pelanggan</th>
                        <th>Nama Anabul</th>
                        <th>Layanan Dipilih</th>
                        <th>Jadwal Layanan</th>
                        <th>Bukti DP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antreanLayanan as $antrean)
                    <tr>
                        <td><strong>#RES-{{ $antrean->id }}</strong></td>
                        <td><small>{{ \Carbon\Carbon::parse($antrean->created_at)->format('d M Y, H:i') }} WIB</small></td>
                        <td>{{ $antrean->user->name ?? 'User' }}</td>
                        <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:600; color: #4c1d95;">🐱 {{ $antrean->pet_name ?? '-' }}</span></td>
                        <td><strong>🏥 {{ $antrean->nama_layanan }}</strong></td>
                        <td>
                            @if(!empty($antrean->tanggal))
                                {{ \Carbon\Carbon::parse($antrean->tanggal)->format('d M Y') }}<br>
                                <small style="color: #6b7280;">{{ \Carbon\Carbon::parse($antrean->waktu)->format('H:i') }} WIB</small>
                            @else
                                <span style="color: #dc2626; font-size: 12px; font-weight: bold;">⚠️ Tanggal Kosong</span>
                            @endif
                        </td>
                        <td>
                            @if($antrean->bukti_pembayaran_dp)
                                <a href="{{ asset('storage/' . $antrean->bukti_pembayaran_dp) }}" target="_blank" style="background: var(--purple-900); color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px;">Lihat Bukti</a>
                            @else
                                <span style="color: red; font-size: 11px;">Belum Bayar DP</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('admin.reservasi.setujui', $antrean->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <button type="submit" class="btn-sm btn-acc" style="background: #28a745; color: white;">Setujui</button>
                                </form>

                                <form action="{{ route('admin.reservasi.tolak', $antrean->id) }}" method="POST" onsubmit="return mintaAlasanTolak(this)">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="alasan_tolak" class="input-alasan">

                                    <button type="submit" class="btn-sm btn-tolak">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align: center; color: #888;">Tidak ada antrean reservasi klinik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card" style="margin-top: 25px;">
        <h3 style="color: #6d28d9;">🏨 Jadwal Checkout Penitipan (Pet Hotel)</h3>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Tgl Dibuat</th>
                        <th>Nama Pelanggan</th>
                        <th>Nama Anabul</th>
                        <th>Layanan Dipilih</th>
                        <th>Jadwal Check-in/out</th>
                        <th>Bukti DP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antreanCheckout as $checkout)
                    <tr>
                        <td><strong>#RES-{{ $checkout->id }}</strong></td>
                        <td><small>{{ \Carbon\Carbon::parse($checkout->created_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</small></td>
                        <td>{{ $checkout->user->name ?? 'User' }}</td>
                        <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:600; color: #4c1d95;">🐶 {{ $checkout->pet_name ?? '-' }}</span></td>
                        <td><strong>🏨 {{ $checkout->nama_layanan }}</strong></td>
                        <td>
                            <div style="margin-top: 8px; font-size: 11px; background: #f3e8ff; padding: 6px; border-radius: 6px; color: #4c1d95; font-weight: bold; display: inline-block;">
                                🚪 Check-in: {{ \Carbon\Carbon::parse($checkout->tanggal)->timezone('Asia/Jakarta')->format('d M Y') }}<br>
                                <span style="color: #dc2626;">🛎️ Check-out: {{ \Carbon\Carbon::parse($checkout->tanggal_keluar)->timezone('Asia/Jakarta')->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td>
                            @if($checkout->bukti_pembayaran_dp)
                                <a href="{{ asset('storage/' . $checkout->bukti_pembayaran_dp) }}" target="_blank" style="background: var(--purple-900); color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px;">Lihat Bukti</a>
                            @else
                                <span style="color: red; font-size: 11px;">Belum Bayar DP</span>
                            @endif
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('admin.pesanan.updateStatus', $checkout->id) }}" method="POST" onsubmit="return confirm('Apakah anabul ini sudah di-checkout dan dibawa pulang oleh owner?');">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="status" value="Selesai">
                                    <button type="submit" class="btn-sm btn-acc" style="background: #28a745; color: white;">Selesaikan</button>
                                </form>

                                <form action="{{ route('admin.reservasi.tolak', $checkout->id) }}" method="POST" onsubmit="return mintaAlasanTolak(this)">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="alasan_tolak" class="input-alasan">
                                    <button type="submit" class="btn-sm btn-tolak">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; color: #888;">Tidak ada antrean checkout penitipan/pet hotel saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card" style="margin-top: 25px;">

        <h3 style="color: #6d28d9;">🛍️ Antrean Konfirmasi Pesanan Produk</h3>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tgl Dibuat</th>
                        <th>Nama Pelanggan</th>
                        <th>Rincian Belanja</th>
                        <th>Bukti Transfer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antreanProduk as $antrean)
                    <tr>
                        <td><strong>#TRX-{{ $antrean->id }}</strong></td>
                        <td><small>{{ \Carbon\Carbon::parse($antrean->created_at)->format('d M Y, H:i') }} WIB</small></td>
                        <td>{{ $antrean->user->name ?? 'User' }}</td>
                        <td>
                            <ul class="detail-list" style="margin: 0; padding-left: 15px; font-size: 13px;">
                                @foreach($antrean->detail_belanja as $detail)
                                    <li>{{ $detail->nama_produk }} <strong>(x{{ $detail->jumlah }})</strong></li>
                                @endforeach
                            </ul>
                            <div style="margin-top: 8px; font-size: 11px; background: #f3e8ff; padding: 6px; border-radius: 6px; color: #4c1d95; font-weight: bold; display: inline-block;">
                                @if(isset($antrean->metode_pengiriman) && $antrean->metode_pengiriman == 'pickup')
                                    📍 Ambil di Toko: {{ !empty($antrean->tanggal_ambil) ? \Carbon\Carbon::parse($antrean->tanggal_ambil)->format('d M Y') : '-' }}
                                @else
                                    🚚 Dikirim Kurir (Delivery)
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($antrean->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $antrean->bukti_pembayaran) }}" target="_blank" style="background: var(--purple-900); color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px;">Lihat Struk</a>

                            @elseif(isset($antrean->metode_pembayaran) && strtolower($antrean->metode_pembayaran) == 'cash')
                                <span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">💵 Cash / Tunai</span>

                            @else
                                <span style="color: red; font-size: 11px;">Belum Transfer</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('admin.reservasi.setujui', $antrean->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipe" value="transaksi">
                                    <button type="submit" class="btn-sm btn-acc" style="background: #28a745; color: white;">Setujui</button>
                                </form>

                                <form action="{{ route('admin.reservasi.tolak', $antrean->id) }}" method="POST" onsubmit="return mintaAlasanTolak(this)">
                                    @csrf
                                    <input type="hidden" name="tipe" value="transaksi">
                                    <input type="hidden" name="alasan_tolak" class="input-alasan">

                                    <button type="submit" class="btn-sm btn-tolak">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; color: #888;">Tidak ada antrean pesanan produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card" style="margin-top: 25px;">
        <h3>Riwayat Aktivitas Sistem</h3>
        <ul class="activity-list">
            @forelse($riwayatAktivitas as $log)
                <li>
                    <span class="activity-time">{{ $log->created_at->diffForHumans() }}</span>
                    <span class="activity-desc">
                        <strong>[{{ $log->aktivitas }}]</strong> {{ $log->deskripsi }}
                    </span>
                </li>
            @empty
                <li class="activity-desc">Belum ada catatan aktivitas.</li>
            @endforelse
        </ul>

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $riwayatAktivitas->links() }}
        </div>
    </div>
</div>

<script>
    function mintaAlasanTolak(form) {
        let alasan = prompt("⚠️ Pesanan akan ditolak!\nSilakan masukkan alasan penolakan:");

        // Jika admin klik Cancel pada popup prompt
        if (alasan === null) {
            return false; // Batalkan submit
        }

        // Jika alasan kosong tapi di-Ok, kasih peringatan (opsional)
        if (alasan.trim() === "") {
            alert("Alasan tolak harus diisi!");
            return false;
        }

        // Masukkan alasan ke input hidden dan lanjutkan submit form
        form.querySelector('.input-alasan').value = alasan;
        return true;
    }
</script>
@endsection
