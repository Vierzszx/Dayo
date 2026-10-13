<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;

class PenawaranController extends CrudController
{
    protected string $model = Penawaran::class;

    protected string $resource = 'penawaran';

    protected array $fields = [
        'produk_id' => 'select',
        'harga_diskon' => 'number',
        'stok' => 'number',
        'waktu_mulai' => 'datetime-local',
        'batas_pengambilan' => 'datetime-local',
    ];

    protected array $rules = [
        'produk_id' => 'required|integer|exists:produk,produk_id',
        'harga_diskon' => 'required|numeric|min:0.01|max:99999999.99',
        'stok' => 'required|integer|min:0|max:100000',
        'waktu_mulai' => 'required|date',
        'batas_pengambilan' => [
            'required',
            'date',
            'after:waktu_mulai',
            'after:now',
        ],
    ];
}