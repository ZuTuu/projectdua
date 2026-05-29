<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class hewan extends Model
{
    // Kasih tahu Laravel kalau nama tabelnya adalah 'hewan'
    protected $table = 'hewan';

    // Biar bisa isi data (Mass Assignment)
    protected $guarded = [];
}
