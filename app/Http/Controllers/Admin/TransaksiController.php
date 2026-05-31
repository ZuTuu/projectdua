<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;

class TransaksiController extends Controller
{
    // Menampilkan semua pesanan
    public function index()
    {
        $transaksi = Transaksi::with('user')->orderBy('created_at', 'desc')->get();
        return view('dashboard.admin.transaksi', compact('transaksi'));
    }

    // Fungsi mengubah status pesanan
    public function updateStatus(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $oldStatus = $transaksi->status;
        $newStatus = $request->status;
        
        // Jika status berubah menjadi "Dikonfirmasi", kurangi stok
        if ($oldStatus !== 'Dikonfirmasi' && $newStatus === 'Dikonfirmasi') {
            foreach ($transaksi->detilProduk as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $produk->stok -= $detail->jumlah;
                    $produk->save();
                }
            }
        }
        
        // Jika status berubah dari "Dikonfirmasi" ke status lain, kembalikan stok
        if ($oldStatus === 'Dikonfirmasi' && $newStatus !== 'Dikonfirmasi') {
            foreach ($transaksi->detilProduk as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $produk->stok += $detail->jumlah;
                    $produk->save();
                }
            }
        }
        
        $transaksi->status = $newStatus;
        $transaksi->save();

        return redirect()->back()->with('success', 'Status pesanan #' . $id . ' berhasil diubah menjadi: ' . $newStatus);
    }
}
