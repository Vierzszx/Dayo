<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

abstract class CrudController extends Controller
{
    protected string $model;

    protected string $resource;

    protected array $fields;

    protected array $rules;

    protected function seller()
    {
        abort_unless(auth()->user()->peran === 'seller', 403);
    }

    protected function owner($item)
    {
        $owner = match ($this->resource) {
            'toko' => $item->pengguna_id,'produk' => $item->toko->pengguna_id,'penawaran' => $item->produk->toko->pengguna_id
        };
        abort_unless($owner === auth()->id(), 403);
    }

    protected function options()
    {
        return match ($this->resource) {
            'produk' => [
                'toko_id' => Toko::where('pengguna_id', auth()->id())
                    ->pluck('nama_toko', 'toko_id'),
            ],
    
            'penawaran' => [
                'produk_id' => Produk::whereHas(
                    'toko',
                    fn ($q) => $q->where('pengguna_id', auth()->id())
                )->pluck('nama_produk', 'produk_id'),
            ],
    
            default => [],
        };
    }

    protected function data(Request $request, $item = null)
    {
        $data = $request->validate($this->rules);
        if ($this->resource === 'toko') {
            $data['pengguna_id'] = auth()->id();
        }
        if ($this->resource === 'produk') {
            $toko = Toko::findOrFail($data['toko_id']);
            abort_unless($toko->pengguna_id === auth()->id(), 403);
            if ($item && $item->penawaran()->exists() && $data['toko_id'] != $item->toko_id) {
                throw ValidationException::withMessages(['toko_id' => 'Produk yang memiliki penawaran tidak boleh dipindahkan toko.']);
            }
        }
        if ($this->resource === 'penawaran') {
            $produk = Produk::with('toko')->findOrFail($data['produk_id']);
            abort_unless($produk->toko->pengguna_id === auth()->id(), 403);
            if ($data['harga_diskon'] > $produk->harga_normal) {
                throw ValidationException::withMessages(['harga_diskon' => 'Harga diskon tidak boleh melebihi harga normal.']);
            }
        }
        if ($this->resource === 'produk') {
            unset($data['foto']);
        }

        return $data;
    }

    public function index()
    {
        $q = ($this->model)::query();
        if ($this->resource === 'penawaran') {
            $q->with('produk.toko');
            if (auth()->user()->peran === 'buyer') {
                $q->where('stok', '>', 0)->where('batas_pengambilan', '>', now());
            } else {
                $q->whereHas('produk.toko', fn ($q) => $q->where('pengguna_id', auth()->id()));
            }
        } else {
            $this->seller();
            if ($this->resource === 'toko') {
                $q->where('pengguna_id', auth()->id());
            } else {
                $q->whereHas('toko', fn ($q) => $q->where('pengguna_id', auth()->id()));
            }
        }

        return view('crud.index', ['items' => $q->latest()->paginate(10), 'resource' => $this->resource, 'fields' => $this->fields]);
    }

    public function create()
    {
        $this->seller();

        return view('crud.form', ['item' => null, 'resource' => $this->resource, 'fields' => $this->fields, 'options' => $this->options()]);
    }

    public function store(Request $request)
    {
        $this->seller();
        $data = $this->data($request);
        $path = null;
        if ($this->resource === 'produk' && $request->hasFile('foto')) {
            $data['foto'] = $path = $request->file('foto')->store('produk', 'public');
        }
        try {
            ($this->model)::create($data);
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }throw $e;
        }

        return redirect()->route($this->resource.'.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $item = ($this->model)::findOrFail($id);
        if ($this->resource === 'penawaran' && auth()->user()->peran === 'buyer') {
            abort_unless($item->stok > 0 && $item->batas_pengambilan->isFuture(), 404);
        } else {
            $this->seller();
            $this->owner($item);
        }

        return view('crud.show', ['item' => $item, 'resource' => $this->resource, 'fields' => $this->fields]);
    }

    public function edit(string $id)
    {
        $this->seller();
        $item = ($this->model)::findOrFail($id);
        $this->owner($item);

        return view('crud.form', ['item' => $item, 'resource' => $this->resource, 'fields' => $this->fields, 'options' => $this->options()]);
    }

    public function update(Request $request, string $id)
    {
        $this->seller();
        $path = null;
        $oldPhoto = null;
        try {
            DB::transaction(function () use ($request, $id, &$path, &$oldPhoto) {
                $item = ($this->model)::lockForUpdate()->findOrFail($id);
                $this->owner($item);
                if ($this->resource === 'penawaran' && $item->pesanan()->exists()) {
                    throw ValidationException::withMessages(['produk_id' => 'Penawaran yang sudah dipesan tidak dapat diedit. Buat penawaran baru.']);
                }
                $data = $this->data($request, $item);
                if ($this->resource === 'produk' && $request->hasFile('foto')) {
                    $oldPhoto = $item->foto;
                    $data['foto'] = $path = $request->file('foto')->store('produk', 'public');
                }
                $item->update($data);
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }throw $e;
        }
        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()->route($this->resource.'.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->seller();
        $photo = null;
        try {
            DB::transaction(function () use ($id, &$photo) {
                $item = ($this->model)::lockForUpdate()->findOrFail($id);
                $this->owner($item);
                $has = match ($this->resource) {
                    'toko' => $item->produk()->exists(),'produk' => $item->penawaran()->exists(),'penawaran' => $item->pesanan()->exists()
                };
                if ($has) {
                    throw ValidationException::withMessages(['hapus' => 'Data masih digunakan. Hapus data terkait yang diizinkan terlebih dahulu.']);
                }
                if ($this->resource === 'produk') {
                    $photo = $item->foto;
                }
                $item->delete();
            });
        } catch (QueryException $e) {
            if (! in_array((string) $e->getCode(), ['23000', '23503'])) {
                throw $e;
            }

return back()->withErrors(['hapus' => 'Data masih digunakan dan tidak dapat dihapus.']);
        }
        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        return redirect()->route($this->resource.'.index')->with('success','Data berhasil dihapus.');
    }
}
