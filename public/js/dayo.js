/**
 * public/js/dayo.js - Interaksi sisi klien aplikasi Dayo.
 * Fitur: (1) pencarian kartu di Beranda, (2) hitung total otomatis,
 *        (3) validasi jumlah terhadap stok, (4) konfirmasi sebelum aksi berbahaya.
 * Semua kode dibungkus DOMContentLoaded agar elemen sudah ada saat diakses.
 */
document.addEventListener('DOMContentLoaded', () => {

    /** Format angka ke Rupiah, contoh 9000 -> "Rp 9.000" */
    const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);

    /* ---------- 1) PENCARIAN DI BERANDA ---------- */
    const kolomCari = document.getElementById('kolom-cari');
    if (kolomCari) {
        const kartu = document.querySelectorAll('#grid-penawaran .kartu');
        const pesanKosong = document.getElementById('kosong');

        const saring = () => {
            const kata = kolomCari.value.trim().toLowerCase();
            let tampil = 0;
            kartu.forEach((k) => {
                // atribut data-cari berisi "nama + toko" huruf kecil (lihat komponen kartu)
                const cocok = k.dataset.cari.includes(kata);
                k.hidden = !cocok;
                if (cocok) tampil++;
            });
            pesanKosong.hidden = tampil > 0;
        };

        kolomCari.addEventListener('input', saring);
        document.getElementById('tombol-cari').addEventListener('click', saring);
    }

    /* ---------- 2 & 3) TOTAL OTOMATIS + VALIDASI JUMLAH ---------- */
    const inputJumlah = document.querySelector('[data-input-jumlah]');
    const labelTotal  = document.querySelector('[data-total]');
    const pilih       = document.querySelector('[data-pilih-penawaran]');   // ada di halaman Buat Pesanan
    const detail      = document.querySelector('.detail');                  // ada di halaman Detail Penawaran

    if (inputJumlah && labelTotal) {
        /** Ambil harga & stok dari sumber yang tersedia (dropdown atau section detail). */
        const ambilData = () => {
            if (pilih) {
                const opt = pilih.selectedOptions[0];
                return { harga: +opt.dataset.harga, stok: +opt.dataset.stok };
            }
            return { harga: +detail.dataset.harga, stok: +detail.dataset.stok };
        };

        const hitung = () => {
            const { harga, stok } = ambilData();
            inputJumlah.max = stok;                        // batas atas = stok tersisa
            let jumlah = parseInt(inputJumlah.value, 10);
            if (isNaN(jumlah) || jumlah < 1) jumlah = 1;   // minimal 1
            if (jumlah > stok) jumlah = stok;              // tidak boleh melebihi stok
            inputJumlah.value = jumlah;
            labelTotal.textContent = rupiah(jumlah * harga);
        };

        inputJumlah.addEventListener('input', hitung);
        if (pilih) pilih.addEventListener('change', hitung);
        hitung(); // hitung awal saat halaman dimuat
    }

    /* ---------- 4) KONFIRMASI AKSI BERBAHAYA ---------- */
    // Form dengan atribut data-konfirmasi="..." akan meminta persetujuan dulu.
    document.querySelectorAll('form[data-konfirmasi]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!confirm(form.dataset.konfirmasi)) e.preventDefault();
        });
    });
});

/* ---------- 5) PRATINJAU FOTO SAAT UNGGAH ---------- */
// Input file dengan data-preview="#idImg" akan menampilkan gambar yang dipilih pada <img id="idImg">.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const img = document.querySelector(input.dataset.preview);
            const file = input.files[0];
            if (img && file) { img.src = URL.createObjectURL(file); img.hidden = false; }
        });
    });
});
