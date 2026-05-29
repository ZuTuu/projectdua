@extends('layouts.app')

@section('title', 'Antrean Grooming Aktif')

@section('content')
<div class="card-dokter">
    <h3 class="card-title">Antrean Grooming (Sedang Berjalan)</h3>
    <div class="table-responsive">
        <table class="table-dokter">
            <thead>
                <tr>
                    <th>ID Reservasi</th>
                    <th>Pemilik</th>
                    <th>Hewan</th>
                    <th>Jadwal Grooming</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grooming as $groom)
                <tr>
                    <td>RES-{{ $groom->id }}</td>
                    <td>{{ $groom->user->name ?? 'User' }}</td>
                    <td><strong>{{ $groom->pet_name }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($groom->tanggal . ' ' . $groom->waktu)->format('d M Y, H:i') }}</td>
                    <td>
                        <span class="status-badge {{ $groom->status == 'Menunggu Jadwal' ? 'badge-dikonfirmasi' : 'badge-diproses' }}">
                            {{ $groom->status }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if($groom->status == 'Menunggu Jadwal')
                                <form action="{{ route('staff.pesanan.updateStatus', $groom->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="status" value="Diproses">
                                    <button type="submit" class="btn-action btn-acc">🫧 Mulai Grooming</button>
                                </form>
                            @elseif($groom->status == 'Diproses')
                                <form action="{{ route('staff.pesanan.updateStatus', $groom->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipe" value="reservasi">
                                    <input type="hidden" name="status" value="Selesai">
                                    <button type="submit" class="btn-action btn-rm">✅ Grooming Selesai</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">Tidak ada antrean grooming yang aktif saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $grooming->links() }} {{-- Untuk grooming --}}
</div>
@endsection