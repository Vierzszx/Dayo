<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = ['toko_id', 'nama_produk', 'deskripsi', 'harga_normal', 'foto'];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function penawaran()
    {
        return $this->hasMany(Penawaran::class, 'produk_id');
    }
}
