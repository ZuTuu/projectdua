@extends('layouts.app')

@section('title', 'Pantau Semua Stok')

@section('content')
<div class="dashboard-header">
    <h2 class="page-title">📦 Pantau Keseluruhan Stok Produk</h2>
</div>

<div class="card-dokter">
    <div class="table-responsive">
        <table class="table-staff">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Sisa Stok</th>
                    <th>Status Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semuaProduk as $produk)
                <tr>
                    <td>
                        @if($produk->gambar)
                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="img" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                        @else
                            <div style="width: 40px; height: 40px; border-radius: 6px; background: #f1f3f5; display: flex; align-items: center; justify-content: center; font-size: 14px;">📦</div>
                        @endif
                    </td>
                    <td>{{ $produk->nama_produk }}</td>
                    <td class="{{ $produk->stok <= 5 ? 'text-danger fw-bold' : '' }}">{{ $produk->stok }} Pcs</td>
                    <td>
                        @if($produk->stok <= 0)
                            <span class="status-badge badge-batal">Habis</span>
                        @elseif($produk->stok <= 5)
                            <span class="status-badge badge-diproses">Kritis</span>
                        @else
                            <span class="status-badge badge-dikonfirmasi">Aman</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-state">Belum ada data produk di database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
