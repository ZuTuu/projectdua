@extends('layouts.app')

@section('title', 'Data Hewan Peliharaan')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: #800080;">🐾 Data Hewan Peliharaan</h2>
    <button onclick="openModal('modalTambah')" style="background: #800080; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">+ Tambah Anabul</button>
</div>

@if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; border-left: 5px solid #28a745;">
        ✅ {{ session('success') }}
    </div>
@endif

<div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
        <thead>
            <tr style="border-bottom: 2px solid #E6E6FA; text-align: left; color: #555;">
                <th style="padding: 12px;">No</th>
                <th style="padding: 12px;">Nama Anabul</th>
                <th style="padding: 12px;">Jenis</th>
                <th style="padding: 12px;">Ras</th>
                <th style="padding: 12px;">Umur</th>
                <th style="padding: 12px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($semua_hewan as $key => $h)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px;">{{ $key + 1 }}</td>
                <td style="padding: 12px; font-weight: bold; color: #800080;">{{ $h->nama_hewan }}</td>
                <td style="padding: 12px;">{{ $h->jenis_hewan }}</td>
                <td style="padding: 12px; color: #666;">{{ $h->ras ?? '-' }}</td>
                <td style="padding: 12px; color: #666;">{{ $h->umur ?? '-' }}</td>
                <td style="padding: 12px; text-align: center; display: flex; justify-content: center; gap: 8px;">
                    <button onclick="openEditModal('{{ $h->id }}', '{{ $h->nama_hewan }}', '{{ $h->jenis_hewan }}', '{{ $h->ras }}', '{{ $h->umur }}')" style="background: #ffc107; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">✏️</button>

                    <form action="{{ route('hewan.hapus', $h->id) }}" method="POST" onsubmit="return confirm('Hapus data {{ $h->nama_hewan }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Belum ada data hewan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="modalTambah" class="modal-custom">
    <div class="modal-content-hewan">
        <h3 style="margin-bottom: 15px; color: #800080;">Tambah Anabul Baru</h3>
        <form action="{{ route('hewan.store') }}" method="POST">
            @csrf
            <div class="form-group-hewan">
                <label>Nama Anabul</label>
                <input type="text" name="nama_hewan" required placeholder="Contoh: Mochi">
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label>Jenis</label>
                    <select name="jenis_hewan" required>
                        <option value="" disabled selected>Pilih Jenis</option>
                        <option value="Kucing">Kucing</option>
                        <option value="Anjing">Anjing</option>
                        <option value="Kelinci">Kelinci</option>
                        <option value="Hamster">Hamster</option>
                        <option value="Burung">Burung</option>
                        <option value="Reptil">Reptil</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Umur</label>
                    <div style="display: flex; gap: 5px;">
                <input type="number" name="umur_angka" required min="1" max="50" placeholder="Angka" style="width: 50%;">
                        <select name="umur_satuan" required style="width: 50%;">
                            <option value="Bulan">Bulan</option>
                            <option value="Tahun">Tahun</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Ras / Keturunan</label>
                <select name="ras" required>
                    <option value="" disabled selected>Pilih Ras</option>
                    <optgroup label="Kucing">
                        <option value="Domestik (Kampung)">Domestik (Kampung)</option>
                        <option value="Persia">Persia</option>
                        <option value="Anggora">Anggora</option>
                        <option value="Maine Coon">Maine Coon</option>
                        <option value="Himalaya">Himalaya</option>
                        <option value="British Shorthair">British Shorthair</option>
                    </optgroup>
                    <optgroup label="Anjing">
                        <option value="Domestik (Kampung)">Domestik (Kampung)</option>
                        <option value="Golden Retriever">Golden Retriever</option>
                        <option value="Pomeranian">Pomeranian</option>
                        <option value="Siberian Husky">Siberian Husky</option>
                        <option value="Bulldog">Bulldog</option>
                        <option value="Chihuahua">Chihuahua</option>
                    </optgroup>
                    <optgroup label="Lainnya">
                        <option value="Campuran (Mix)">Campuran (Mix)</option>
                        <option value="Tidak Diketahui">Tidak Diketahui</option>
                        <option value="Lainnya">Lainnya</option>
                    </optgroup>
                </select>
            </div>
            <button type="submit" class="btn-save-hewan">Simpan Anabul</button>
            <button type="button" onclick="closeModal('modalTambah')" style="width: 100%; background: none; border: none; color: #999; margin-top: 10px; cursor: pointer;">Batal</button>
        </form>
    </div>
</div>

<div id="modalEdit" class="modal-custom">
    <div class="modal-content">
        <h3 style="margin-bottom: 15px; color: #ffc107;">Edit Data Anabul</h3>
        <form id="formEdit" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Anabul</label>
                <input type="text" name="nama_hewan" id="edit_nama" required>
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label>Jenis</label>
                    <select name="jenis_hewan" id="edit_jenis" required>
                        <option value="Kucing">Kucing</option>
                        <option value="Anjing">Anjing</option>
                        <option value="Kelinci">Kelinci</option>
                        <option value="Hamster">Hamster</option>
                        <option value="Burung">Burung</option>
                        <option value="Reptil">Reptil</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Umur</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="number" name="umur_angka" id="edit_umur_angka" required min="1" max="50" style="width: 50%;">
                        <select name="umur_satuan" id="edit_umur_satuan" required style="width: 50%;">
                            <option value="Bulan">Bulan</option>
                            <option value="Tahun">Tahun</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Ras / Keturunan</label>
                <select name="ras" id="edit_ras" required>
                    <optgroup label="Kucing">
                        <option value="Domestik (Kampung)">Domestik (Kampung)</option>
                        <option value="Persia">Persia</option>
                        <option value="Anggora">Anggora</option>
                        <option value="Maine Coon">Maine Coon</option>
                        <option value="Himalaya">Himalaya</option>
                        <option value="British Shorthair">British Shorthair</option>
                    </optgroup>
                    <optgroup label="Anjing">
                        <option value="Domestik (Kampung)">Domestik (Kampung)</option>
                        <option value="Golden Retriever">Golden Retriever</option>
                        <option value="Pomeranian">Pomeranian</option>
                        <option value="Siberian Husky">Siberian Husky</option>
                        <option value="Bulldog">Bulldog</option>
                        <option value="Chihuahua">Chihuahua</option>
                    </optgroup>
                    <optgroup label="Lainnya">
                        <option value="Campuran (Mix)">Campuran (Mix)</option>
                        <option value="Tidak Diketahui">Tidak Diketahui</option>
                        <option value="Lainnya">Lainnya</option>
                    </optgroup>
                </select>
            </div>
            <button type="submit" class="btn-save" style="background: #ffc107;">Update Data</button>
            <button type="button" onclick="closeModal('modalEdit')" style="width: 100%; background: none; border: none; color: #999; margin-top: 10px; cursor: pointer;">Batal</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function openEditModal(id, nama, jenis, ras, umur) {
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_jenis').value = jenis;

        // Memastikan ras yang dipilih sesuai dengan dropdown (kasus sensitif)
        let rasDropdown = document.getElementById('edit_ras');
        let rasFound = false;
        for(let i=0; i<rasDropdown.options.length; i++) {
            if(rasDropdown.options[i].value === ras) {
                rasDropdown.selectedIndex = i;
                rasFound = true;
                break;
            }
        }
        if(!rasFound) document.getElementById('edit_ras').value = 'Lainnya'; // Fallback jika data ras lama tidak ada di list

        // Memecah umur yang bentuknya string (misal: "2 Tahun") jadi angka dan satuan
        if(umur) {
            let umurSplit = umur.split(" ");
            if(umurSplit.length >= 2) {
                document.getElementById('edit_umur_angka').value = umurSplit[0];
                document.getElementById('edit_umur_satuan').value = umurSplit[1];
            } else {
                document.getElementById('edit_umur_angka').value = umur;
            }
        }

        document.getElementById('formEdit').action = "/data-hewan/update/" + id;
        openModal('modalEdit');
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal-custom') {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection
