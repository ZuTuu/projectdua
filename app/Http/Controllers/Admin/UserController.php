<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Menampilkan daftar semua user
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('dashboard.admin.users.index', compact('users'));
    }

    // Menambah user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,kasir,pelanggan,dokter,staff,owner'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'aktif' // Default status
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    // Mengupdate data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,kasir,pelanggan,dokter,staff,owner',
            'status' => 'required|in:aktif,blokir'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        // Update password cuma kalau diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Data akun berhasil diperbarui!');
    }

    // Menghapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus permanen!');
    }

    // Fitur khusus: Memulihkan akun yang terblokir
    public function unblock($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'aktif';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Yey! Akun berhasil dipulihkan dan bisa login lagi.');
    }
}
