<?php

namespace App\Http\Controllers;

use App\Models\Toko;

class TokoController extends CrudController
{
    protected string $model = Toko::class;

    protected string $resource = 'toko';

    protected array $fields = ['nama_toko' => 'text', 'alamat' => 'textarea', 'kontak' => 'text', 'jam_operasional' => 'text'];

    protected array $rules = ['nama_toko' => 'required|string|max:100', 'alamat' => 'required|string|max:1000', 'kontak' => 'required|string|max:30', 'jam_operasional' => 'nullable|string|max:100'];
}
