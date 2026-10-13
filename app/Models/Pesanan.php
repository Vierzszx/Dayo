<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $primaryKey = 'pesanan_id';

    protected $fillable = [
        'pengguna_id',
        'penawaran_id',
        'jumlah',
        'total_harga',
        'status',
        'kode_pengambilan',
        'waktu_diambil',
    ];

    protected $casts = [
        'waktu_diambil' => 'datetime',
    ];

    public function pembeli()
    {
        return $this->belongsTo(
            User::class,
            'pengguna_id',
            'pengguna_id'
        );
    }

    public function penawaran()
    {
        return $this->belongsTo(
            Penawaran::class,
            'penawaran_id',
            'penawaran_id'
        );
    }
}