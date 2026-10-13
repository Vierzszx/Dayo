<?php

namespace App\Http\Controllers;

use App\Models\Toko;

class TokoController extends CrudController
{
    protected string $model = Toko::class;

    protected string $resource = 'toko';

    protected array $fields = [
        'nama_toko' => 'text',
        'deskripsi' => 'textarea',
        'titik_pengambilan' => 'textarea',
        'nomor_whatsapp' => 'text',
    ];

    protected array $rules = [
        'nama_toko' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'titik_pengambilan' => 'required|string|max:255',
        'nomor_whatsapp' => 'nullable|string|max:20',
    ];
}