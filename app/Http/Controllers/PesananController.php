<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    private function access(Pesanan $pesanan)
    {
        $pembeli = $pesanan->pengguna_id === auth()->id();

        $penjual = auth()->user()->peran === 'seller'
            && $pesanan->penawaran->produk->toko->pengguna_id
                === auth()->id();

        abort_unless($pembeli || $penjual, 403);
    }

    private function perbaruiStatusPenawaran(Penawaran $offer)
    {
        if ($offer->batas_pengambilan->lte(now())) {
            $status = 'kedaluwarsa';
        } elseif ($offer->stok <= 0) {
            $status = 'habis';
        } else {
            $status = 'aktif';
        }

        $offer->update(['status' => $status]);
    }

    public function index()
    {
        $q = Pesanan::with('penawaran.produk.toko', 'pembeli');

        if (auth()->user()->peran === 'seller') {
            $q->whereHas(
                'penawaran.produk.toko',
                fn ($q) => $q->where('pengguna_id', auth()->id())
            );
        } else {
            $q->where('pengguna_id', auth()->id());
        }

        return view('pesanan.index', [
            'items' => $q->latest()->paginate(10),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless(auth()->user()->peran === 'buyer', 403);

        $offers = Penawaran::with('produk.toko')
            ->where('status', 'aktif')
            ->where('stok', '>', 0)
            ->where('waktu_mulai', '<=', now())
            ->where('batas_pengambilan', '>', now())
            ->get();

        return view('pesanan.form', [
            'item' => null,
            'offers' => $offers,
            'selected' => $request->query('penawaran_id'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->peran === 'buyer', 403);

        $data = $request->validate([
            'penawaran_id' => 'required|integer|exists:penawaran,penawaran_id',
            'jumlah' => 'required|integer|min:1|max:100000',
        ]);

        DB::transaction(function () use ($data) {
            $offer = Penawaran::lockForUpdate()
                ->findOrFail($data['penawaran_id']);

            if (
                $offer->status !== 'aktif'
                || $offer->waktu_mulai->isFuture()
                || $offer->batas_pengambilan->lte(now())
                || $data['jumlah'] > $offer->stok
            ) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Penawaran belum dimulai, sudah berakhir, atau stok tidak cukup.',
                ]);
            }

            do {
                $kode = strtoupper(bin2hex(random_bytes(5)));
            } while (
                Pesanan::where('kode_pengambilan', $kode)->exists()
            );

            Pesanan::create([
                'pengguna_id' => auth()->id(),
                'penawaran_id' => $offer->penawaran_id,
                'jumlah' => $data['jumlah'],
                'total_harga' => round(
                    $data['jumlah'] * $offer->harga_diskon,
                    2
                ),
                'status' => 'pending',
                'kode_pengambilan' => $kode,
            ]);

            $offer->decrement('stok', $data['jumlah']);
            $this->perbaruiStatusPenawaran($offer);
        });

        return redirect()
            ->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dibuat. Bayar saat pengambilan.');
    }

    public function show(string $id)
    {
        $item = Pesanan::with('penawaran.produk.toko', 'pembeli')
            ->findOrFail($id);

        $this->access($item);

        return view('pesanan.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item = Pesanan::with('penawaran.produk.toko')
            ->findOrFail($id);

        $this->access($item);

        abort_unless(
            in_array($item->status, ['pending', 'siap_diambil'], true),
            403
        );

        return view('pesanan.form', [
            'item' => $item,
            'offers' => collect(),
            'selected' => null,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,siap_diambil,selesai,dibatalkan',
            'jumlah' => 'nullable|integer|min:1|max:100000',
        ]);

        DB::transaction(function () use ($data, $id) {
            // Kunci penawaran sebelum pesanan.
            $reference = Pesanan::findOrFail($id);
            $this->access($reference);

            $offer = Penawaran::lockForUpdate()
                ->findOrFail($reference->penawaran_id);

            $item = Pesanan::lockForUpdate()->findOrFail($id);
            $this->access($item);

            if (! in_array(
                $item->status,
                ['pending', 'siap_diambil'],
                true
            )) {
                throw ValidationException::withMessages([
                    'status' => 'Pesanan ini sudah tidak dapat diperbarui.',
                ]);
            }

            if ($data['status'] === 'dibatalkan') {
                $offer->increment('stok', $item->jumlah);
                $this->perbaruiStatusPenawaran($offer);

                $item->update(['status' => 'dibatalkan']);
            } elseif (in_array(
                $data['status'],
                ['siap_diambil', 'selesai'],
                true
            )) {
                abort_unless(auth()->user()->peran === 'seller', 403);

                if (
                    $offer->waktu_mulai->isFuture()
                    || $offer->batas_pengambilan->lte(now())
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Di luar waktu pengambilan penawaran.',
                    ]);
                }

                if (
                    $data['status'] === 'siap_diambil'
                    && $item->status !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Hanya pesanan pending yang dapat ditandai siap diambil.',
                    ]);
                }

                $perubahan = ['status' => $data['status']];

                if ($data['status'] === 'selesai') {
                    $perubahan['waktu_diambil'] = now();
                }

                $item->update($perubahan);
            } else {
                abort_unless(
                    auth()->user()->peran === 'buyer'
                    && $item->pengguna_id === auth()->id(),
                    403
                );

                if ($item->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Jumlah hanya dapat diubah saat pesanan pending.',
                    ]);
                }

                $jumlah = $data['jumlah'] ?? $item->jumlah;
                $delta = $jumlah - $item->jumlah;

                if (
                    $offer->waktu_mulai->isFuture()
                    || $offer->batas_pengambilan->lte(now())
                    || $delta > $offer->stok
                    || ($delta > 0 && $offer->status === 'kedaluwarsa')
                ) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok tidak cukup atau penawaran tidak tersedia.',
                    ]);
                }

                if ($delta > 0) {
                    $offer->decrement('stok', $delta);
                } elseif ($delta < 0) {
                    $offer->increment('stok', -$delta);
                }

                $this->perbaruiStatusPenawaran($offer);

                $item->update([
                    'jumlah' => $jumlah,
                    'total_harga' => round(
                        $jumlah * $offer->harga_diskon,
                        2
                    ),
                ]);
            }
        });

        return redirect()
            ->route('pesanan.index')
            ->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $item = Pesanan::lockForUpdate()->findOrFail($id);
            $this->access($item);

            if ($item->status !== 'dibatalkan') {
                throw ValidationException::withMessages([
                    'hapus' => 'Hanya pesanan dibatalkan yang boleh dihapus. Pesanan selesai tetap menjadi riwayat.',
                ]);
            }

            $item->delete();
        });

        return redirect()
            ->route('pesanan.index')
            ->with('success', 'Pesanan dibatalkan berhasil dihapus.');
    }
}