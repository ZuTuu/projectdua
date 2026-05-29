<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    // 1. Menampilkan Halaman Kelola Katalog
    public function index(Request $request)
        {
            $search = $request->input('search');

            // Query buat nampilin data, kalo ada search dia bakal filter
            $produk = Produk::when($search, function ($query, $search) {
                return $query->where('nama_produk', 'like', '%' . $search . '%')
                            ->orWhere('kategori', 'like', '%' . $search . '%');
            })->paginate(20)->appends(['search' => $search]);
            // appends() itu penting banget biar keyword search-nya tetep nempel pas kita klik Next Page

            return view('dashboard.admin.katalog.index', compact('produk'));
        }

    // 2. Menyimpan Data Produk Baru (Create)
    public function store(Request $request)
    {
        // Validasi inputan dari form
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori_produk,id', // Kalau kamu pakai relasi kategori
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Maksimal 2MB
        ]);

        $data = $request->except('_token');

        // Proses upload gambar kalau kasir/admin masukin gambar
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('katalog', 'public');
        }

        Produk::create($data);

        return redirect()->route('admin.katalog.index')->with('success', 'Yey! Produk baru berhasil ditambahkan ke katalog.');
    }

    // 3. Memperbarui Data Produk (Update)
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        // Validasi inputan
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori_produk,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except(['_token', '_method']);

        // Cek kalau admin upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari storage biar nggak menuh-menuhin memori
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('katalog', 'public');
        }

        $produk->update($data);

        return redirect()->route('admin.katalog.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    // 4. Menghapus Data Produk (Delete)
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus file gambar dari folder public/storage kalau ada
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.katalog.index')->with('success', 'Produk berhasil dihapus dari katalog!');
    }
}
