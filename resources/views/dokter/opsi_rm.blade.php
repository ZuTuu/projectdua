@extends('layouts.app')

@section('title', 'Kelola Opsi Rekam Medis - Paw Center')

@section('content')
<style>
    /* ===== LAYOUT ===== */
    .orm-wrap { display: grid; grid-template-columns: 260px 1fr; gap: 24px; align-items: start; }
    @media (max-width: 900px) { .orm-wrap { grid-template-columns: 1fr; } }

    /* ===== SIDEBAR KATEGORI ===== */
    .kat-sidebar { background: #fff; border-radius: 14px; border: 1px solid #e8e0f5; box-shadow: 0 4px 16px rgba(128,0,128,.06); overflow: hidden; position: sticky; top: 20px; }
    .kat-sidebar h3 { padding: 16px 20px; margin: 0; font-size: 14px; font-weight: 700; color: #800080; background: #faf5ff; border-bottom: 1px solid #e8e0f5; }
    .kat-item { display: flex; align-items: center; gap: 10px; padding: 12px 20px; cursor: pointer; font-size: 13.5px; color: #444; border-left: 3px solid transparent; transition: all .2s; text-decoration: none; border-bottom: 1px solid #f5f0fa; }
    .kat-item:hover { background: #faf5ff; color: #800080; border-left-color: #c084fc; }
    .kat-item.active { background: #f3e8ff; color: #800080; border-left-color: #800080; font-weight: 700; }
    .kat-badge { margin-left: auto; background: #800080; color: #fff; border-radius: 20px; padding: 2px 9px; font-size: 11px; font-weight: 700; min-width: 24px; text-align: center; }
    .kat-badge.zero { background: #e0e0e0; color: #999; }

    /* ===== PANEL UTAMA ===== */
    .orm-panel { background: #fff; border-radius: 14px; border: 1px solid #e8e0f5; box-shadow: 0 4px 16px rgba(128,0,128,.06); }
    .orm-panel-header { padding: 20px 24px; border-bottom: 1px solid #f0e8ff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .orm-panel-header h2 { margin: 0; font-size: 18px; color: #800080; font-weight: 700; }
    .orm-panel-body { padding: 24px; }

    /* ===== FORM TAMBAH ===== */
    .add-form { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; background: #faf5ff; padding: 16px 20px; border-radius: 10px; border: 1px dashed #c084fc; margin-bottom: 24px; }
    .add-form label { font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 5px; }
    .add-form input, .add-form select { padding: 9px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; color: #333; outline: none; transition: border .2s; }
    .add-form input:focus, .add-form select:focus { border-color: #800080; }
    .form-group-inline { display: flex; flex-direction: column; }
    .form-group-inline.grow { flex: 1; min-width: 160px; }
    .form-group-inline.urutan { width: 80px; }

    /* ===== TABEL OPSI ===== */
    .opsi-table { width: 100%; border-collapse: collapse; }
    .opsi-table th { background: #faf5ff; color: #800080; padding: 10px 14px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #e8e0f5; }
    .opsi-table td { padding: 11px 14px; border-bottom: 1px solid #f5f0fa; font-size: 13.5px; vertical-align: middle; }
    .opsi-table tr:last-child td { border-bottom: none; }
    .opsi-table tr:hover td { background: #fdf9ff; }

    /* ===== TOMBOL ===== */
    .btn-xs { padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; border: none; cursor: pointer; transition: .2s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .btn-add   { background: #800080; color: #fff; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; white-space: nowrap; }
    .btn-add:hover { background: #6a006a; }
    .btn-edit  { background: #e8f0fe; color: #1a73e8; }
    .btn-edit:hover { background: #1a73e8; color: #fff; }
    .btn-del   { background: #fff0f0; color: #c62828; }
    .btn-del:hover { background: #c62828; color: #fff; }

    /* ===== MODAL EDIT ===== */
    .modal-bg { display: none; position: fixed; z-index: 100; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,.45); }
    .modal-box { background: #fff; margin: 8% auto; padding: 28px; border-radius: 14px; width: 420px; max-width: 94%; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
    .modal-box h3 { margin: 0 0 20px; color: #800080; font-size: 17px; }
    .modal-field { margin-bottom: 14px; }
    .modal-field label { font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 5px; }
    .modal-field input { width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13.5px; box-sizing: border-box; }
    .modal-field input:focus { border-color: #800080; outline: none; }
    .modal-actions { display: flex; gap: 10px; margin-top: 20px; }

    /* ===== ALERT ===== */
    .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
    .alert-success { background: #e6f4ea; color: #1e8e3e; border: 1px solid #b2dfdb; }
    .alert-error   { background: #fce4ec; color: #c62828; border: 1px solid #f8bbd0; }

    /* ===== EMPTY ===== */
    .empty-opsi { text-align: center; color: #aaa; padding: 30px; font-size: 13px; }
</style>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="margin:0; color:#800080; font-size:22px;">⚙️ Kelola Opsi Rekam Medis</h2>
            <p style="margin:4px 0 0; color:#888; font-size:13px;">Tambah, edit, atau hapus pilihan dropdown pada form rekam medis. Perubahan langsung berlaku di form pemeriksaan.</p>
        </div>
        <a href="{{ route('dashboard.dokter') }}" class="btn-xs btn-edit" style="padding:9px 16px; font-size:13px;">← Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="orm-wrap">

        {{-- ===== SIDEBAR KATEGORI ===== --}}
        <div class="kat-sidebar">
            <h3>📂 Kategori</h3>
            @foreach($kategoriList as $key => $label)
                @php $jumlah = $opsiPerKategori[$key]->count(); @endphp
                <a href="#kat-{{ $key }}" class="kat-item" onclick="aktivasiKat('{{ $key }}')">
                    {{ $label }}
                    <span class="kat-badge {{ $jumlah === 0 ? 'zero' : '' }}">{{ $jumlah }}</span>
                </a>
            @endforeach
        </div>

        {{-- ===== PANEL UTAMA ===== --}}
        <div class="orm-panel">
            @foreach($kategoriList as $key => $label)
            <div id="kat-{{ $key }}" class="kat-section" style="{{ !$loop->first ? 'display:none;' : '' }}">
                <div class="orm-panel-header">
                    <h2>{{ $label }}</h2>
                    <span style="font-size:12px; color:#999;">{{ $opsiPerKategori[$key]->count() }} opsi terdaftar</span>
                </div>
                <div class="orm-panel-body">

                    {{-- FORM TAMBAH --}}
                    <form action="{{ route('dokter.opsi-rm.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kategori" value="{{ $key }}">
                        <div class="add-form">
                            <div class="form-group-inline grow">
                                <label>Nama Opsi Baru</label>
                                <input type="text" name="nilai" placeholder="Contoh: Aktif, Normal, ..." required maxlength="150">
                            </div>
                            <div class="form-group-inline urutan">
                                <label>Urutan</label>
                                <input type="number" name="urutan" value="0" min="0" style="width:70px;">
                            </div>
                            <div class="form-group-inline" style="justify-content:flex-end;">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn-add">+ Tambah</button>
                            </div>
                        </div>
                    </form>

                    {{-- TABEL OPSI --}}
                    @if($opsiPerKategori[$key]->count() > 0)
                    <div style="overflow-x:auto;">
                        <table class="opsi-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Opsi</th>
                                    <th style="width:70px;">Urutan</th>
                                    <th style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($opsiPerKategori[$key] as $i => $opsi)
                                <tr>
                                    <td style="color:#bbb; font-size:12px;">{{ $i + 1 }}</td>
                                    <td><strong>{{ $opsi->nilai }}</strong></td>
                                    <td style="color:#888;">{{ $opsi->urutan }}</td>
                                    <td>
                                        <div style="display:flex; gap:5px;">
                                            {{-- Edit --}}
                                            <button class="btn-xs btn-edit"
                                                onclick="openEdit({{ $opsi->id }}, '{{ addslashes($opsi->nilai) }}', {{ $opsi->urutan }})">
                                                ✏️ Edit
                                            </button>

                                            {{-- Hapus --}}
                                            <form action="{{ route('dokter.opsi-rm.destroy', $opsi->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus opsi \"{{ addslashes($opsi->nilai) }}\" secara permanen?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-xs btn-del">🗑️ Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="empty-opsi">
                            Belum ada opsi untuk kategori ini.<br>
                            <span style="font-size:12px;">Gunakan form di atas untuk menambahkan.</span>
                        </div>
                    @endif

                </div>{{-- /orm-panel-body --}}
            </div>{{-- /kat-section --}}
            @endforeach
        </div>{{-- /orm-panel --}}

    </div>{{-- /orm-wrap --}}
</div>

{{-- ===== MODAL EDIT ===== --}}
<div class="modal-bg" id="editModal">
    <div class="modal-box">
        <h3>✏️ Edit Opsi</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-field">
                <label>Nama Opsi</label>
                <input type="text" name="nilai" id="editNilai" required maxlength="150">
            </div>
            <div class="modal-field">
                <label>Urutan (angka lebih kecil = tampil lebih awal)</label>
                <input type="number" name="urutan" id="editUrutan" min="0">
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-add" style="flex:1;">Simpan Perubahan</button>
                <button type="button" class="btn-xs btn-edit" style="padding:10px 16px; font-size:13px;"
                    onclick="closeEdit()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ===== SIDEBAR NAVIGASI =====
    const sections = document.querySelectorAll('.kat-section');
    const katItems = document.querySelectorAll('.kat-item');

    function aktivasiKat(key) {
        sections.forEach(s => s.style.display = 'none');
        katItems.forEach(i => i.classList.remove('active'));

        const target = document.getElementById('kat-' + key);
        if (target) target.style.display = 'block';

        const link = [...katItems].find(i => i.getAttribute('href') === '#kat-' + key);
        if (link) link.classList.add('active');
    }

    // Aktifkan sidebar item pertama saat load
    document.addEventListener('DOMContentLoaded', function () {
        if (katItems.length > 0) katItems[0].classList.add('active');
    });

    // ===== MODAL EDIT =====
    function openEdit(id, nilai, urutan) {
        const base = '{{ url("/dokter/opsi-rekam-medis") }}';
        document.getElementById('editForm').action = base + '/' + id;
        document.getElementById('editNilai').value = nilai;
        document.getElementById('editUrutan').value = urutan;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEdit() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Tutup modal jika klik background
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEdit();
    });
</script>
@endsection
