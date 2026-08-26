<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class JurnalRekonsiliasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_jurnal']);
        Permission::firstOrCreate(['name' => 'create_jurnal']);

        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'is_active' => true,
        ]);

        Coa::create([
            'tenant_id' => $tenant->id,
            'kode_akun' => '1103',
            'nama_akun' => 'Bank Jatim',
            'tipe' => 'Asset',
            'klasifikasi' => 'Aset Lancar',
        ]);

        Coa::create([
            'tenant_id' => $tenant->id,
            'kode_akun' => '8102',
            'nama_akun' => 'Beban Administrasi Bank',
            'tipe' => 'Expense',
            'klasifikasi' => 'Beban Non-Operasional',
        ]);

        Coa::create([
            'tenant_id' => $tenant->id,
            'kode_akun' => '7102',
            'nama_akun' => 'Pendapatan Bunga Bank',
            'tipe' => 'Revenue',
            'klasifikasi' => 'Pendapatan Non-Operasional',
        ]);
    }

    public function test_jurnal_create_with_bank_reconciliation_pengeluaran(): void
    {
        $tenant = Tenant::first();
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_super_admin' => true,
        ]);
        $user->givePermissionTo(['view_jurnal', 'create_jurnal']);

        $bankCoa = Coa::where('nama_akun', 'like', '%Bank Jatim%')->first();
        $targetCoaId = $bankCoa->id;

        $response = $this->actingAs($user)->get(route('admin.jurnal.create', [
            'tanggal' => '2026-08-20',
            'deskripsi' => 'BIAYA ADM REK',
            'nominal' => 25000,
            'tipe' => 'keluar',
            'target_coa_id' => $targetCoaId,
            'source' => 'rekonsiliasi_bank',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('row0Debit', 25000.0);
        $response->assertViewHas('row1Kredit', 25000.0);
        $response->assertViewHas('row1CoaId', $targetCoaId);
        $response->assertViewHas('defaultDeskripsi', 'BIAYA ADM REK');
        $response->assertViewHas('defaultTanggal', '2026-08-20');
    }

    public function test_jurnal_create_with_bank_reconciliation_penerimaan(): void
    {
        $tenant = Tenant::first();
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Test',
            'username' => 'admintest2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_super_admin' => true,
        ]);
        $user->givePermissionTo(['view_jurnal', 'create_jurnal']);

        $bankCoa = Coa::where('nama_akun', 'like', '%Bank Jatim%')->first();
        $targetCoaId = $bankCoa->id;

        $response = $this->actingAs($user)->get(route('admin.jurnal.create', [
            'tanggal' => '2026-08-21',
            'deskripsi' => 'BUNGA TABUNGAN',
            'nominal' => 50000,
            'tipe' => 'masuk',
            'target_coa_id' => $targetCoaId,
            'source' => 'rekonsiliasi_bank',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('row0Debit', 50000.0);
        $response->assertViewHas('row0CoaId', $targetCoaId);
        $response->assertViewHas('row1Kredit', 50000.0);
        $response->assertViewHas('defaultDeskripsi', 'BUNGA TABUNGAN');
        $response->assertViewHas('defaultTanggal', '2026-08-21');
    }

    public function test_jurnal_store_from_bank_reconciliation_pengeluaran(): void
    {
        $tenant = Tenant::first();
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Test',
            'username' => 'admintest3',
            'email' => 'admin3@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_super_admin' => true,
        ]);
        $user->givePermissionTo(['view_jurnal', 'create_jurnal']);

        $bankCoa = Coa::where('nama_akun', 'like', '%Bank Jatim%')->first();
        $admCoa = Coa::where('nama_akun', 'like', '%Administrasi%')->first();

        // Seed initial balance for Bank Jatim
        $initialHeader = \App\Models\JurnalHeader::create([
            'tenant_id' => $tenant->id,
            'tanggal' => '2026-08-01',
            'deskripsi' => 'Saldo Awal',
            'status' => 'posted',
        ]);
        $initialHeader->jurnalDetails()->create([
            'coa_id' => $bankCoa->id,
            'debit' => 1000000,
            'kredit' => 0,
        ]);
        $initialHeader->jurnalDetails()->create([
            'coa_id' => $admCoa->id,
            'debit' => 0,
            'kredit' => 1000000,
        ]);

        $postData = [
            'tanggal' => '2026-08-20',
            'deskripsi' => 'BIAYA ADM REK',
            'details' => [
                [
                    'coa_id' => $admCoa->id,
                    'debit' => 25000,
                    'kredit' => 0,
                ],
                [
                    'coa_id' => $bankCoa->id,
                    'debit' => 0,
                    'kredit' => 25000,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('admin.jurnal.store'), $postData);

        $response->assertRedirect(route('admin.jurnal.index'));
        $this->assertDatabaseHas('jurnal_header', [
            'deskripsi' => 'BIAYA ADM REK',
        ]);
    }

    public function test_jurnal_store_from_bank_reconciliation_penerimaan(): void
    {
        $tenant = Tenant::first();
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Test',
            'username' => 'admintest4',
            'email' => 'admin4@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_super_admin' => true,
        ]);
        $user->givePermissionTo(['view_jurnal', 'create_jurnal']);

        $bankCoa = Coa::where('nama_akun', 'like', '%Bank Jatim%')->first();
        $bungaCoa = Coa::where('nama_akun', 'like', '%Bunga%')->first();

        $postData = [
            'tanggal' => '2026-08-21',
            'deskripsi' => 'BUNGA TABUNGAN',
            'details' => [
                [
                    'coa_id' => $bankCoa->id,
                    'debit' => 50000,
                    'kredit' => 0,
                ],
                [
                    'coa_id' => $bungaCoa->id,
                    'debit' => 0,
                    'kredit' => 50000,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('admin.jurnal.store'), $postData);

        $response->assertRedirect(route('admin.jurnal.index'));
        $this->assertDatabaseHas('jurnal_header', [
            'deskripsi' => 'BUNGA TABUNGAN',
        ]);
    }
}
