@extends('layouts.app')

@section('title', 'Kelola Akun Pengguna')

@section('content')
<div class="content">
    <div class="admin-header">
        <h2>Kelola Akun Pengguna 👥</h2>
        <button onclick="document.getElementById('modalTambah').style.display='block'" class="btn-acc" style="padding: 10px 20px; border-radius: 8px; border:none; cursor:pointer; font-weight:bold;">+ Tambah Akun Baru</button>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="admin-card">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span style="text-transform: uppercase; font-size: 12px; font-weight:bold; color: var(--purple-600);">{{ $user->role }}</span></td>
                        <td>
                            <span class="badge {{ $user->status == 'aktif' ? 'badge-aktif' : 'badge-blokir' }}">
                                {{ ucfirst($user->status ?? 'aktif') }}
                            </span>
                        </td>
                        <td style="display: flex; gap: 5px;">
                            @if($user->status == 'blokir')
                                <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-sm btn-warning">Pulihkan</button>
                                </form>
                            @endif

                            <button onclick="bukaModalEdit({{ $user }})" class="btn-sm btn-acc">Edit</button>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini permanen?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-tolak">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalTambah" class="modal-user">
    <div class="modal-content-user">
        <span class="close-user" onclick="document.getElementById('modalTambah').style.display='none'">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--purple-900);">Tambah Akun Baru</h3>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group-user">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control-user" required>
            </div>
            <div class="form-group-user">
                <label>Email</label>
                <input type="email" name="email" class="form-control-user" required>
            </div>
            <div class="form-group-user">
                <label>Password</label>
                <input type="password" name="password" class="form-control-user" required>
            </div>
            <div class="form-group-user">
                <label>Role / Peran</label>
                <select name="role" class="form-control-user" required>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="dokter">Dokter</option>
                    <option value="staff">Staff</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <button type="submit" class="btn-acc" style="width: 100%; padding: 12px; border-radius: 8px; border:none; cursor:pointer; font-weight:bold; margin-top: 10px;">Simpan Akun</button>
        </form>
    </div>
</div>

<div id="modalEdit" class="modal-user">
    <div class="modal-content-user">
        <span class="close-user" onclick="document.getElementById('modalEdit').style.display='none'">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--purple-900);">Edit Data Akun</h3>
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <div class="form-group-user">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control-user" required>
            </div>
            <div class="form-group-user">
                <label>Email</label>
                <input type="email" name="email" id="edit_email" class="form-control-user" required>
            </div>
            <div class="form-group-user">
                <label>Password Baru (Kosongkan jika tidak ingin ganti)</label>
                <input type="password" name="password" class="form-control-user">
            </div>
            <div class="form-group-user">
                <label>Role / Peran</label>
                <select name="role" id="edit_role" class="form-control-user" required>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="dokter">Dokter</option>
                    <option value="staff">Staff</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <div class="form-group-user">
                <label>Status Akun</label>
                <select name="status" id="edit_status" class="form-control-user" required>
                    <option value="aktif">Aktif</option>
                    <option value="blokir">Blokir</option>
                </select>
            </div>
            <button type="submit" class="btn-acc" style="width: 100%; padding: 12px; border-radius: 8px; border:none; cursor:pointer; font-weight:bold; margin-top: 10px;">Update Data</button>
        </form>
    </div>
</div>

<script>
    function bukaModalEdit(user) {
        document.getElementById('modalEdit').style.display = 'block';
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_status').value = user.status || 'aktif';
        document.getElementById('formEdit').action = `/admin/users/update/${user.id}`;
    }

    // Klik di luar buat nutup modal
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