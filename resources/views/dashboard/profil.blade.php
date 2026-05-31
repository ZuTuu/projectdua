@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

    {{-- Tag H2 yang sebelumnya kepotong udah diperbaiki dan ukurannya disesuaikan --}}
    <h2 style="color: #36005E; margin-bottom: 20px; text-align: center; font-size: 22px;">Profil Saya</h2>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 8px; border-left: 5px solid #28a745; font-size: 13px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @php
        $user = auth()->user();
        $isAdminKasirOrOwner = $user && in_array($user->role, ['admin', 'kasir', 'owner']);
        $isOwner = $user && $user->role === 'owner';

        $formUpdateRoute = $isOwner
            ? route('owner.profil.update')
            : ($isAdminKasirOrOwner ? route('admin.profil.update') : route('profil.umum.update'));

        $profileSettingsRoute = $isOwner
            ? route('owner.profil')
            : ($isAdminKasirOrOwner ? route('admin.profil') : route('profil.umum'));
    @endphp

    <form action="{{ $formUpdateRoute }}" method="POST" enctype="multipart/form-data">
    @csrf

        <div style="text-align: center; margin-bottom: 25px;">
            <img id="preview-foto" src="{{ Auth::user()->foto_profil ? asset('storage/' . Auth::user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=36005E&color=fff' }}" alt="Foto Profil" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #F7F1FF; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">

            <label style="font-size: 12px; font-weight: bold; color: white; background: #36005E; padding: 6px 15px; border-radius: 20px; cursor: pointer; transition: 0.3s;">
                📷 Ganti Foto
                <input type="file" name="foto_profil" accept="image/jpeg, image/png, image/jpg" style="display: none;" onchange="previewImage(event)">
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px; color: #555; font-size: 13px;">Nama Lengkap</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}" required style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px; color: #555; font-size: 13px;">Username</label>
                <input type="text" name="username" value="{{ Auth::user()->username }}" placeholder="Contoh: mochi_lover" style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px; color: #555; font-size: 13px;">No. Handphone</label>
                <input type="text" name="no_hp" value="{{ Auth::user()->no_hp }}" placeholder="Contoh: 081234567890" style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px; color: #555; font-size: 13px;">Email</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" required style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
            <label style="font-weight: bold; display: block; margin-bottom: 10px; color: #555; font-size: 13px;">
                Ubah Password <small style="font-weight: normal; color: #888; font-size: 11px;">(Kosongkan jika tidak ingin mengubah)</small>
            </label>

            <div style="position: relative; margin-bottom: 10px;">
                <input type="password" name="password" id="password" placeholder="Password Baru" style="width: 100%; padding: 8px 35px 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                <span class="auth-toggle-password" onclick="togglePasswordVisibility('password', this)" title="Lihat Password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">👁️</span>
            </div>

            <div style="position: relative;">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ketik Ulang Password Baru" style="width: 100%; padding: 8px 35px 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                <span class="auth-toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" title="Lihat Password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">👁️</span>
            </div>
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background: #36005E; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer; box-shadow: 0 4px 6px rgba(54, 0, 94, 0.2);">Simpan Perubahan</button>

    </form>
</div>

<script>
    // Preview gambar sebelum diupload
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('preview-foto');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Toggle lihat/sembunyikan password
    function togglePasswordVisibility(inputId, icon) {
        const inputElement = document.getElementById(inputId);
        if (inputElement.type === "password") {
            inputElement.type = "text";
            icon.innerText = "🙈";
        } else {
            inputElement.type = "password";
            icon.innerText = "👁️";
        }
    }
</script>
@endsection
