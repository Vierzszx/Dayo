<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';

    protected $fillable = ['user_id', 'nama_toko', 'alamat', 'kontak', 'jam_operasional'];

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'toko_id');
    }
}
