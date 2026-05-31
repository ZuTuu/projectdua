@extends('layouts.app')

@section('title', 'Dashboard Dokter - Paw Center')

@section('content')
<style>
    .welcome-banner { background: #faf5ff; padding: 20px; border-radius: 12px; border-left: 5px solid #36005E; margin-bottom: 25px; }
    .welcome-banner h2 { margin: 0; color: #36005E; font-weight: 700; font-size: 22px; }
    .welcome-banner p { margin: 5px 0 0 0; color: #666; font-size: 14px; }

    .card-dokter { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #E6E6FA; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    .badge-dikonfirmasi { background: #E6F4EA; color: #1E8E3E; }
    .badge-diproses { background: #FEF7E0; color: #F9AB00; }
    .badge-menunggu-jadwal { background: #E8F0FE; color: #1A73E8; }
    .badge-selsai { background: #e0f2fe; color: #0284c7; }

    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    th { color: #888; font-size: 13px; text-transform: uppercase; }

    .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; text-align: center; color: white; }
    .btn-start { background: #1E8E3E; }
    .btn-start:hover { background: #146c2e; }
    .btn-finish { background: #36005E; }
    .btn-finish:hover { background: #2c004f; }

    /* Kotak Statistik 3 Kolom */
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
    @media (max-width: 768px) { .stat-grid { grid-template-columns: 1fr; } }
    .stat-box { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #E6E6FA; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px; }
    .stat-icon { font-size: 30px; background: #f7f1ff; padding: 15px; border-radius: 10px; color: #36005E; }
    .stat-text h4 { margin: 0; font-size: 24px; color: #333; }
    .stat-text p { margin: 0; color: #888; font-size: 13px; }

    /* Pop-up Modal Update RM */
    .modal-rm { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); }
    .modal-rm-content { background: white; margin: 3% auto; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; }
</style>

<div class="welcome-banner">
    <h2>Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
    <p>Semoga harimu menyenangkan. Yuk, pantau dan cek antrean pasien medis hari ini!</p>
</div>

@if(session('success'))
    <div style="background: #E6F4EA; color: #1E8E3E; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        {{ session('success') }}
    </div>
@endif

{{-- KOTAK STATISTIK --}}
<div class="stat-grid">
    <div class="stat-box" style="border-left: 4px solid #36005E;">
        <div class="stat-icon">🩺</div>
        <div class="stat-text">
            <h4>{{ $totalPemeriksaan }}</h4>
            <p>Total Antrean Aktif</p>
        </div>
    </div>
    <div class="stat-box" style="border-left: 4px solid #be185d;">
        <div class="stat-icon" style="color: #be185d; background: #fce7f3;">💉</div>
        <div class="stat-text">
            <h4>{{ $totalVaksinasi }}</h4>
            <p>Antrean Vaksinasi</p>
        </div>
    </div>
    <div class="stat-box" style="border-left: 4px solid #10b981;">
        <div class="stat-icon" style="color: #10b981; background: #d1fae5;">📝</div>
        <div class="stat-text">
            <h4>{{ $totalRekamMedis }}</h4>
            <p>Rekam Medis (Selesai)</p>
        </div>
    </div>
</div>

{{-- TABEL 1: ANTREAN KONSULTASI DOKTER --}}
<div class="card-dokter">
    <h3 style="margin-bottom: 15px; color:#36005E;">🩺 Antrean Konsultasi Dokter</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Jadwal</th>
                    <th>Pasien (Hewan)</th>
                    <th>Owner</th>
                    <th>Keluhan / Layanan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanKonsultasi as $item)
                <tr>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong><br>
                        <small style="color: #666;">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                    </td>
                    <td>
                        <strong>{{ $item->pet_name }}</strong>
                    </td>
                    <td>{{ $item->user->name ?? 'N/A' }}</td>
                    <td>{{ $item->nama_layanan }}</td>
                    <td>
                        <span class="status-badge {{ $item->status == 'Diproses' ? 'badge-diproses' : 'badge-menunggu-jadwal' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td>
                        @if($item->status != 'Diproses')
                            <form action="{{ route('dokter.mulaiPeriksa', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-start">🩺 Mulai Periksa</button>
                            </form>
                        @else
                            {{-- addslashes penting buat nahan error kalo nama hewannya ada tanda kutip --}}
                            <button type="button" class="btn-action btn-finish" onclick="openRMModal('{{ $item->id }}', '{{ addslashes($item->pet_name) }}')">✅ Isi Rekam Medis</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #888;">Belum ada antrean konsultasi saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- TABEL 2: ANTREAN VAKSINASI --}}
<div class="card-dokter">
    <h3 style="margin-bottom: 15px; color:#be185d;">💉 Antrean Vaksinasi & Tindakan Medis</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Jadwal</th>
                    <th>Pasien (Hewan)</th>
                    <th>Owner</th>
                    <th>Keluhan / Layanan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanVaksinasi as $item)
                <tr>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong><br>
                        <small style="color: #666;">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                    </td>
                    <td>
                        <strong>{{ $item->pet_name }}</strong>
                    </td>
                    <td>{{ $item->user->name ?? 'N/A' }}</td>
                    <td>{{ $item->nama_layanan }}</td>
                    <td>
                        <span class="status-badge {{ $item->status == 'Diproses' ? 'badge-diproses' : 'badge-menunggu-jadwal' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td>
                        @if($item->status != 'Diproses')
                            <form action="{{ route('dokter.mulaiPeriksa', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-start">🩺 Mulai Periksa</button>
                            </form>
                        @else
                            <button type="button" class="btn-action btn-finish" onclick="openRMModal('{{ $item->id }}', '{{ addslashes($item->pet_name) }}')">✅ Isi Rekam Medis</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #888;">Belum ada antrean vaksinasi saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- POP-UP REKAM MEDIS --}}
<div id="rmUpdateModal" class="modal-rm">
    <div class="modal-rm-content">
        <h3 id="modalTitle" style="color:#36005E; margin-bottom:15px;">Form Rekam Medis</h3>
        <form action="{{ route('dokter.simpanRM') }}" method="POST">
            @csrf
            <input type="hidden" id="modal_reservasi_id" name="reservasi_id">

            <div style="margin-bottom:15px;">
                <label style="font-size:13px; color:#666; font-weight:bold; display:block; margin-bottom:5px;">Dokter yang Menangani</label>
                <select name="nama_dokter" style="width:100%; border:1px solid #ddd; border-radius:8px; padding:10px; font-size:14px;" required>
                    <option value="" disabled selected>-- Pilih Dokter --</option>
                    <option value="Arifa">drh. Arifa</option> 
                    <option value="Agung">drh. Agung</option> 
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-size:13px; color:#666; font-weight:bold; display:block; margin-bottom:5px;">Diagnosa Medis</label>
                <input type="text" name="diagnosa" style="width:100%; border:1px solid #ddd; border-radius:8px; padding:10px; font-size:14px;" required placeholder="Misal: Cat Flu, Scabies, Demam">
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-size:13px; color:#666; font-weight:bold; display:block; margin-bottom:5px;">Tindakan / Terapi Obat</label>
                <textarea name="tindakan" style="width:100%; border:1px solid #ddd; border-radius:8px; padding:10px; font-size:14px; resize:vertical;" rows="3" required placeholder="Misal: Injeksi vitamin, resep amoxicillin tablet 2x1"></textarea>
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-size:13px; color:#666; font-weight:bold; display:block; margin-bottom:5px;">Biaya Tambahan (Opsional)</label>
                <input type="number" name="biaya_tambahan" min="0" style="width:100%; border:1px solid #ddd; border-radius:8px; padding:10px; font-size:14px;" placeholder="Contoh: 25000 (Kosongkan jika tidak ada)">
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:13px; color:#666; font-weight:bold; display:block; margin-bottom:5px;">Catatan untuk Pelanggan (Opsional)</label>
                <textarea name="catatan" style="width:100%; border:1px solid #ddd; border-radius:8px; padding:10px; font-size:14px; resize:vertical;" rows="3" placeholder="Pesan, anjuran, atau resep untuk pemilik hewan..."></textarea>
            </div>
            <button type="submit" class="btn-action btn-finish" style="width:100%; padding:12px; margin-bottom:10px; font-size:14px;">💾 Simpan & Selesaikan Pemeriksaan</button>
            <button type="button" onclick="closeRMModal()" style="width:100%; background:#eee; color:#333; border:none; padding:10px; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px;">Batal</button>
        </form>
    </div>
</div>

<script>
    function openRMModal(reservasiId, namaHewan) {
        document.getElementById('rmUpdateModal').style.display = 'block';
        document.getElementById('modal_reservasi_id').value = reservasiId;
        document.getElementById('modalTitle').innerText = '📝 Rekam Medis: ' + namaHewan;
    }

    function closeRMModal() {
        document.getElementById('rmUpdateModal').style.display = 'none';
    }

    // Biar pop-up nutup kalo user ngeklik di luar kotak form
    window.onclick = function(event) {
        const modal = document.getElementById('rmUpdateModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection