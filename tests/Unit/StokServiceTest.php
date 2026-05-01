<?php

namespace Tests\Unit;

use App\Models\Barang;
use App\Models\Stok;
use App\Services\StokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StokServiceTest extends TestCase
{
    use RefreshDatabase;

    private StokService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StokService();
    }

    public function test_tambah_stok_membuat_record_baru(): void
    {
        $barang = Barang::factory()->create();

        $this->service->tambahStok($barang, 100, 'LOT-001');

        $this->assertDatabaseHas('stoks', [
            'id_barang'  => $barang->id,
            'nomor_lot'  => 'LOT-001',
            'stok_akhir' => 100,
        ]);
    }

    public function test_kurangi_stok_fifo(): void
    {
        $barang = Barang::factory()->create();
        Stok::factory()->create(['id_barang' => $barang->id, 'nomor_lot' => 'LOT-001', 'stok_akhir' => 50, 'tanggal_update' => now()->subDays(2)]);
        Stok::factory()->create(['id_barang' => $barang->id, 'nomor_lot' => 'LOT-002', 'stok_akhir' => 50, 'tanggal_update' => now()->subDay()]);

        $this->service->kurangiStok($barang, 70, null);

        $this->assertDatabaseHas('stoks', ['nomor_lot' => 'LOT-001', 'stok_akhir' => 0]);
        $this->assertDatabaseHas('stoks', ['nomor_lot' => 'LOT-002', 'stok_akhir' => 30]);
    }

    public function test_kurangi_stok_throw_jika_tidak_cukup(): void
    {
        $barang = Barang::factory()->create();
        Stok::factory()->create(['id_barang' => $barang->id, 'nomor_lot' => 'LOT-001', 'stok_akhir' => 10]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/tidak cukup/i');

        $this->service->kurangiStok($barang, 100, null);
    }

    public function test_tambah_stok_update_existing_lot(): void
    {
        $barang = Barang::factory()->create();
        Stok::factory()->create(['id_barang' => $barang->id, 'nomor_lot' => 'LOT-001', 'stok_akhir' => 50]);

        $this->service->tambahStok($barang, 25, 'LOT-001');

        $this->assertDatabaseHas('stoks', ['nomor_lot' => 'LOT-001', 'stok_akhir' => 75]);
    }
}
