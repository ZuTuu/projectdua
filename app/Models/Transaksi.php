<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function detilProduk() {
        return $this->hasMany(DetilTransaksiProduk::class, 'transaksi_id');
    }

protected $fillable = [
        'user_id',
        'total_harga',
        'status',
        'alasan_tolak', // <--- TAMBAHKAN INI
        'bukti_pembayaran',
        'metode_pengiriman',
        'tanggal_ambil',
        'stok_dikurangi',
        'metode_pembayaran',
        'kembalian'
    ];
}
