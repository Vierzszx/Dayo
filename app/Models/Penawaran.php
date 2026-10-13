<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    protected $table = 'penawaran';

    protected $primaryKey = 'penawaran_id';

    protected $fillable = [
        'produk_id',
        'harga_diskon',
        'stok',
        'waktu_mulai',
        'batas_pengambilan',
        'status',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'batas_pengambilan' => 'datetime',
    ];

    public function produk()
    {
        return $this->belongsTo(
            Produk::class,
            'produk_id',
            'produk_id'
        );
    }

    public function pesanan()
    {
        return $this->hasMany(
            Pesanan::class,
            'penawaran_id',
            'penawaran_id'
        );
    }
}