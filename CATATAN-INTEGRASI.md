# Catatan integrasi frontend (Dayo) -> backend

Prinsip: backend (routes, controller, model, SQL) tidak diubah. Frontend yang menyesuaikan.

## Pemetaan utama
| Frontend lama | Backend (dipakai sekarang) |
|---|---|
| `auth()->user()->name / role` (`penjual`/`pembeli`) | `nama` / `peran` (`seller`/`buyer`) |
| `session('sukses')` | `session('success')` |
| `$t->id`, `$p->id` | `toko_id`, `produk_id`, `penawaran_id`, `pesanan_id` |
| toko: `nama`, `alamat`, `no_wa` | `nama_toko`, `titik_pengambilan`, `nomor_whatsapp` |
| produk: `nama`, `harga` | `nama_produk`, `harga_normal` |
| penawaran: `nama`, `toko_nama`, `harga_normal`, `foto`, `no_wa` | lewat relasi `produk->nama_produk`, `produk->toko->nama_toko`, `produk->harga_normal`, `produk->foto`, `produk->toko->nomor_whatsapp` |
| `?penawaran=ID` ke halaman pesan | `?penawaran_id=ID` |
| status pesanan `menunggu/dikonfirmasi/...` | `pending/siap_diambil/selesai/dibatalkan/kadaluwarsa` |
| variabel `$daftar`, `$toko`, `$produk`, `$p`, `$o` | `$items` (paginator), `$item`, `$options`, `$offers`, `$selected` |

## Struktur view
- `crud/index|form|show` dirender backend untuk toko/produk/penawaran; file ini hanya meneruskan ke
  `toko/_index|_form|_show`, `produk/...`, `penawaran/...` (tampilan Dayo).
- `pesanan/index|show|form` dirender `PesananController` (form dipakai untuk buat DAN kelola).
- Satu layout: `layouts/dayo.blade.php` (layout `app` dihapus).
- Paginasi: `resources/views/vendor/pagination/dayo.blade.php`.

## Dihapus karena tidak ada di backend
`routes/demo.php`, `beranda`, `penjual/dashboard`, `pesanan/create` (backend memakai `pesanan/form`).
Fitur pencarian beranda dipindah ke halaman Penawaran (pembeli).

## Hal yang perlu diperhatikan di sisi backend/setup (tidak diubah)
- Jalankan `php artisan storage:link` agar foto produk tampil.
- `SESSION_DRIVER=database` butuh tabel `sessions` (ada di migration bawaan, tidak di `Latihan-An.sql`);
  jalankan `php artisan migrate` atau set `SESSION_DRIVER=file` di `.env`.
- Migration `users` bawaan dan `DatabaseSeeder` (kolom `name`) tidak cocok dengan tabel `pengguna`; abaikan/hapus.
- Opsional: `APP_LOCALE=id` agar nama bulan berbahasa Indonesia.
- Pencarian di halaman Penawaran menyaring 10 kartu pada halaman yang sedang tampil (client-side).
