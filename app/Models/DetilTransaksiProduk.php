<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetilTransaksiProduk extends Model
{
    protected $table = 'detil_transaksi_produk';
    protected $guarded = [];

    public function produk() {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Tambahin relasi ini ya sayang biar bisa diakses dari controller
    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
