<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    private function access(Pesanan $p)
    {
        abort_unless($p->user_id === auth()->id() || (auth()->user()->peran === 'penjual' && $p->penawaran->produk->toko->user_id === auth()->id()), 403);
    }

    public function index()
    {
        $q = Pesanan::with('penawaran.produk.toko', 'pembeli');
        if (auth()->user()->peran === 'penjual') {
            $q->whereHas('penawaran.produk.toko', fn ($q) => $q->where('user_id', auth()->id()));
        } else {
            $q->where('user_id', auth()->id());
        }

        return view('pesanan.index', ['items' => $q->latest()->paginate(10)]);
    }

    public function create(Request $request)
    {
        abort_unless(auth()->user()->peran === 'pembeli', 403);
        $offers = Penawaran::with('produk.toko')->where('stok', '>', 0)->where('batas_pengambilan', '>', now())->get();

        return view('pesanan.form', ['item' => null, 'offers' => $offers, 'selected' => $request->query('penawaran_id')]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->peran === 'pembeli', 403);
        $data = $request->validate(['penawaran_id' => 'required|integer|exists:penawaran,id', 'jumlah' => 'required|integer|min:1|max:100000']);
        DB::transaction(function () use ($data) {
            $offer = Penawaran::lockForUpdate()->findOrFail($data['penawaran_id']);
            if ($offer->batas_pengambilan->isPast() || $data['jumlah'] > $offer->stok) {
                throw ValidationException::withMessages(['jumlah' => 'Stok tidak cukup atau penawaran telah berakhir.']);
            }
            Pesanan::create(['user_id' => auth()->id(), 'penawaran_id' => $offer->id, 'jumlah' => $data['jumlah'], 'total_harga' => $data['jumlah'] * $offer->harga_diskon, 'status' => 'menunggu']);
            $offer->decrement('stok', $data['jumlah']);
        });

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dibuat. Bayar saat pengambilan.');
    }

    public function show(string $id)
    {
        $item = Pesanan::with('penawaran.produk.toko', 'pembeli')->findOrFail($id);
        $this->access($item);

        return view('pesanan.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item = Pesanan::with('penawaran.produk')->findOrFail($id);
        $this->access($item);
        abort_unless($item->status === 'menunggu', 403);

        return view('pesanan.form', ['item' => $item, 'offers' => collect(), 'selected' => null]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate(['status' => 'required|in:menunggu,selesai,dibatalkan', 'jumlah' => 'nullable|integer|min:1|max:100000']);
        DB::transaction(function () use ($data, $id) {
            // Selalu kunci penawaran sebelum pesanan agar urutan penguncian konsisten.
            $reference = Pesanan::findOrFail($id);
            $this->access($reference);
            $offer = Penawaran::lockForUpdate()->findOrFail($reference->penawaran_id);
            $item = Pesanan::lockForUpdate()->findOrFail($id);
            $this->access($item);
            if ($item->status !== 'menunggu') {
                throw ValidationException::withMessages(['status' => 'Pesanan sudah selesai atau dibatalkan.']);
            }
            if ($data['status'] === 'selesai') {
                abort_unless(auth()->user()->peran === 'penjual', 403);
                if (now()->lt($offer->mulai_pengambilan)) {
                    throw ValidationException::withMessages(['status' => 'Waktu pengambilan belum dimulai.']);
                }
                $item->update(['status' => 'selesai']);
            } elseif ($data['status'] === 'dibatalkan') {
                $offer->increment('stok', $item->jumlah);
                $item->update(['status' => 'dibatalkan']);
            } else {
                abort_unless($item->user_id === auth()->id(), 403);
                $jumlah = $data['jumlah'] ?? $item->jumlah;
                $delta = $jumlah - $item->jumlah;
                if ($offer->batas_pengambilan->isPast() || $delta > $offer->stok) {
                    throw ValidationException::withMessages(['jumlah' => 'Stok tidak cukup atau penawaran telah berakhir.']);
                }
                if ($delta > 0) {
                    $offer->decrement('stok', $delta);
                } elseif ($delta < 0) {
                    $offer->increment('stok', -$delta);
                }
                $item->update(['jumlah' => $jumlah, 'total_harga' => $jumlah * $offer->harga_diskon]);
            }
        });

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $item = Pesanan::lockForUpdate()->findOrFail($id);
            $this->access($item);
            if ($item->status !== 'dibatalkan') {
                throw ValidationException::withMessages(['hapus' => 'Hanya pesanan dibatalkan yang boleh dihapus. Pesanan selesai tetap menjadi riwayat.']);
            }
            $item->delete();
        });

        return redirect()->route('pesanan.index')->with('success','Pesanan dibatalkan berhasil dihapus.');
    }
}
