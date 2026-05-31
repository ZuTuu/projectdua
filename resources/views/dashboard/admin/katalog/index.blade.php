@extends('layouts.app')

@section('title', 'Kelola Katalog Produk')

@section('content')
<div class="content">
    <div class="admin-header">
        <h2>Kelola Katalog Produk 📦</h2>
        <button onclick="document.getElementById('modalTambah').style.display='block'" class="btn-acc" style="padding: 10px 20px; border-radius: 8px; border:none; cursor:pointer; font-weight:bold;">+ Tambah Produk Baru</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.katalog.index') }}" method="GET" class="search-container">
        <input type="text" name="search" class="search-input" placeholder="Cari nama produk atau kategori..." value="{{ request('search') }}">
        <button type="submit" class="btn-acc" style="padding: 0 20px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer;">Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.katalog.index') }}" class="btn-tolak" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; font-weight: bold;">Reset</a>
        @endif
    </form>

    <div style="overflow-x: auto;">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk as $index => $item)
                <tr>
                    <td>{{ $produk->firstItem() + $index }}</td>
                    <td><img src="{{ $item->gambar }}" width="50" style="border-radius: 6px; border: 1px solid var(--purple-100);" alt="{{ $item->nama_produk }}"></td>
                    <td><strong>{{ $item->nama_produk }}</strong></td>
                    <td><span class="badge" style="background: var(--purple-50); color: var(--purple-800);">{{ $item->kategori }}</span></td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" onclick="bukaModalEdit({{ $item }})" class="btn-sm btn-acc">
                                Edit
                            </button>

                            <form action="{{ route('admin.katalog.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin sayang mau hapus {{ addslashes($item->nama_produk) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm btn-tolak" style="border: none;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #6c757d;">
                        Wah, produknya nggak ketemu. Coba kata kunci lain ya!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
        {{ $produk->links('pagination::bootstrap-5') }}
    </div>
</div>

<div id="modalTambah" class="modal-katalog">
    <div class="modal-content-katalog">
        <span class="close-katalog" onclick="document.getElementById('modalTambah').style.display='none'">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--purple-900);">Tambah Produk Baru</h3>
        <form action="{{ route('admin.katalog.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group-katalog"><label>Nama Produk</label><input type="text" name="nama_produk" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Kategori</label><input type="text" name="kategori" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Harga</label><input type="number" name="harga" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Stok</label><input type="number" name="stok" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Deskripsi</label><textarea name="deskripsi" class="form-control-katalog" rows="3"></textarea></div>
            <div class="form-group-katalog"><label>Gambar Produk</label><input type="file" name="gambar" class="form-control-katalog"></div>
            <button type="submit" class="btn-acc" style="width: 100%; padding: 12px; border-radius: 8px; border:none; font-weight:bold; margin-top:10px;">Simpan Produk</button>
        </form>
    </div>
</div>

<div id="modalEdit" class="modal-katalog">
    <div class="modal-content-katalog">
        <span class="close-katalog" onclick="document.getElementById('modalEdit').style.display='none'">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--purple-900);">Edit Produk</h3>
        <form id="formEdit" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group-katalog"><label>Nama Produk</label><input type="text" name="nama_produk" id="edit_nama" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Kategori</label><input type="text" name="kategori" id="edit_kategori" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Harga</label><input type="number" name="harga" id="edit_harga" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Stok</label><input type="number" name="stok" id="edit_stok" class="form-control-katalog" required></div>
            <div class="form-group-katalog"><label>Deskripsi</label><textarea name="deskripsi" id="edit_deskripsi" class="form-control-katalog" rows="3"></textarea></div>
            <div class="form-group-katalog"><label>Gambar Baru (Opsional)</label><input type="file" name="gambar" class="form-control-katalog"></div>
            <button type="submit" class="btn-acc" style="width: 100%; padding: 12px; border-radius: 8px; border:none; font-weight:bold; margin-top:10px;">Update Produk</button>
        </form>
    </div>
</div>

<script>
    function bukaModalEdit(item) {
        document.getElementById('modalEdit').style.display = 'block';
        document.getElementById('edit_nama').value = item.nama_produk;

        let kategoriInput = document.getElementById('edit_kategori');
        if(kategoriInput) kategoriInput.value = item.kategori;

        document.getElementById('edit_harga').value = item.harga;
        document.getElementById('edit_stok').value = item.stok;
        document.getElementById('edit_deskripsi').value = item.deskripsi;
        document.getElementById('formEdit').action = `/admin/katalog/update/${item.id}`;
    }

    // Tutup modal kalau user klik di luar area kotak putih
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