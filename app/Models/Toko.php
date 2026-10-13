<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';

    protected $primaryKey = 'toko_id';

    protected $fillable = [
        'pengguna_id',
        'nama_toko',
        'deskripsi',
        'titik_pengambilan',
        'nomor_whatsapp',
    ];

    public function pemilik()
    {
        return $this->belongsTo(
            User::class,
            'pengguna_id',
            'pengguna_id'
        );
    }

    public function produk()
    {
        return $this->hasMany(
            Produk::class,
            'toko_id',
            'toko_id'
        );
    }
}