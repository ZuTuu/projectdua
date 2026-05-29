@extends('layouts.app')

@section('title', 'Kelola Pemeriksaan Medis - Paw Center')

@section('content')
<style>
    .card-dokter { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #E6E6FA; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    .badge-dikonfirmasi { background: #E6F4EA; color: #1E8E3E; }
    .badge-diproses { background: #FEF7E0; color: #F9AB00; }
    .badge-menunggu-jadwal { background: #E8F0FE; color: #1A73E8; }
    .badge-selesai { background: #e0f2fe; color: #0284c7; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    th { color: #888; font-size: 13px; text-transform: uppercase; }
    
    .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; text-align: center; color: white; }
    .btn-start { background: #1E8E3E; }
    .btn-finish { background: #36005E; }
    
    /* MODAL STYLING */
    .modal-rm { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .modal-rm-content { background: white; margin: 10% auto; padding: 25px; border-radius: 12px; width: 90%; max-width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
</style>

<div class="card-dokter">
    <h3 style="color:#36005E; margin: 0;">🩺 Kelola & Riwayat Pemeriksaan Medis</h3>

    @if(session('success'))
        <div style="background: #E6F4EA; color: #1E8E3E; padding: 12px; border-radius: 8px; margin-top: 15px; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Jadwal</th>
                <th>Pasien</th>
                <th>Layanan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($semuaPemeriksaan as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M') }}<br><small>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</small></td>
                <td><strong>{{ $item->nama_hewan ?? $item->pet_name }}</strong></td>
                <td>{{ $item->nama_layanan }}</td>
                <td><span class="status-badge {{ $item->status == 'Diproses' ? 'badge-diproses' : '' }}">{{ $item->status }}</span></td>
                <td>
                    @if($item->status == 'Menunggu Jadwal' || $item->status == 'Dikonfirmasi')
                        <form action="{{ route('dokter.mulaiPeriksa', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action btn-start">Mulai</button>
                        </form>
                    @elseif($item->status == 'Diproses')
                        <button type="button" class="btn-action btn-finish" onclick="openRMModal('{{ $item->id }}', '{{ $item->nama_hewan ?? $item->pet_name }}')">Selesai</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center;">Data kosong</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="rmUpdateModal" class="modal-rm">
    <div class="modal-rm-content">
        <h3 id="modalTitle" style="color:#36005E; margin-bottom:15px;">Form Rekam Medis</h3>
        
        <form action="{{ route('dokter.simpanRM') }}" method="POST">
            @csrf
            <input type="hidden" id="modal_reservasi_id" name="reservasi_id">
            
            <div style="margin-bottom:15px;">
                <label>Diagnosa</label>
                <input type="text" name="diagnosa" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Tindakan</label>
                <textarea name="tindakan" rows="3" required style="width:100%; padding:8px;"></textarea>
            </div>
            
            <button type="submit" class="btn-action btn-finish" style="width:100%; padding:10px;">💾 Simpan & Selesaikan</button>
        </form>
    </div>
</div>

<script>
    function openRMModal(id, nama) {
        document.getElementById('rmUpdateModal').style.display = 'block';
        document.getElementById('modal_reservasi_id').value = id;
        document.getElementById('modalTitle').innerText = 'Rekam Medis: ' + nama;
    }

    function closeRMModal() {
        document.getElementById('rmUpdateModal').style.display = 'none';
    }
</script>
@endsection