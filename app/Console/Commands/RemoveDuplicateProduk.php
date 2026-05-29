<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateProduk extends Command
{
    protected $signature = 'produk:dedupe';
    protected $description = 'Remove duplicate rows in produk table based on (nama_produk, kategori) keeping the smallest id.';

    public function handle(): int
    {
        $removed = 0;

        // Pastikan kategori tidak null biar grouping konsisten
        // (produk.kategori nullable di migration)
        $rows = DB::table('produk')
            ->select(DB::raw('MIN(id) as keep_id, CONCAT(nama_produk, "-", COALESCE(kategori, "")) as grp, COUNT(*) as cnt'))
            ->groupBy(DB::raw('CONCAT(nama_produk, "-", COALESCE(kategori, ""))'))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($rows as $r) {
            $keepId = (int)$r->keep_id;

            // Hapus semua yang duplikat selain keepId untuk grup tersebut
            // Grup dibentuk dari (nama_produk, kategori)
            $parts = explode('-', $r->grp, 2);
            $nama = $parts[0] ?? '';
            $kategori = $parts[1] ?? '';

            $deletedNow = DB::table('produk')
                ->where('id', '!=', $keepId)
                ->where('nama_produk', $nama)
                ->where(function ($q) use ($kategori) {
                    if ($kategori === '') {
                        $q->whereNull('kategori');
                    } else {
                        $q->where('kategori', $kategori);
                    }
                })
                ->delete();

            $removed += (int)$deletedNow;
        }

        $this->info("Produk duplicate rows removed: {$removed}");
        return 0;
    }
}

