<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class StokFixController extends Controller
{
    /**
     * Fix stok produk yang sudah dikonfirmasi tapi belum dikurangi dari stok
     * Hanya untuk admin
     */
    public function fixHistoric()
    {
        // Ambil semua transaksi yang statusnya Dikonfirmasi atau Selesai tapi stok BELUM dikurangi
        $transaksiSelesai = Transaksi::whereIn('status', ['Dikonfirmasi', 'Selesai'])
            ->where('stok_dikurangi', false)
            ->with('detilProduk')
            ->get();

        $results = [];
        $totalFixed = 0;

        foreach ($transaksiSelesai as $transaksi) {
            $transaksiFixed = false;
            
            foreach ($transaksi->detilProduk as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $stokLama = $produk->stok;
                    $produk->stok -= $detail->jumlah;
                    $produk->save();
                    $totalFixed++;
                    $transaksiFixed = true;

                    $results[] = [
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $detail->produk_id,
                        'nama_produk' => $produk->nama_produk,
                        'jumlah_dikurangi' => $detail->jumlah,
                        'stok_lama' => $stokLama,
                        'stok_baru' => $produk->stok
                    ];

                    // Log aktivitas
                    LogAktivitas::create([
                        'user_id' => Auth::id(),
                        'aktivitas' => 'Fix Stok Historis',
                        'deskripsi' => "Stok produk {$produk->nama_produk} dikurangi {$detail->jumlah} (dari transaksi #{$transaksi->id})"
                    ]);
                }
            }

            // Mark transaksi sebagai sudah dikurangi
            if ($transaksiFixed) {
                $transaksi->update(['stok_dikurangi' => true]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Fix stok selesai! Total {$totalFixed} produk diperbaiki.",
            'data' => $results
        ]);
    }
}
