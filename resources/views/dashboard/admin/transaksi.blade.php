@extends('layouts.app')

@section('title', 'Kelola Pesanan Produk')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #800080; font-weight: bold;">📦 Kelola Pesanan Produk</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead style="background-color: #800080; color: white;">
                        <tr>
                            <th class="py-3 px-4 text-start">ID Pesanan</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Total & Ongkir</th>
                            <th class="py-3 px-4">Bukti Bayar</th>
                            <th class="py-3 px-4">Aksi / Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td class="px-4 text-start">
                                <strong>#TRX-{{ $item->id }}</strong><br>
                                <small class="text-muted">{{ $item->user->name ?? 'Anonim' }}</small>
                            </td>
                            <td class="px-4">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="px-4 text-success fw-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>

                            <td class="px-4">
                                @if($item->bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}" target="_blank" class="btn btn-sm text-white" style="background-color: #2e1065;">Lihat Struk</a>
                                @elseif($item->status == 'Menunggu Pembayaran')
                                    <span class="badge bg-warning text-dark">Cash/Belum Bayar</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Ada</span>
                                @endif
                            </td>

                            <td class="px-4">
                                <form action="{{ route('admin.transaksi.update', $item->id) }}" method="POST" class="d-flex gap-2 justify-content-center">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm" style="width: 200px; border-color: #800080;">
                                        <option value="Menunggu Pembayaran" {{ $item->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                        <option value="Menunggu Konfirmasi Admin" {{ $item->status == 'Menunggu Konfirmasi Admin' ? 'selected' : '' }}>Menunggu Konfirmasi (Dicek)</option>
                                        <option value="Dikonfirmasi" {{ $item->status == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi / Diproses</option>
                                        <option value="Menunggu Kurir" {{ $item->status == 'Menunggu Kurir' ? 'selected' : '' }}>Menunggu Kurir</option>
                                        <option value="Pesanan Diantar" {{ $item->status == 'Pesanan Diantar' ? 'selected' : '' }}>Pesanan Diantar</option>
                                        <option value="Selesai" {{ $item->status == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                        <option value="Dibatalkan" {{ $item->status == 'Dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success">Ubah</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada pesanan produk yang masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
