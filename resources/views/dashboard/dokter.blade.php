@extends('layouts.app')

@section('title', 'Dashboard Dokter - Paw Center')

@section('content')
<div class="dashboard-header">
    <h2 style="color: var(--purple-900); font-weight: 700;">Dashboard Dokter Klinik</h2>
    <a href="{{ route('profil.umum') }}" class="btn-profile">⚙️ Pengaturan Profil</a>
</div>

<div class="grid-container">
    <div class="card-dokter card-full">
        <h3 style="color: var(--purple-800); margin-bottom: 15px; border-bottom: 2px solid var(--purple-50); padding-bottom: 10px;">Antrean Pemeriksaan (Dikonfirmasi)</h3>

        <div style="overflow-x: auto;">
            <table class="table-dokter">
                <thead>
                    <tr>
                        <th>No. Antrean</th>
                        <th>Jadwal</th>
                        <th>Pemilik</th>
                        <th>Nama Hewan</th>
                        <th>Keluhan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antreanPemeriksaan as $index => $antrean)
                    <tr>
                        <td><strong>#{{ $index + 1 }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($antrean->waktu_reservasi)->format('d M Y - H:i') }}</td>
                        <td>{{ $antrean->user->name ?? 'User' }}</td>
                        <td>{{ $antrean->hewan->nama_hewan ?? '-' }} ({{ $antrean->hewan->jenis ?? '-' }})</td>
                        <td>{{ $antrean->keluhan ?? 'Tidak ada catatan' }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('dokter.reservasi.updateStatus', $antrean->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Diproses">
                                    <button type="submit" class="btn-action btn-acc">Mulai Periksa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 20px;">Tidak ada antrean pemeriksaan saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-dokter card-full">
        <h3 style="color: var(--purple-800); margin-bottom: 15px; border-bottom: 2px solid var(--purple-50); padding-bottom: 10px;">Sedang Diperiksa & Isi Rekam Medis</h3>

        <div style="overflow-x: auto;">
            <table class="table-dokter">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Hewan</th>
                        <th>Layanan</th>
                        <th>Aksi / Rekam Medis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pasienDiperiksa as $pasien)
                    <tr>
                        <td>RES-{{ $pasien->id }}</td>
                        <td><strong>{{ $pasien->hewan->nama_hewan ?? '-' }}</strong></td>
                        <td>{{ $pasien->nama_layanan }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button type="button" class="btn-action btn-rm" onclick="openModal('{{ $pasien->id }}', '{{ $pasien->hewan->nama_hewan ?? '-' }}', '{{ $pasien->hewan_id }}')">
                                    📝 Isi Rekam Medis & Selesai
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #888; padding: 20px;">Belum ada pasien yang sedang diperiksa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="rmModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 style="color: var(--purple-800); margin-bottom: 15px;">Input Rekam Medis</h3>
        <p style="font-size: 14px; margin-bottom: 15px;">Pasien: <strong id="modalPasienName"></strong></p>

        <form id="rmForm" action="{{ route('dokter.simpanRM') }}" method="POST">
            @csrf
            <input type="hidden" name="reservasi_id" id="reservasi_id">
            <input type="hidden" name="hewan_id" id="hewan_id">

            <div style="margin-bottom:15px;">
                <label style="font-weight: bold; font-size: 14px;">Diagnosa</label><br>
                <input type="text" name="diagnosa" style="width:100%; border:1px solid #ccc; border-radius:8px; padding:10px; margin-top:5px;" placeholder="Hasil diagnosa..." required>
            </div>
            <div style="margin-bottom:20px;">
                <label style="font-weight: bold; font-size: 14px;">Tindakan / Terapi</label><br>
                <textarea name="tindakan" style="width:100%; border:1px solid #ccc; border-radius:8px; padding:10px; margin-top:5px;" rows="3" placeholder="Obat / Treatment yang diberikan..." required></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-action btn-acc" style="flex: 1; padding:12px; font-size: 14px;">Simpan & Selesai</button>
                <button type="button" onclick="closeModal()" style="flex: 1; background:#f1f3f5; color:#333; border:none; padding:12px; border-radius:6px; cursor: pointer; font-weight: bold;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(reservasiId, namaHewan, hewanId) {
        document.getElementById('rmModal').style.display = 'block';
        document.getElementById('modalPasienName').innerText = namaHewan;
        document.getElementById('reservasi_id').value = reservasiId;
        document.getElementById('hewan_id').value = hewanId;
    }

    function closeModal() {
        document.getElementById('rmModal').style.display = 'none';
    }

    // Tutup modal kalau user klik di luar area modal (kotak putih)
    window.onclick = function(event) {
        let modal = document.getElementById('rmModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection
