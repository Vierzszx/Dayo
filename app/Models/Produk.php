<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $primaryKey = 'produk_id';

    protected $fillable = [
        'toko_id',
        'kategori_id',
        'nama_produk',
        'deskripsi',
        'harga_normal',
        'foto',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id', 'toko_id');
    }

    public function penawaran()
    {
        return $this->hasMany(
            Penawaran::class,
            'produk_id',
            'produk_id'
        );
    }
}