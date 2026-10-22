<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\JurnalKas;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class JurnalKasDuplikatTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Coa $coaKas;
    protected Coa $coaBeban;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_jurnal_kas']);
        Permission::firstOrCreate(['name' => 'create_jurnal_kas']);
        Permission::firstOrCreate(['name' => 'delete_jurnal_kas']);

        $this->tenant = Tenant::create([
            'name' => 'TPST Test Tenant',
            'domain' => 'tpst.example.com',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Admin Kas',
            'username' => 'adminkas',
            'email' => 'adminkas@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->user->givePermissionTo('view_jurnal_kas');

        $this->coaKas = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '1101',
            'nama_akun' => 'Kas Operasional',
            'tipe' => 'Asset',
            'klasifikasi' => 'Aset Lancar',
        ]);

        $this->coaBeban = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '5101',
            'nama_akun' => 'Beban Operasional',
            'tipe' => 'Expense',
            'klasifikasi' => 'Beban Operasional',
        ]);
    }

    public function test_description_similarity_logic()
    {
        // Sama persis
        $this->assertTrue(JurnalKas::isDescriptionSimilar('Beli BBM Armada 1', 'Beli BBM Armada 1'));

        // Case insensitive dan spasi/tanda baca
        $this->assertTrue(JurnalKas::isDescriptionSimilar('beli bbm armada 1', 'Beli BBM Armada 1.'));

        // Variasi penulisan mirip
        $this->assertTrue(JurnalKas::isDescriptionSimilar('Beli BBM armada 01', 'Beli BBM armada 1'));
        $this->assertTrue(JurnalKas::isDescriptionSimilar('Pembelian solar truk sampah B 1234 CD', 'Beli solar truk sampah B 1234 CD'));

        // Deskripsi berbeda (bukan duplikat)
        $this->assertFalse(JurnalKas::isDescriptionSimilar('Beli pulsa listrik', 'Beli bensin motor'));
        $this->assertFalse(JurnalKas::isDescriptionSimilar('Konsumsi rapat pengurus', 'Beli solar truk'));
        $this->assertFalse(JurnalKas::isDescriptionSimilar('', 'Beli solar truk'));
        $this->assertFalse(JurnalKas::isDescriptionSimilar(null, 'Beli solar truk'));

        // Keduanya kosong
        $this->assertTrue(JurnalKas::isDescriptionSimilar('', ''));
        $this->assertTrue(JurnalKas::isDescriptionSimilar(null, null));
    }

    public function test_same_nominal_different_description_on_same_date_is_not_duplicate()
    {
        $this->actingAs($this->user);

        // Dua transaksi pada tanggal sama, nominal sama (50.000), tapi deskripsi berbeda
        JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 50000,
            'deskripsi' => 'Beli bensin motor operasional',
        ]);

        JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 50000,
            'deskripsi' => 'Konsumsi makan siang staf',
        ]);

        $stats = JurnalKas::getDuplicateStats();
        $this->assertEquals(0, $stats['group_count']);
        $this->assertEquals(0, $stats['total_items_count']);
        $this->assertEmpty($stats['duplicate_ids']);

        $groups = JurnalKas::getDuplicateGroups();
        $this->assertCount(0, $groups);
    }

    public function test_same_nominal_different_date_is_not_duplicate()
    {
        $this->actingAs($this->user);

        // Dua transaksi tanggal berbeda, nominal sama (100.000), deskripsi sama
        JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-20',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 100000,
            'deskripsi' => 'Beli solar truk armada',
        ]);

        JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-21',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 100000,
            'deskripsi' => 'Beli solar truk armada',
        ]);

        $stats = JurnalKas::getDuplicateStats();
        $this->assertEquals(0, $stats['group_count']);
        $this->assertEquals(0, $stats['total_items_count']);
    }

    public function test_same_nominal_same_date_similar_description_is_duplicate()
    {
        $this->actingAs($this->user);

        // Dua transaksi tanggal sama, nominal sama (75.000), deskripsi mirip (duplikat input)
        $t1 = JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 75000,
            'deskripsi' => 'Beli solar armada 01',
        ]);

        $t2 = JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 75000,
            'deskripsi' => 'Beli solar armada 1',
        ]);

        $stats = JurnalKas::getDuplicateStats();
        $this->assertEquals(1, $stats['group_count']);
        $this->assertEquals(2, $stats['total_items_count']);
        $this->assertEquals(75000, $stats['potensi_redundansi_nominal']);
        $this->assertContains($t1->id, $stats['duplicate_ids']);
        $this->assertContains($t2->id, $stats['duplicate_ids']);

        $groups = JurnalKas::getDuplicateGroups();
        $this->assertCount(1, $groups);
        $this->assertCount(2, $groups->first());
    }

    public function test_mixed_entries_on_same_date_and_nominal_only_groups_similar_descriptions()
    {
        $this->actingAs($this->user);

        // Pada tanggal 2026-10-22 dan nominal 100.000:
        // Item A1 & A2 mirip (duplikat)
        // Item B adalah transaksi terpisah yang sah (bukan duplikat)
        $a1 = JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 100000,
            'deskripsi' => 'Beli Solar Truk B 1234 CD',
        ]);

        $a2 = JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 100000,
            'deskripsi' => 'beli solar truk b 1234 cd',
        ]);

        $b = JurnalKas::create([
            'tenant_id' => $this->tenant->id,
            'tanggal' => '2026-10-22',
            'tipe' => 'Pengeluaran',
            'coa_kas_id' => $this->coaKas->id,
            'coa_lawan_id' => $this->coaBeban->id,
            'nominal' => 100000,
            'deskripsi' => 'Biaya Konsumsi Rapat Bulanan',
        ]);

        $stats = JurnalKas::getDuplicateStats();
        $this->assertEquals(1, $stats['group_count']);
        $this->assertEquals(2, $stats['total_items_count']);
        $this->assertContains($a1->id, $stats['duplicate_ids']);
        $this->assertContains($a2->id, $stats['duplicate_ids']);
        $this->assertNotContains($b->id, $stats['duplicate_ids']);

        // Halaman duplikat dapat diakses dengan sukses
        $response = $this->get(route('admin.jurnal-kas.duplikat'));
        $response->assertStatus(200);
        $response->assertSee('Beli Solar Truk B 1234 CD');
        $response->assertDontSee('Biaya Konsumsi Rapat Bulanan');
    }
}
