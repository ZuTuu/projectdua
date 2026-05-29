@extends('layouts.app')

@section('title', 'Kelola Layanan Klinik')

@section('content')
<div class="content">
    <div class="admin-header">
        <h2>Kelola Layanan Klinik 🩺</h2>
        <button onclick="document.getElementById('modalTambah').style.display='block'" class="btn-acc" style="padding: 10px 20px; border-radius: 8px; border:none; cursor:pointer; font-weight:bold;">+ Tambah Layanan Baru</button>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="admin-card">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Layanan</th>
                        <th>Harga Estimasi</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($layanan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" width="60" style="border-radius: 8px; border: 1px solid var(--purple-100);">
                            @else
                                <span style="color: var(--text-muted); font-style: italic; font-size: 12px;">Tidak ada foto</span>
                            @endif
                        </td>
                        <td><strong>{{ $item->nama_layanan }}</strong></td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td><small>{{ Str::limit($item->deskripsi, 50) }}</small></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button onclick="bukaModalEdit({{ $item }})" class="btn-sm btn-acc">Edit</button>
                                <form action="{{ route('admin.layanan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus layanan {{ addslashes($item->nama_layanan) }} dari klinik?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm btn-tolak">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada layanan klinik yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalTambah" class="modal-layanan">
    <div class="modal-content-layanan">
        <span class="close-layanan" onclick="document.getElementById('modalTambah').style.display='none'">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--purple-900);">Tambah Layanan Baru</h3>
        <form action="{{ route('admin.layanan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group-layanan">
                <label>Nama Layanan</label>
                <input type="text" name="nama_layanan" class="form-control-layanan" placeholder="Contoh: Vaksinasi Kucing" required>
            </div>
            <div class="form-group-layanan">
                <label>Harga Estimasi (Rp)</label>
                <input type="number" name="harga" class="form-control-layanan" required>
            </div>
            <div class="form-group-layanan">
                <label>Deskripsi Layanan</label>
                <textarea name="deskripsi" class="form-control-layanan" rows="3" required></textarea>
            </div>
            <div class="form-group-layanan">
                <label>Gambar Layanan (Opsional)</label>
                <input type="file" name="gambar" class="form-control-layanan">
            </div>
            <button type="submit" class="btn-acc" style="width: 100%; padding: 12px; border-radius: 8px; border:none; font-weight:bold; cursor:pointer; margin-top:10px;">Simpan Layanan</button>
        </form>
    </div>
</div>

<div id="modalEdit" class="modal-layanan">
    <div class="modal-content-layanan">
        <span class="close-layanan" onclick="document.getElementById('modalEdit').style.display='none'">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--purple-900);">Edit Layanan</h3>
        <form id="formEdit" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group-layanan">
                <label>Nama Layanan</label>
                <input type="text" name="nama_layanan" id="edit_nama" class="form-control-layanan" required>
            </div>
            <div class="form-group-layanan">
                <label>Harga Estimasi (Rp)</label>
                <input type="number" name="harga" id="edit_harga" class="form-control-layanan" required>
            </div>
            <div class="form-group-layanan">
                <label>Deskripsi Layanan</label>
                <textarea name="deskripsi" id="edit_deskripsi" class="form-control-layanan" rows="3" required></textarea>
            </div>
            <div class="form-group-layanan">
                <label>Ganti Gambar (Opsional)</label>
                <input type="file" name="gambar" class="form-control-layanan">
            </div>
            <button type="submit" class="btn-acc" style="width: 100%; padding: 12px; border-radius: 8px; border:none; font-weight:bold; cursor:pointer; margin-top:10px;">Update Layanan</button>
        </form>
    </div>
</div>

<script>
    function bukaModalEdit(item) {
        document.getElementById('modalEdit').style.display = 'block';
        document.getElementById('edit_nama').value = item.nama_layanan;
        document.getElementById('edit_harga').value = item.harga;
        document.getElementById('edit_deskripsi').value = item.deskripsi;
        document.getElementById('formEdit').action = `/admin/layanan/update/${item.id}`;
    }

    // Biar modal ketutup kalau user klik di luar kotak
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalTambah')) {
            document.getElementById('modalTambah').style.display = "none";
        }
        if (event.target == document.getElementById('modalEdit')) {
            document.getElementById('modalEdit').style.display = "none";
        }
    }
</script>
@endsection