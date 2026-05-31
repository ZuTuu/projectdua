<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    // Sambungin ke nama tabel kamu yang ada di phpMyAdmin
    protected $table = 'rekam_medis';
    protected $guarded = []; // Biar semua kolom bisa diisi otomatis

    // Relasi balik ke tabel-tabel lain
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function hewan() {
        return $this->belongsTo(hewan::class, 'hewan_id');
    }
    public function reservasi() {
        return $this->belongsTo(reservasi::class, 'reservasi_id');
    }

    protected $fillable = [
        'reservasi_id',
        'user_id',
        'nama_dokter', // <--- INI WAJIB ADA BIAR NAMANYA MASUK!
        'hewan_id',
        'diagnosa',
        'tindakan',
        'catatan',     // <--- INI JUGA WAJIB ADA!
        'tanggal_periksa'
    ];
}
