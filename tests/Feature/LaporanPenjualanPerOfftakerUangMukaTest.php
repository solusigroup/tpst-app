<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Klien;
use App\Models\Penjualan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LaporanPenjualanPerOfftakerUangMukaTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Klien $klien;
    protected Invoice $invoice;
    protected Penjualan $penjualan1;
    protected Penjualan $penjualan2;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_laporan_operasional']);
        Permission::firstOrCreate(['name' => 'view_invoice']);

        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_super_admin' => true,
        ]);
        $this->user->givePermissionTo(['view_laporan_operasional', 'view_invoice']);

        $this->klien = Klien::create([
            'tenant_id' => $this->tenant->id,
            'nama_klien' => 'Saiful Umroh',
            'jenis' => 'Offtaker',
            'kontak' => '081233534826',
        ]);

        // Invoice dengan total 2.007.960, DP 1.000.000, status Paid
        $this->invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'nomor_invoice' => 'INV/TPST/2026/07/003',
            'klien_id' => $this->klien->id,
            'tanggal_invoice' => '2026-07-15',
            'tanggal_jatuh_tempo' => '2026-07-15',
            'periode_bulan' => 'Juli',
            'periode_tahun' => '2026',
            'status' => 'Paid',
            'total_tagihan' => 2007960,
            'uang_muka' => 1000000,
        ]);

        $this->penjualan1 = Penjualan::create([
            'tenant_id' => $this->tenant->id,
            'klien_id' => $this->klien->id,
            'tanggal' => '2026-07-14',
            'jenis_produk' => 'Atom',
            'berat_kg' => 300,
            'harga_satuan' => 2000,
            'jumlah_bayar' => 0,
            'invoice_id' => $this->invoice->id,
            'status_invoice' => 'Paid',
        ]);

        $this->penjualan2 = Penjualan::create([
            'tenant_id' => $this->tenant->id,
            'klien_id' => $this->klien->id,
            'tanggal' => '2026-07-14',
            'jenis_produk' => 'Kardus',
            'berat_kg' => 703.98,
            'harga_satuan' => 2000,
            'jumlah_bayar' => 0,
            'invoice_id' => $this->invoice->id,
            'status_invoice' => 'Paid',
        ]);
    }

    public function test_laporan_operasional_shows_dp_for_paid_invoice(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.laporan-operasional.penjualan.per-offtaker-per-invoice', [
            'dari' => '2026-07-13',
            'sampai' => '2026-07-15',
            'klien_id' => $this->klien->id,
        ]));

        $response->assertStatus(200);

        // Harus menampilkan nomor invoice
        $response->assertSee('INV/TPST/2026/07/003');

        // Harus menampilkan DP 1.000.000
        $response->assertSee('DP: Rp 1.000.000');
        $response->assertSee('1.000.000');

        // Status Lunas dan Sisa 0
        $response->assertSee('Terbayar Lunas');
        $response->assertSee('Sisa: Rp 0');

        // Summary dan viewData
        $reports = $response->viewData('reports');
        $this->assertNotEmpty($reports);
        $this->assertEquals(1000000, $reports[0]->total_uang_muka);
        $this->assertEquals(2007960, $reports[0]->total_terbayar);
        $this->assertEquals(0, $reports[0]->total_sisa);

        $summary = $response->viewData('summary');
        $this->assertEquals(1000000, $summary->total_uang_muka);
    }

    public function test_invoice_index_shows_sisa_zero_for_paid_invoice(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'search' => 'INV/TPST/2026/07/003',
        ]));

        $response->assertStatus(200);
        $response->assertSee('INV/TPST/2026/07/003');
        $response->assertSee('Rp 2.007.960');
        $response->assertSee('Rp 1.000.000');
        // Sisa untuk Paid harus Rp 0, bukan Rp 1.007.960
        $response->assertSee('Rp 0');
    }
}
