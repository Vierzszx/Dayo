<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    protected $table = 'penawaran';

    protected $fillable = ['produk_id', 'harga_diskon', 'stok', 'mulai_pengambilan', 'batas_pengambilan'];

    protected $casts = ['mulai_pengambilan' => 'datetime', 'batas_pengambilan' => 'datetime'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'penawaran_id');
    }
}
