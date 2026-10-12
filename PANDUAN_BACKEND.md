# Backend RescueBite untuk proyek Latihan-An

Paket perubahan untuk proyek Laravel 12 milik kelompok. Salin file ke proyek asli yang sudah memiliki artisan, public, storage dan vendor. ZIP ini bukan instalasi Laravel lengkap. Cadangkan proyek dan kerjakan pada branch backend. Tidak ada respons JSON untuk halaman CRUD: seluruh controller menggunakan Blade dan redirect.

## Pemasangan
1. Salin app, routes, resources dan tests dari paket ini ke proyek asli. File dengan nama sama diganti setelah dibandingkan. config/bootstrap/composer bawaan dipertahankan dari unggahan.
2. database/migrations/2026_10_12_000001_create_rescuebite_tables.php adalah migration acuan, disertakan agar paket dapat diuji. Koordinasikan dengan teman database. Gunakan SATU versi migration untuk tabel yang sama; jangan menjalankan acuan sekaligus migration teman yang membuat tabel/kolom identik. Tidak menggunakan migrate:fresh pada database berisi data.
3. Buat database MySQL lokal dengan CREATE DATABASE IF NOT EXISTS rescuebite; atur DB_CONNECTION=mysql, DB_DATABASE=rescuebite, DB_HOST, DB_PORT, DB_USERNAME dan DB_PASSWORD pada .env lokal. Password tidak dikirim ke GitHub.
4. Jalankan php artisan config:clear, php artisan migrate, php artisan storage:link, lalu php artisan serve dari folder yang berisi artisan.
5. Buka /register. Buat akun penjual dan akun pembeli dengan email berbeda. Login/logout disertakan untuk integrasi pengujian; cocokkan AuthController dan route auth dengan pekerjaan teman autentikasi sebelum merge.
6. Penjual membuat toko, produk, penawaran. Pembeli memesan penawaran. Penjual menandai selesai saat makanan dibayar dan diambil.
7. php artisan test --filter=RescueBiteTest untuk pengujian otomatis; konfigurasi memakai SQLite memory, bukan database MySQL pengguna.

## Kontrak data
- users: id, name, email, password, peran (penjual/pembeli), timestamps; default pembeli.
- toko: id, user_id, nama_toko, alamat, kontak, jam_operasional nullable, timestamps.
- produk: id, toko_id, nama_produk, deskripsi nullable, harga_normal (rupiah integer), foto nullable (path disk public), timestamps.
- penawaran: id, produk_id, harga_diskon (rupiah integer), stok (sisa yang tersedia), mulai_pengambilan, batas_pengambilan, timestamps.
- pesanan: id, user_id, penawaran_id, jumlah, total_harga (rupiah integer), status (menunggu/selesai/dibatalkan), timestamps.
Foreign key menggunakan restrict delete. Harga tidak menggunakan angka desimal karena rupiah bulat. Zona waktu aplikasi disepakati Asia/Jakarta. Sesuaikan config/app.php timezone atau tambahkan APP_TIMEZONE bila konfigurasi membacanya.

## Aturan dan hak akses
- Satu pesanan untuk satu penawaran; jumlah boleh lebih dari satu. Bayar di toko, tanpa payment gateway.
- Penjual hanya mengelola toko/produk/penawaran sendiri; pembeli hanya mengelola pesanan sendiri. Penjual melihat pesanan untuk toko miliknya.
- Harga total dihitung server. Stok dikurangi dalam transaksi dengan lockForUpdate (MySQL/InnoDB) agar pesanan bersamaan tidak menjual stok yang sama.
- Pembeli dapat mengubah jumlah pesanan menunggu atau membatalkannya. Penjual dapat menyelesaikan atau membatalkan pesanan toko sendiri. Pembatalan mengembalikan stok sekali. Pesanan selesai tidak bisa diubah/dihapus.
- Hanya pesanan dibatalkan boleh dihapus; penawaran yang memiliki pesanan tidak bisa diedit/dihapus. Produk yang memiliki penawaran tidak boleh dipindahkan toko. Data induk terkait tidak dapat dihapus.
- Harga diskon tidak boleh lebih besar daripada harga normal ketika penawaran dibuat. Penawaran kedaluwarsa tidak dapat dipesan; tidak ada pembatalan otomatis atau pengembalian stok otomatis saat batas waktu habis. Kelola pembatalan lewat aplikasi.
- Foto produk mendukung upload JPG/PNG/WebP maksimal 2 MB, disimpan di disk public. Jalankan storage:link. Foto sebelumnya dihapus ketika diganti; foto dihapus ketika produk dihapus.
- Pendaftaran penjual bebas untuk demo tugas; verifikasi toko dan moderasi tidak termasuk ruang lingkup.

## Integrasi frontend
Halaman sederhana disertakan sebagai sarana uji yang dapat diganti tampilannya oleh teman frontend. Halaman ini memakai CSS inline, sehingga tidak memerlukan npm/Vite; proyek frontend tetap boleh menggunakan Vite.
- crud.index menerima items (paginator), resource dan fields.
- crud.form menerima item (null saat tambah), resource, fields dan options.
- crud.show menerima item, resource, fields.
- pesanan.index menerima items; pesanan.form menerima item, offers, selected; pesanan.show menerima item.
- Form wajib @csrf. Edit menggunakan @method('PUT'); hapus @method('DELETE'). Input sesuai kontrak tabel; input foto type=file dan enctype=multipart/form-data.
- Error input tersedia pada $errors, nilai lama melalui old(), pesan sukses session('success').
- TokoController, ProdukController dan PenawaranController berbagi CrudController agar pemeriksaan pemilik konsisten. PesananController terpisah karena logika stok/transaksi.

## Git
Periksa git diff dan git status. Commit file backend yang relevan di branch backend. Halaman pengujian, auth, dan migration acuan perlu dikoordinasikan sebelum merge dengan pekerjaan teman. Jangan commit .env, database.sqlite, vendor, node_modules atau bootstrap/cache/*.php.

## Hasil pemeriksaan
13 pengujian otomatis berhasil, 89 assertion, menggunakan Laravel 12 dan PHP 8.3 dengan SQLite memory. Termasuk rendering halaman Blade, autentikasi, CRUD produk, upload/ganti/hapus foto, hak akses, validasi diskon, stok pesanan, pembatalan sekali, serta perlindungan riwayat. Sintaks PHP diperiksa dan kode diformat. Koneksi MySQL di laptop pengguna dan perilaku konkurensi MySQL belum diuji langsung; perlu pengujian integrasi setelah konfigurasi kelompok tersedia.
