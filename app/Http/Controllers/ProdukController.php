<?php

namespace App\Http\Controllers;

use App\Models\Produk;

class ProdukController extends CrudController
{
    protected string $model = Produk::class;

    protected string $resource = 'produk';

    protected array $fields = [
        'toko_id' => 'select',
        'nama_produk' => 'text',
        'deskripsi' => 'textarea',
        'harga_normal' => 'number',
        'foto' => 'file',
    ];

    protected array $rules = [
        'toko_id' => 'required|integer|exists:toko,toko_id',
        'nama_produk' => 'required|string|max:255',
        'deskripsi' => 'nullable|string|max:2000',
        'harga_normal' => 'required|numeric|min:0.01|max:99999999.99',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ];
}