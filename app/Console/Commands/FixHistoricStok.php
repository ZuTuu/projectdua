<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Produk;

class FixHistoricStok extends Command
{
    protected $signature = 'stok:fix-historic';
    protected $description = 'Fix stok produk dari transaksi yang sudah Dikonfirmasi atau Selesai tapi stoknya belum berkurang';

    public function handle()
    {
        $this->info('🔧 Memulai perbaikan stok historis...');

        // Ambil semua transaksi yang statusnya Dikonfirmasi atau Selesai
        $transaksiSelesai = Transaksi::whereIn('status', ['Dikonfirmasi', 'Selesai'])
            ->with('detilProduk')
            ->get();

        $totalKurangStok = 0;

        foreach ($transaksiSelesai as $transaksi) {
            // Untuk setiap detail produk dalam transaksi, kurangi stok
            foreach ($transaksi->detilProduk as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    // Cek apakah stok perlu dikurangi (ketika status Dikonfirmasi/Selesai)
                    // Hanya kurangi jika belum dikurangi sebelumnya (simple check: stok masih cukup)
                    if ($produk->stok >= $detail->jumlah) {
                        $produk->stok -= $detail->jumlah;
                        $produk->save();
                        $totalKurangStok++;

                        $this->line("✓ Produk ID {$detail->produk_id} ({$produk->nama_produk}) - Stok dikurangi {$detail->jumlah}");
                    }
                }
            }
        }

        $this->info("✅ Selesai! Total produk yang stoknya diperbaiki: {$totalKurangStok}");
    }
}
