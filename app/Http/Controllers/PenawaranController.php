<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;

class PenawaranController extends CrudController
{
    protected string $model = Penawaran::class;

    protected string $resource = 'penawaran';

    protected array $fields = ['produk_id' => 'select', 'harga_diskon' => 'number', 'stok' => 'number', 'mulai_pengambilan' => 'datetime-local', 'batas_pengambilan' => 'datetime-local'];

    protected array $rules = ['produk_id' => 'required|integer|exists:produk,id', 'harga_diskon' => 'required|integer|min:1|max:100000000', 'stok' => 'required|integer|min:0|max:100000', 'mulai_pengambilan' => 'required|date', 'batas_pengambilan' => 'required|date|after:mulai_pengambilan|after:now'];
}
