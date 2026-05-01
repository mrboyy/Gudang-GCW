<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function operatorUser(): User
    {
        return User::factory()->create(['role' => 'operator', 'is_active' => true]);
    }

    public function test_admin_bisa_lihat_daftar_barang(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('barang.index'))
            ->assertOk();
    }

    public function test_operator_tidak_bisa_akses_create_barang(): void
    {
        $this->actingAs($this->operatorUser())
            ->get(route('barang.create'))
            ->assertForbidden();
    }

    public function test_admin_bisa_buat_barang(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('barang.store'), [
                'kode_barang'   => 'BRG-TEST-001',
                'nama_barang'   => 'Barang Test',
                'merk'          => 'TestMerk',
                'satuan'        => 'pcs',
                'stok_minimum'  => 10,
                'is_active'     => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('barangs', ['kode_barang' => 'BRG-TEST-001']);
    }

    public function test_guest_redirect_ke_login(): void
    {
        $this->get(route('barang.index'))
            ->assertRedirect(route('login'));
    }
}
