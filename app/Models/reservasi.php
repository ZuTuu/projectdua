<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\hewan;

/**
 * @property-read User $user
 */
class reservasi extends Model
{
    // Kasih tahu Laravel kalau nama tabelnya adalah 'reservasi'
    protected $table = 'reservasi';

protected $fillable = [
        'user_id',
        'nama_layanan',
        'tanggal',
        'tanggal_keluar',
        'waktu',
        'pet_name',
        'keluhan',
        'harga_total',
        'biaya_tambahan',
        'dp',             
        'sisa_bayar',     
        'status',
        'alasan_batal',
        'bukti_pembayaran_dp'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Tambahin relasi ini sayang, biar jadwalnya tahu ini hewan siapa
    public function hewan(): BelongsTo
    {
        return $this->belongsTo(hewan::class, 'hewan_id');
    }

    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class, 'reservasi_id');
    }
}
