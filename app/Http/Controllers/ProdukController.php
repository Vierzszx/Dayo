<?php

namespace App\Http\Controllers;

use App\Models\Produk;

class ProdukController extends CrudController
{
    protected string $model = Produk::class;

    protected string $resource = 'produk';

    protected array $fields = ['toko_id' => 'select', 'nama_produk' => 'text', 'deskripsi' => 'textarea', 'harga_normal' => 'number', 'foto' => 'file'];

    protected array $rules = ['toko_id' => 'required|integer|exists:toko,id', 'nama_produk' => 'required|string|max:100', 'deskripsi' => 'nullable|string|max:2000', 'harga_normal' => 'required|integer|min:1|max:100000000', 'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'];
}
