@extends('layouts.app')

@section('title', 'Riwayat Reservasi Layanan')

@section('content')
<div class="content">
    <div class="admin-card" style="margin-top: 25px;">
        <h3 style="color: #0ea5e9;">🩺 Semua Riwayat Reservasi Layanan</h3>
        
        <form action="{{ route('admin.riwayat_layanan') }}" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari ID, Pelanggan, Layanan, atau Status..." style="padding: 8px 15px; border: 1px solid #cbd5e1; border-radius: 8px; width: 300px; outline: none;">
            <button type="submit" class="btn-sm btn-acc" style="padding: 8px 20px; font-size: 14px;">Cari</button>
            @if(!empty($search))
                <a href="{{ route('admin.riwayat_layanan') }}" style="padding: 8px; color: #dc2626; text-decoration: none; font-size: 14px; font-weight: bold;">Reset</a>
            @endif
        </form>

        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Tgl Dibuat</th>
                        <th>Pelanggan</th>
                        <th>Nama Anabul</th> <th>Layanan</th>
                        <th>Jadwal Layanan</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservasiLayanan as $pesanan)
                    <tr>
                        <td><strong>#RES-{{ $pesanan->id }}</strong></td>
                        <td><small>{{ \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y, H:i') }} WIB</small></td>
                        <td>{{ $pesanan->user->name ?? 'User' }}</td>
                        
                        <td>
                            <span style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:600; font-size: 13px; color: #4c1d95;">
                                🐾 {{ $pesanan->pet_name ?? '-' }}
                            </span>
                        </td>
                        
                        <td>{{ $pesanan->nama_layanan }}</td>
                        <td>
                            @if(!empty($pesanan->tanggal))
                                {{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }}<br>
                                <small style="color: #6b7280;">{{ \Carbon\Carbon::parse($pesanan->waktu)->format('H:i') }} WIB</small>
                            @else
                                <span style="color: #dc2626; font-size: 12px; font-weight: bold;">⚠️ Tanggal Kosong</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST" style="display: flex; gap: 5px;">
                                @csrf
                                <input type="hidden" name="tipe" value="reservasi">
                                <select name="status" class="status-select">
                                    <option value="Dikonfirmasi" {{ $pesanan->status == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="Menunggu Jadwal" {{ $pesanan->status == 'Menunggu Jadwal' ? 'selected' : '' }}>Menunggu Jadwal</option>
                                    <option value="Selesai" {{ $pesanan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <button type="submit" class="btn-sm btn-acc">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align: center; color: #888;">Tidak ada reservasi layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $reservasiLayanan->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>
@endsection