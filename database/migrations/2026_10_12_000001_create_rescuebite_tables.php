<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('peran', 20)->default('pembeli');
        });
        Schema::create('toko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('nama_toko', 100);
            $table->text('alamat');
            $table->string('kontak', 30);
            $table->string('jam_operasional', 100)->nullable();
            $table->timestamps();
        });
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('toko')->restrictOnDelete();
            $table->string('nama_produk', 100);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('harga_normal');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
        Schema::create('penawaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->restrictOnDelete();
            $table->unsignedBigInteger('harga_diskon');
            $table->unsignedInteger('stok');
            $table->dateTime('mulai_pengambilan');
            $table->dateTime('batas_pengambilan')->index();
            $table->timestamps();
        });
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('penawaran_id')->constrained('penawaran')->restrictOnDelete();
            $table->unsignedInteger('jumlah');
            $table->unsignedBigInteger('total_harga');
            $table->string('status', 20)->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('penawaran');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('toko');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('peran'));
    }
};
