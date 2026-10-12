<?php

namespace Tests\Feature;

use App\Models\Penawaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RescueBiteTest extends TestCase
{
    use RefreshDatabase;

    private function fixtures(): array
    {
        $seller = User::create(['name' => 'Penjual', 'email' => 'seller@test.local', 'password' => 'password123', 'peran' => 'penjual']);
        $buyer = User::create(['name' => 'Pembeli', 'email' => 'buyer@test.local', 'password' => 'password123', 'peran' => 'pembeli']);
        $shop = Toko::create(['user_id' => $seller->id, 'nama_toko' => 'Roti Sari', 'alamat' => 'Malang', 'kontak' => '0812345']);
        $product = Produk::create(['toko_id' => $shop->id, 'nama_produk' => 'Roti', 'harga_normal' => 10000]);
        $offer = Penawaran::create(['produk_id' => $product->id, 'harga_diskon' => 6000, 'stok' => 5, 'mulai_pengambilan' => now()->subHour(), 'batas_pengambilan' => now()->addHours(2)]);

        return [$seller, $buyer, $shop, $product, $offer];
    }

    public function test_guest_must_login(): void
    {
        $this->get('/produk')->assertRedirect('/login');
    }

    public function test_order_uses_server_price_and_updates_stock(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 2, 'total_harga' => 1])->assertRedirect(route('pesanan.index'));
        $this->assertDatabaseHas('pesanan', ['user_id' => $buyer->id, 'jumlah' => 2, 'total_harga' => 12000, 'status' => 'menunggu']);
        $this->assertEquals(3, $offer->fresh()->stok);
    }

    public function test_overstock_and_expired_orders_are_rejected(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 6])->assertSessionHasErrors('jumlah');
        $offer->update(['batas_pengambilan' => now()->subMinute()]);
        $this->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 1])->assertSessionHasErrors('jumlah');
        $this->assertDatabaseCount('pesanan', 0);
        $this->assertEquals(5, $offer->fresh()->stok);
    }

    public function test_cancellation_restores_stock_exactly_once(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 2]);
        $order = Pesanan::first();
        $this->put('/pesanan/'.$order->id, ['status' => 'dibatalkan'])->assertRedirect(route('pesanan.index'));
        $this->assertEquals(5, $offer->fresh()->stok);
        $this->put('/pesanan/'.$order->id, ['status' => 'dibatalkan'])->assertSessionHasErrors('status');
        $this->assertEquals(5, $offer->fresh()->stok);
    }

    public function test_quantity_edit_adjusts_stock_and_total(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 2]);
        $order = Pesanan::first();
        $this->put('/pesanan/'.$order->id, ['status' => 'menunggu', 'jumlah' => 4])->assertRedirect(route('pesanan.index'));
        $this->assertEquals(1, $offer->fresh()->stok);
        $this->assertEquals(24000, $order->fresh()->total_harga);
    }

    public function test_other_seller_cannot_update_product(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $other = User::create(['name' => 'Other', 'email' => 'other@test.local', 'password' => 'password123', 'peran' => 'penjual']);
        $this->actingAs($other)->put('/produk/'.$product->id, ['toko_id' => $shop->id, 'nama_produk' => 'Hacked', 'harga_normal' => 1])->assertForbidden();
        $this->assertEquals('Roti', $product->fresh()->nama_produk);
    }

    public function test_buyer_cannot_complete_order_and_finished_history_is_kept(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 2]);
        $order = Pesanan::first();
        $this->put('/pesanan/'.$order->id, ['status' => 'selesai'])->assertForbidden();
        $this->actingAs($seller)->put('/pesanan/'.$order->id, ['status' => 'selesai'])->assertRedirect(route('pesanan.index'));
        $this->delete('/pesanan/'.$order->id)->assertSessionHasErrors('hapus');
        $this->assertDatabaseHas('pesanan', ['id' => $order->id, 'status' => 'selesai']);
    }

    public function test_owned_shop_and_product_crud_pages(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($seller);
        foreach (['/toko', '/produk', '/penawaran', '/pesanan', '/produk/create', '/produk/'.$product->id.'/edit', '/produk/'.$product->id] as $url) {
            $this->get($url)->assertOk();
        }
        $this->post('/produk', ['toko_id' => $shop->id, 'nama_produk' => 'Baru', 'harga_normal' => 5000])->assertRedirect(route('produk.index'));
        $new = Produk::where('nama_produk', 'Baru')->firstOrFail();
        $this->put('/produk/'.$new->id, ['toko_id' => $shop->id, 'nama_produk' => 'Revisi', 'harga_normal' => 7000])->assertRedirect(route('produk.index'));
        $this->delete('/produk/'.$new->id)->assertRedirect(route('produk.index'));
        $this->assertDatabaseMissing('produk', ['id' => $new->id]);
    }

    public function test_buyer_pages_render_and_other_buyer_cannot_read_order(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($buyer);
        foreach (['/penawaran', '/penawaran/'.$offer->id, '/pesanan/create', '/pesanan'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 1]);
        $order = Pesanan::first();
        $this->get('/pesanan/'.$order->id)->assertOk();
        $this->get('/pesanan/'.$order->id.'/edit')->assertOk();
        $other = User::create(['name' => 'Other Buyer', 'email' => 'otherbuyer@test.local', 'password' => 'password123', 'peran' => 'pembeli']);
        $this->actingAs($other)->get('/pesanan/'.$order->id)->assertForbidden();
    }

    public function test_authentication_register_login_logout(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->post('/register', ['name' => 'New', 'email' => 'new@test.local', 'password' => 'password123', 'password_confirmation' => 'password123', 'peran' => 'pembeli'])->assertRedirect(route('penawaran.index'));
        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->post('/login', ['email' => 'new@test.local', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'new@test.local', 'password' => 'password123'])->assertRedirect(route('penawaran.index'));
        $this->assertAuthenticated();
    }

    public function test_product_photo_upload_replacement_and_delete(): void
    {
        [$seller,$buyer,$shop] = $this->fixtures();
        Storage::fake('public');
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aWQAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('roti.png', $png);
        $this->actingAs($seller)->post('/produk', ['toko_id' => $shop->id, 'nama_produk' => 'Foto', 'harga_normal' => 5000, 'foto' => $file])->assertRedirect(route('produk.index'));
        $item = Produk::where('nama_produk', 'Foto')->firstOrFail();
        $old = $item->foto;
        Storage::disk('public')->assertExists($old);
        $this->put('/produk/'.$item->id, ['toko_id' => $shop->id, 'nama_produk' => 'Foto', 'harga_normal' => 5000, 'foto' => UploadedFile::fake()->createWithContent('baru.png', $png)])->assertRedirect(route('produk.index'));
        $new = $item->fresh()->foto;
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($new);
        $this->delete('/produk/'.$item->id)->assertRedirect(route('produk.index'));
        Storage::disk('public')->assertMissing($new);
    }

    public function test_parent_delete_and_ordered_offer_edit_are_rejected(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($seller)->delete('/produk/'.$product->id)->assertSessionHasErrors('hapus');
        $this->delete('/toko/'.$shop->id)->assertSessionHasErrors('hapus');
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 1]);
        $this->actingAs($seller)->delete('/penawaran/'.$offer->id)->assertSessionHasErrors('hapus');
        $this->put('/penawaran/'.$offer->id, ['produk_id' => $product->id, 'harga_diskon' => 5000, 'stok' => 50, 'mulai_pengambilan' => now()->subHour()->format('Y-m-d H:i:s'), 'batas_pengambilan' => now()->addHour()->format('Y-m-d H:i:s')])->assertSessionHasErrors('produk_id');
        $this->assertEquals(4, $offer->fresh()->stok);
    }

    public function test_discount_validation_and_cancelled_order_deletion(): void
    {
        [$seller,$buyer,$shop,$product,$offer] = $this->fixtures();
        $this->actingAs($seller)->post('/penawaran', ['produk_id' => $product->id, 'harga_diskon' => 11000, 'stok' => 2, 'mulai_pengambilan' => now()->format('Y-m-d H:i:s'), 'batas_pengambilan' => now()->addHour()->format('Y-m-d H:i:s')])->assertSessionHasErrors('harga_diskon');
        $this->actingAs($buyer)->post('/pesanan', ['penawaran_id' => $offer->id, 'jumlah' => 2]);
        $item = Pesanan::first();
        $this->put('/pesanan/'.$item->id, ['status' => 'dibatalkan']);
        $this->delete('/pesanan/'.$item->id)->assertRedirect(route('pesanan.index'));
        $this->assertDatabaseMissing('pesanan',['id' => $item->id]);
        $this->assertEquals(5,$offer->fresh()->stok);
    }
}
