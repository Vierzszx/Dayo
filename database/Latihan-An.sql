-- DROP DATABASE IF EXISTS lebih_an;
CREATE DATABASE lebih_an;
USE lebih_an;

CREATE TABLE pengguna(
	pengguna_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    peran ENUM('buyer', 'seller', 'admin') DEFAULT 'buyer',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

CREATE TABLE toko(
	toko_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengguna_id INT UNSIGNED NOT NULL,
    nama_toko VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    titik_pengambilan VARCHAR(255) NOT NULL,
    nomor_whatsapp VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_toko_pengguna FOREIGN KEY (pengguna_id) REFERENCES pengguna(pengguna_id) ON DELETE CASCADE
);

CREATE TABLE kategori(
	kategori_id INT UNSIGNED auto_increment PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE produk(
	produk_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    toko_id INT UNSIGNED NOT NULL,
    kategori_id INT UNSIGNED NULL,
    nama_produk VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    foto VARCHAR(255) NULL,
    harga_normal DECIMAL (10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_produk_harga CHECK (harga_normal > 0),
    CONSTRAINT fk_produk_toko FOREIGN KEY (toko_id) REFERENCES toko(toko_id) ON DELETE CASCADE,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(kategori_id) ON DELETE SET NULL
);

CREATE TABLE penawaran(
	penawaran_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produk_id INT UNSIGNED NOT NULL,
    harga_diskon DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL,
    waktu_mulai DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    batas_pengambilan DATETIME NOT NULL,
    status ENUM('aktif', 'habis', 'kedaluwarsa') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_penawaran_harga CHECK (harga_diskon > 0),
    CONSTRAINT chk_penawaran_stok CHECK (stok >= 0),
    CONSTRAINT chk_penawaran_waktu CHECK (batas_pengambilan > waktu_mulai),
    CONSTRAINT fk_penawaran_produk FOREIGN KEY (produk_id) REFERENCES produk(produk_id) ON DELETE CASCADE,
    INDEX idx_penawaran_aktif (status, batas_pengambilan)
);

CREATE TABLE pesanan(
	pesanan_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengguna_id INT UNSIGNED NOT NULL,
    penawaran_id INT UNSIGNED NOT NULL,
    jumlah INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'siap_diambil', 'selesai', 'dibatalkan', 'kadaluwarsa') NOT NULL DEFAULT 'pending',
    kode_pengambilan VARCHAR(10) NOT NULL UNIQUE,
	waktu_diambil DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_pesanan_jumlah CHECK (jumlah > 0),
	CONSTRAINT chk_pesanan_total CHECK (total_harga >= 0),
    CONSTRAINT fk_pesanan_pengguna FOREIGN KEY (pengguna_id) REFERENCES pengguna(pengguna_id) ON DELETE RESTRICT,
    CONSTRAINT fk_pesanan_penawaran FOREIGN KEY (penawaran_id) REFERENCES penawaran(penawaran_id) ON DELETE RESTRICT,
    INDEX idx_pesanan_pengguna (pengguna_id, status),
    INDEX idx_pesanan_penawaran (penawaran_id)
);

INSERT INTO kategori (nama_kategori) VALUES ('Makanan Berat'), ('Snack'), ('Kue dan Roti'), ('Minuman'), ('Lainnya');

SHOW TABLES;