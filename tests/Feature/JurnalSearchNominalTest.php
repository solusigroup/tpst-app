<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalHeader;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class JurnalSearchNominalTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Coa $coaKas;
    protected Coa $coaPendapatan;
    protected Coa $coaBeban;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_jurnal']);
        Permission::firstOrCreate(['name' => 'create_jurnal']);

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
        ]);
        $this->user->givePermissionTo('view_jurnal');

        $this->coaKas = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '1101',
            'nama_akun' => 'Kas',
            'tipe' => 'Asset',
            'klasifikasi' => 'Aset Lancar',
        ]);

        $this->coaPendapatan = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '4101',
            'nama_akun' => 'Pendapatan Jasa',
            'tipe' => 'Revenue',
            'klasifikasi' => 'Pendapatan',
        ]);

        $this->coaBeban = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '5101',
            'nama_akun' => 'Beban Listrik',
            'tipe' => 'Expense',
            'klasifikasi' => 'Beban Operasional',
        ]);
    }

    public function test_can_search_nominal_on_any_side(): void
    {
        // Journal 1: Kas (D: 50.000), Pendapatan (K: 50.000)
        $j1 = JurnalHeader::create([
            'tenant_id' => $this->tenant->id,
            'nomor_referensi' => 'JV-TEST-001',
            'tanggal' => now()->toDateString(),
            'deskripsi' => 'Transaksi 50rb',
            'status' => 'posted',
        ]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaKas->id, 'debit' => 50000, 'kredit' => 0]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaPendapatan->id, 'debit' => 0, 'kredit' => 50000]);

        // Journal 2: Beban (D: 75.000), Kas (K: 75.000)
        $j2 = JurnalHeader::create([
            'tenant_id' => $this->tenant->id,
            'nomor_referensi' => 'JV-TEST-002',
            'tanggal' => now()->toDateString(),
            'deskripsi' => 'Transaksi 75rb',
            'status' => 'posted',
        ]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j2->id, 'coa_id' => $this->coaBeban->id, 'debit' => 75000, 'kredit' => 0]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j2->id, 'coa_id' => $this->coaKas->id, 'debit' => 0, 'kredit' => 75000]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.jurnal.index', ['nominal' => '50000']));

        $response->assertOk();
        $response->assertSee('JV-TEST-001');
        $response->assertDontSee('JV-TEST-002');
    }

    public function test_can_search_nominal_with_formatted_dots(): void
    {
        // Journal with 1.500.000
        $j1 = JurnalHeader::create([
            'tenant_id' => $this->tenant->id,
            'nomor_referensi' => 'JV-TEST-003',
            'tanggal' => now()->toDateString(),
            'deskripsi' => 'Transaksi 1.5jt',
            'status' => 'posted',
        ]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaKas->id, 'debit' => 1500000, 'kredit' => 0]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaPendapatan->id, 'debit' => 0, 'kredit' => 1500000]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.jurnal.index', ['nominal' => '1.500.000']));

        $response->assertOk();
        $response->assertSee('JV-TEST-003');
    }

    public function test_can_search_nominal_specifically_on_debit_side(): void
    {
        // Compound journal:
        // Debit: Beban Listrik 100.000
        // Kredit: Kas 80.000
        // Kredit: Utang/Lainnya 20.000
        $j1 = JurnalHeader::create([
            'tenant_id' => $this->tenant->id,
            'nomor_referensi' => 'JV-COMPOUND-01',
            'tanggal' => now()->toDateString(),
            'deskripsi' => 'Listrik dan cicilan',
            'status' => 'posted',
        ]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaBeban->id, 'debit' => 100000, 'kredit' => 0]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaKas->id, 'debit' => 0, 'kredit' => 80000]);
        JurnalDetail::create(['tenant_id' => $this->tenant->id, 'jurnal_header_id' => $j1->id, 'coa_id' => $this->coaPendapatan->id, 'debit' => 0, 'kredit' => 20000]);

        // Search 80.000 on Debit side -> should NOT match JV-COMPOUND-01
        $resDebit = $this->actingAs($this->user)
            ->get(route('admin.jurnal.index', ['nominal' => '80000', 'posisi' => 'debit']));
        $resDebit->assertOk();
        $resDebit->assertDontSee('JV-COMPOUND-01');

        // Search 80.000 on Kredit side -> SHOULD match JV-COMPOUND-01
        $resKredit = $this->actingAs($this->user)
            ->get(route('admin.jurnal.index', ['nominal' => '80000', 'posisi' => 'kredit']));
        $resKredit->assertOk();
        $resKredit->assertSee('JV-COMPOUND-01');

        // Search 100.000 on Debit side -> SHOULD match JV-COMPOUND-01
        $resDebit100 = $this->actingAs($this->user)
            ->get(route('admin.jurnal.index', ['nominal' => '100000', 'posisi' => 'debit']));
        $resDebit100->assertOk();
        $resDebit100->assertSee('JV-COMPOUND-01');

        // Search 100.000 on Kredit side -> should NOT match JV-COMPOUND-01
        $resKredit100 = $this->actingAs($this->user)
            ->get(route('admin.jurnal.index', ['nominal' => '100000', 'posisi' => 'kredit']));
        $resKredit100->assertOk();
        $resKredit100->assertDontSee('JV-COMPOUND-01');
    }
}
