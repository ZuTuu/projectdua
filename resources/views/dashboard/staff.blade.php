@extends('layouts.app')

@section('title', 'Dashboard Staff (Operasional)')

@section('content')
<div class="dashboard-header">
    <h2 class="page-title">Dashboard Staff - Operasional</h2>
</div>

<div class="grid-stats" style="margin-bottom: 25px; display: flex; gap: 15px; flex-wrap: wrap;">
    <div class="stat-card" style="flex: 1; min-width: 200px;">
        <div class="title">Total Jenis Produk Tersedia</div>
        <div class="angka">{{ $totalProduk }}</div>
    </div>
    <div class="stat-card" style="flex: 1; min-width: 200px;">
        <div class="title">Total Jenis Layanan Tersedia</div>
        <div class="angka">{{ $totalLayanan }}</div>
    </div>
    <div class="stat-card" style="flex: 1; min-width: 200px;">
        <div class="title">Pet Hotel Aktif</div>
        <div class="angka">{{ $jumlahHotelAktif }}</div> </div>

    <div class="stat-card" style="flex: 1; min-width: 200px;">
        <div class="title">Antrean Grooming</div>
        <div class="angka">{{ $jumlahGroomingAktif }}</div>
    </div>
</div>

@if($produkKritis->count() > 0)
<div class="alert-stok">
    <strong>⚠️ Peringatan:</strong> Ada {{ $produkKritis->count() }} produk yang stoknya kritis (<= 5). Segera lakukan restock!
</div>
@endif

<div class="grid-container">
    <div class="card-dokter card-full">
        <h3 class="card-title">Daftar Produk Kritis (Butuh Restock)</h3>
        <div class="table-responsive">
            <table class="table-staff">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkKritis as $produk)
                    <tr>
                        <td>
                            @if($produk->gambar)
                                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="img" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 6px; background: #f1f3f5; display: flex; align-items: center; justify-content: center; font-size: 14px;">📦</div>
                            @endif
                        </td>
                        <td>{{ $produk->nama_produk }}</td>
                        <td>{{ $produk->kategori->nama_kategori ?? 'Lainnya' }}</td>
                        <td class="text-danger fw-bold">{{ $produk->stok }} Pcs</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state">Semua stok produk dalam kondisi aman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-dokter card-full">
        <h3 class="card-title">Manajemen Antrean Grooming</h3>
        <div class="table-responsive">
            <table class="table-dokter">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Pemilik</th>
                        <th>Hewan</th>
                        <th>Jadwal Grooming</th>
                        <th>Status Saat Ini</th>
                        <th>Aksi / Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grooming as $groom)
                    <tr>
                        <td>RES-{{ $groom->id }}</td>
                        <td>{{ $groom->user->name ?? 'User' }}</td>
                        <td>
                            <strong>{{ $groom->pet_name }}</strong>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($groom->tanggal . ' ' . $groom->waktu)->format('d M Y, H:i') }}</td>
                        <td>
                            @if($groom->status == 'Menunggu Jadwal')
                                <span class="status-badge badge-dikonfirmasi">{{ $groom->status }}</span>
                            @elseif($groom->status == 'Diproses')
                                <span class="status-badge badge-diproses">{{ $groom->status }}</span>
                            @else
                                <span class="status-badge" style="background-color: #e6f4ea; color: #1e7e34;">{{ $groom->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if($groom->status == 'Menunggu Jadwal')
                                    <form action="{{ route('staff.pesanan.updateStatus', $groom->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tipe" value="reservasi">
                                        <input type="hidden" name="status" value="Diproses">
                                        <button type="submit" class="btn-action btn-acc">✂️ Mulai Grooming</button>
                                    </form>
                                @elseif($groom->status == 'Diproses')
                                    <form action="{{ route('staff.pesanan.updateStatus', $groom->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tipe" value="reservasi">
                                        <input type="hidden" name="status" value="Selesai">
                                        <button type="submit" class="btn-action btn-rm">✅ Grooming Selesai</button>
                                    </form>
                                @else
                                    <span style="color: #1e7e34; font-weight: bold; font-size: 14px;">✅ Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada antrean grooming saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $grooming->appends(['hotel_page' => request('hotel_page')])->links() }}
        </div>
    </div>

    <div class="card-dokter card-full">
        <h3 class="card-title">Manajemen Pet Hotel & Penitipan Aktif</h3>
        <div class="table-responsive">
            <table class="table-dokter">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Pemilik</th>
                        <th>Hewan</th>
                        <th>Jadwal Masuk</th>
                        <th>Jadwal Keluar</th>
                        <th>Status Saat Ini</th>
                        <th>Aksi / Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petHotel as $hotel)
                    <tr>
                        <td>RES-{{ $hotel->id }}</td>
                        <td>{{ $hotel->user->name ?? 'User' }}</td>
                        <td>
                            <strong>{{ $hotel->pet_name }}</strong>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($hotel->tanggal . ' ' . $hotel->waktu)->format('d M Y, H:i') }}</td>
                        <td>
                            {{ $hotel->tanggal_keluar ? \Carbon\Carbon::parse($hotel->tanggal_keluar)->format('d M Y') : 'Sesuai Durasi' }}
                        </td>
                        <td>
                            @if($hotel->status == 'Menunggu Jadwal')
                                <span class="status-badge badge-dikonfirmasi">{{ $hotel->status }}</span>
                            @elseif($hotel->status == 'Diproses')
                                <span class="status-badge badge-diproses">{{ $hotel->status }}</span>
                            @else
                                <span class="status-badge" style="background-color: #e6f4ea; color: #1e7e34;">{{ $hotel->status }}</span>
                            @endif
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
                                @else
                                    <span style="color: #1e7e34; font-weight: bold; font-size: 14px;">✅ Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada hewan yang sedang dititipkan di Pet Hotel saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $petHotel->appends(['grooming_page' => request('grooming_page')])->links() }}
        </div>
    </div>
</div>
@endsection
