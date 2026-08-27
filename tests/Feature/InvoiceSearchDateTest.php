<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Klien;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InvoiceSearchDateTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Klien $klien;
    protected Invoice $inv1;
    protected Invoice $inv2;
    protected Invoice $inv3;

    protected function setUp(): void
    {
        parent::setUp();

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
        $this->user->givePermissionTo(['view_invoice']);

        $this->klien = Klien::create([
            'tenant_id' => $this->tenant->id,
            'nama_klien' => 'PT Sumber Rejeki',
            'jenis' => 'Swasta',
            'kontak' => '08111222333',
        ]);

        $this->inv1 = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'nomor_invoice' => 'INV/2026/08/001',
            'klien_id' => $this->klien->id,
            'tanggal_invoice' => '2026-08-05',
            'tanggal_jatuh_tempo' => '2026-08-19',
            'periode_bulan' => 'Agustus',
            'periode_tahun' => '2026',
            'status' => 'Sent',
            'total_tagihan' => 1000000,
            'uang_muka' => 0,
        ]);

        $this->inv2 = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'nomor_invoice' => 'INV/2026/08/002',
            'klien_id' => $this->klien->id,
            'tanggal_invoice' => '2026-08-15',
            'tanggal_jatuh_tempo' => '2026-08-29',
            'periode_bulan' => 'Agustus',
            'periode_tahun' => '2026',
            'status' => 'Draft',
            'total_tagihan' => 2000000,
            'uang_muka' => 0,
        ]);

        $this->inv3 = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'nomor_invoice' => 'INV/2026/08/003',
            'klien_id' => $this->klien->id,
            'tanggal_invoice' => '2026-08-25',
            'tanggal_jatuh_tempo' => '2026-09-08',
            'periode_bulan' => 'Agustus',
            'periode_tahun' => '2026',
            'status' => 'Paid',
            'total_tagihan' => 3000000,
            'uang_muka' => 0,
        ]);
    }

    public function test_can_filter_invoices_by_date_range(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'dari' => '2026-08-10',
            'sampai' => '2026-08-20',
        ]));

        $response->assertStatus(200);
        $response->assertSee('INV/2026/08/002');
        $response->assertDontSee('INV/2026/08/001');
        $response->assertDontSee('INV/2026/08/003');
    }

    public function test_can_filter_invoices_by_dari_only(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'dari' => '2026-08-15',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('INV/2026/08/001');
        $response->assertSee('INV/2026/08/002');
        $response->assertSee('INV/2026/08/003');
    }

    public function test_can_filter_invoices_by_sampai_only(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'sampai' => '2026-08-15',
        ]));

        $response->assertStatus(200);
        $response->assertSee('INV/2026/08/001');
        $response->assertSee('INV/2026/08/002');
        $response->assertDontSee('INV/2026/08/003');
    }

    public function test_can_filter_using_start_and_end_date_aliases(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-30',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('INV/2026/08/001');
        $response->assertDontSee('INV/2026/08/002');
        $response->assertSee('INV/2026/08/003');
    }

    public function test_can_search_invoice_by_date_in_search_field(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'search' => '2026-08-25',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('INV/2026/08/001');
        $response->assertDontSee('INV/2026/08/002');
        $response->assertSee('INV/2026/08/003');

        // Formatted Indonesian date
        $responseId = $this->actingAs($this->user)->get(route('admin.invoice.index', [
            'search' => '05/08/2026',
        ]));

        $responseId->assertStatus(200);
        $responseId->assertSee('INV/2026/08/001');
        $responseId->assertDontSee('INV/2026/08/002');
        $responseId->assertDontSee('INV/2026/08/003');
    }
}
