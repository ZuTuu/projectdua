@extends('layouts.app')

@section('title', 'Pesanan Pet Hotel Aktif')

@section('content')
<div class="card-dokter">
    <h3 class="card-title">Daftar Pesanan Pet Hotel (Sedang Berjalan)</h3>
    <div class="table-responsive">
        <table class="table-dokter">
            <thead>
                <tr>
                    <th>ID Reservasi</th>
                    <th>Pemilik</th>
                    <th>Hewan</th>
                    <th>Jadwal Masuk</th>
                    <th>Jadwal Keluar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($petHotel as $hotel)
                <tr>
                    <td>RES-{{ $hotel->id }}</td>
                    <td>{{ $hotel->user->name ?? 'User' }}</td>
                    <td><strong>{{ $hotel->pet_name }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($hotel->tanggal . ' ' . $hotel->waktu)->format('d M Y, H:i') }}</td>
                    <td>{{ $hotel->tanggal_keluar ? \Carbon\Carbon::parse($hotel->tanggal_keluar)->format('d M Y') : 'Sesuai Durasi' }}</td>
                    <td>
                        <span class="status-badge {{ $hotel->status == 'Menunggu Jadwal' ? 'badge-dikonfirmasi' : 'badge-diproses' }}">
                            {{ $hotel->status }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if($hotel->status == 'Menunggu Jadwal')
                                <form action="{{ route('staff.pesanan.updateStatus', $hotel->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="status" value="Diproses">
                                    <button type="submit" class="btn-action btn-acc">📥 Check-in</button>
                                </form>
                            @elseif($hotel->status == 'Diproses')
                                <form action="{{ route('staff.pesanan.updateStatus', $hotel->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="status" value="Selesai">
                                    <button type="submit" class="btn-action btn-rm">📤 Check-out</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">Tidak ada pesanan Pet Hotel yang sedang aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $petHotel->links() }} {{-- Untuk pet hotel --}}
</div>
@endsection