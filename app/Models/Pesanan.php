<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = ['user_id', 'penawaran_id', 'jumlah', 'total_harga', 'status'];

    public function pembeli()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penawaran()
    {
        return $this->belongsTo(Penawaran::class);
    }
}
