<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    // 1. Menampilkan Halaman Kelola Layanan
    public function index()
    {
        // Ambil semua data layanan dari database
        $layanan = Layanan::orderBy('created_at', 'desc')->get();

        // Arahkan ke file view khusus layanan (pastikan file blade-nya sudah kamu buat ya!)
        return view('dashboard.admin.layanan.index', compact('layanan'));
    }

    // 2. Menyimpan Data Layanan Baru (Create)
    public function store(Request $request)
    {
        // Validasi form input
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Gambar opsional, maksimal 2MB
        ]);

        $data = $request->except('_token');

        // Kalau ada file gambar yang diupload, simpan di folder public/storage/layanan
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('layanan', 'public');
        }

        Layanan::create($data);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan klinik baru berhasil ditambahkan!');
    }

    // 3. Memperbarui Data Layanan (Update)
    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        // Validasi form input
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except(['_token', '_method']);

        // Kalau admin upload gambar baru buat update
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dulu biar nggak numpuk
            if ($layanan->gambar && Storage::disk('public')->exists($layanan->gambar)) {
                Storage::disk('public')->delete($layanan->gambar);
            }
            // Simpan gambar yang baru
            $data['gambar'] = $request->file('gambar')->store('layanan', 'public');
        }

        $layanan->update($data);

        return redirect()->route('admin.layanan.index')->with('success', 'Data layanan berhasil diperbarui!');
    }

    // 4. Menghapus Data Layanan (Delete)
    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);

        // Hapus file gambar dari memori kalau ada
        if ($layanan->gambar && Storage::disk('public')->exists($layanan->gambar)) {
            Storage::disk('public')->delete($layanan->gambar);
        }

        $layanan->delete();

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus dari sistem!');
    }
}
