<?php
/**
 * routes/demo.php - RUTE DEMO SEMENTARA untuk melihat tampilan frontend tanpa backend.
 * JANGAN di-commit. Hapus file ini dan baris require-nya setelah backend teman tersedia.
 *
 * Cara pakai: tambahkan di PALING BAWAH routes/web.php
 *     require __DIR__.'/demo.php';
 * Lalu buka http://127.0.0.1:8000/demo  (pembeli)  atau  /demo?peran=penjual  (penjual).
 */

use App\Models\User;
use Illuminate\Support\Facades\Route;

// ---------- DATA DUMMY (objek agar cocok dengan view yang memakai $p->nama) ----------
$obj = fn (array $baris) => collect($baris)->map(fn ($a) => (object) $a);

$penawaran = $obj([
    ['id'=>1,'produk_id'=>1,'nama'=>'Risol Mayo','toko_nama'=>'Danusan HIMA TI','lokasi'=>'Danusan HIMA TI, Gedung A lantai 1',
     'deskripsi'=>'Risol isi sayur dan mayo, dibuat pagi ini.','harga_normal'=>5000,'harga_diskon'=>3000,'stok'=>12,
     'waktu_mulai'=>now()->subHour(),'batas_pengambilan'=>now()->addHours(3),'no_wa'=>'6281234567890',
     'foto'=>'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800'],
    ['id'=>2,'produk_id'=>2,'nama'=>'Es Teh Jumbo','toko_nama'=>'Danusan BEM','lokasi'=>'Danusan BEM, Gedung B lantai 1',
     'deskripsi'=>'Es teh manis ukuran jumbo.','harga_normal'=>4000,'harga_diskon'=>2500,'stok'=>8,
     'waktu_mulai'=>now()->subHour(),'batas_pengambilan'=>now()->addHours(2),'no_wa'=>'6281234567891',
     'foto'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=800'],
    ['id'=>3,'produk_id'=>3,'nama'=>'Brownies','toko_nama'=>'UKM Kuliner','lokasi'=>'UKM Kuliner, Kantin Pusat',
     'deskripsi'=>'Brownies cokelat panggang.','harga_normal'=>10000,'harga_diskon'=>6000,'stok'=>5,
     'waktu_mulai'=>now()->subHour(),'batas_pengambilan'=>now()->addHours(4),'no_wa'=>'6281234567892',
     'foto'=>'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800'],
]);

$toko = $obj([
    ['id'=>1,'nama'=>'Danusan HIMA TI','alamat'=>'Gedung A lantai 1','no_wa'=>'6281234567890','deskripsi'=>'Danusan himpunan mahasiswa TI.'],
    ['id'=>2,'nama'=>'Danusan BEM','alamat'=>'Gedung B lantai 1','no_wa'=>'6281234567891','deskripsi'=>'Danusan BEM fakultas.'],
]);

$produk = $obj([
    ['id'=>1,'toko_id'=>1,'toko_nama'=>'Danusan HIMA TI','nama'=>'Risol Mayo','harga'=>5000,'deskripsi'=>'Risol isi sayur dan mayo.','foto'=>$penawaran[0]->foto],
    ['id'=>2,'toko_id'=>2,'toko_nama'=>'Danusan BEM','nama'=>'Es Teh Jumbo','harga'=>4000,'deskripsi'=>'Es teh manis jumbo.','foto'=>$penawaran[1]->foto],
    ['id'=>3,'toko_id'=>1,'toko_nama'=>'UKM Kuliner','nama'=>'Brownies','harga'=>10000,'deskripsi'=>'Brownies cokelat.','foto'=>$penawaran[2]->foto],
]);

$pesanan = $obj([
    ['id'=>12,'status'=>'menunggu','makanan'=>'Risol Mayo','toko'=>'Danusan HIMA TI','pembeli'=>'Budi','jumlah'=>3,'harga'=>3000,'batas_pengambilan'=>now()->addHours(3)],
    ['id'=>13,'status'=>'dikonfirmasi','makanan'=>'Es Teh Jumbo','toko'=>'Danusan BEM','pembeli'=>'Sari','jumlah'=>2,'harga'=>2500,'batas_pengambilan'=>now()->addHours(2)],
    ['id'=>14,'status'=>'siap-diambil','makanan'=>'Brownies','toko'=>'UKM Kuliner','pembeli'=>'Dimas','jumlah'=>1,'harga'=>6000,'batas_pengambilan'=>now()->addHours(4)],
    ['id'=>15,'status'=>'selesai','makanan'=>'Risol Mayo','toko'=>'Danusan HIMA TI','pembeli'=>'Rina','jumlah'=>2,'harga'=>3000,'batas_pengambilan'=>now()->subHour()],
]);

// ---------- MIDDLEWARE DEMO: pengguna palsu (tanpa database) ----------
// ?peran=penjual atau ?peran=pembeli; pilihan diingat lewat session.
$penggunaDemo = function ($request, $next) {
    $peran = $request->query('peran', session('demo_peran', 'pembeli'));
    session(['demo_peran' => $peran]);
    auth()->setUser((new User)->forceFill([
        'id' => 1, 'name' => $peran === 'penjual' ? 'Sari' : 'Budi', 'role' => $peran,
    ]));
    return $next($request);
};

Route::group(['middleware' => [$penggunaDemo]], function () use ($penawaran, $toko, $produk, $pesanan) {

    $sukses = fn ($tujuan) => fn () => redirect($tujuan)->with('sukses', 'Mode demo: data tidak benar-benar disimpan.');

    // Beranda & autentikasi
    Route::get('/demo', fn () => view('beranda', ['daftar' => $penawaran]))->name('beranda');
    Route::get('/login',    fn () => view('auth.login'))->name('login');
    Route::post('/login',   $sukses('/demo'));
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', $sukses('/login'));
    Route::post('/logout',  fn () => redirect('/demo'))->name('logout');

    // Penawaran
    Route::get('/penawaran', fn () => view('penawaran.index', ['daftar' => $penawaran]))->name('penawaran.index');
    Route::get('/penawaran/create', fn () => view('penawaran.create', ['daftarProduk' => $produk]))->name('penawaran.create');
    Route::get('/penawaran/{id}', fn ($id) => view('penawaran.show', ['p' => $penawaran->firstWhere('id', $id) ?? abort(404)]))->name('penawaran.show');
    Route::get('/penawaran/{id}/edit', fn ($id) => view('penawaran.edit', ['penawaran' => $penawaran->firstWhere('id', $id) ?? abort(404), 'daftarProduk' => $produk]))->name('penawaran.edit');

    // Toko
    Route::get('/toko', fn () => view('toko.index', ['daftar' => $toko]))->name('toko.index');
    Route::get('/toko/create', fn () => view('toko.create'))->name('toko.create');
    Route::get('/toko/{id}', fn ($id) => view('toko.show', ['toko' => $toko->firstWhere('id', $id) ?? abort(404)]))->name('toko.show');
    Route::get('/toko/{id}/edit', fn ($id) => view('toko.edit', ['toko' => $toko->firstWhere('id', $id) ?? abort(404)]))->name('toko.edit');

    // Produk
    Route::get('/produk', fn () => view('produk.index', ['daftar' => $produk]))->name('produk.index');
    Route::get('/produk/create', fn () => view('produk.create', ['daftarToko' => $toko]))->name('produk.create');
    Route::get('/produk/{id}', fn ($id) => view('produk.show', ['produk' => $produk->firstWhere('id', $id) ?? abort(404)]))->name('produk.show');
    Route::get('/produk/{id}/edit', fn ($id) => view('produk.edit', ['produk' => $produk->firstWhere('id', $id) ?? abort(404), 'daftarToko' => $toko]))->name('produk.edit');

    // Pesanan
    Route::get('/pesanan', fn () => view('pesanan.index', ['pesanan' => $pesanan]))->name('pesanan.index');
    Route::get('/pesanan/create', fn () => view('pesanan.create', ['daftar' => $penawaran]))->name('pesanan.create');
    Route::get('/pesanan/{id}', fn ($id) => view('pesanan.show', ['o' => $pesanan->firstWhere('id', $id) ?? abort(404)]))->name('pesanan.show');

    // Dashboard penjual (opsional)
    Route::get('/penjual/dashboard', fn () => view('penjual.dashboard', [
        'ringkasan' => ['masuk' => 7, 'stok' => 25, 'pemasukan' => 85000],
        'pesanan'   => $pesanan->map(fn ($o) => (object) ['id'=>$o->id,'pembeli'=>$o->pembeli,'item'=>$o->makanan.' x'.$o->jumlah,'status'=>$o->status]),
    ]))->name('penjual.dashboard');

    // Simpan / ubah / hapus (hanya pura-pura)
    foreach (['penawaran', 'toko', 'produk', 'pesanan'] as $n) {
        Route::post("/$n", $sukses("/$n"))->name("$n.store");
        Route::match(['put', 'patch'], "/$n/{id}", $sukses("/$n"))->name("$n.update");
        Route::delete("/$n/{id}", $sukses("/$n"))->name("$n.destroy");
    }
});