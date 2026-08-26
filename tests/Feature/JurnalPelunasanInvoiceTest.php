<?php

namespace Tests\Feature;

use App\Models\BukuPembantu;
use App\Models\Coa;
use App\Models\Invoice;
use App\Models\JurnalHeader;
use App\Models\Klien;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class JurnalPelunasanInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Klien $klien;
    protected Coa $bankCoa;
    protected Coa $piutangCoa;
    protected Coa $pendapatanCoa;
    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_jurnal']);
        Permission::firstOrCreate(['name' => 'create_jurnal']);
        Permission::firstOrCreate(['name' => 'delete_jurnal']);

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
        $this->user->givePermissionTo(['view_jurnal', 'create_jurnal', 'delete_jurnal']);

        $this->bankCoa = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '1103',
            'nama_akun' => 'Bank Jatim',
            'tipe' => 'Asset',
            'klasifikasi' => 'Aset Lancar',
        ]);

        $this->piutangCoa = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '1104',
            'nama_akun' => 'Piutang Usasta',
            'tipe' => 'Asset',
            'klasifikasi' => 'Aset Lancar',
            'kategori_buku_pembantu' => 'piutang_swasta',
        ]);

        $this->pendapatanCoa = Coa::create([
            'tenant_id' => $this->tenant->id,
            'kode_akun' => '4103',
            'nama_akun' => 'Pendapatan Tipping Fee Swasta',
            'tipe' => 'Revenue',
            'klasifikasi' => 'Pendapatan Operasional',
        ]);

        $this->klien = Klien::create([
            'tenant_id' => $this->tenant->id,
            'nama_klien' => 'PT Swasta Jaya',
            'jenis' => 'Swasta',
            'kontak' => '08123456789',
        ]);

        // Create Invoice with status Sent (which generates Piutang Journal 1)
        $this->invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'nomor_invoice' => 'INV/TEST/2026/08/001',
            'klien_id' => $this->klien->id,
            'tanggal_invoice' => '2026-08-01',
            'tanggal_jatuh_tempo' => '2026-08-31',
            'periode_bulan' => 'Agustus',
            'periode_tahun' => '2026',
            'status' => 'Sent',
            'total_tagihan' => 5000000,
            'uang_muka' => 0,
        ]);
    }

    public function test_pelunasan_create_form_loads_correct_defaults(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.jurnal.create', [
            'ref_type' => 'App\Models\Invoice',
            'ref_id' => $this->invoice->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('isPelunasan', true);
        $response->assertViewHas('row0Debit', 5000000.0);
        $response->assertViewHas('row1Kredit', 5000000.0);
        $response->assertViewHas('row0CoaId', $this->bankCoa->id);
        $response->assertViewHas('row1CoaId', $this->piutangCoa->id);
        $response->assertSee('Otomatis (Nomor Baru)');
        $response->assertSee('Penerimaan Pembayaran Pelunasan Invoice INV/TEST/2026/08/001 - PT Swasta Jaya');
    }

    public function test_pelunasan_create_form_supports_url_encoded_ref_type(): void
    {
        // Even if double URL encoded (%255C), it should load smoothly
        $response = $this->actingAs($this->user)->get(route('admin.jurnal.create', [
            'ref_type' => 'App%5CModels%5CInvoice',
            'ref_id' => $this->invoice->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('isPelunasan', true);
        $response->assertViewHas('row0Debit', 5000000.0);
    }

    public function test_pelunasan_store_generates_new_journal_reference_number(): void
    {
        // Verify initial Piutang journal exists
        $piutangJournal = JurnalHeader::where('referensi_type', Invoice::class)
            ->where('referensi_id', $this->invoice->id)
            ->first();

        $this->assertNotNull($piutangJournal);
        $this->assertStringContainsString('Piutang Invoice', $piutangJournal->deskripsi);
        $originalNoRef = $piutangJournal->nomor_referensi;

        // Post pelunasan journal
        $postData = [
            'tanggal' => '2026-08-26',
            'deskripsi' => "Penerimaan Pembayaran Pelunasan Invoice {$this->invoice->nomor_invoice} - {$this->klien->nama_klien}",
            'referensi_type' => Invoice::class,
            'referensi_id' => $this->invoice->id,
            'details' => [
                [
                    'coa_id' => $this->bankCoa->id,
                    'debit' => 5000000,
                    'kredit' => 0,
                    'contactable_type_id' => null,
                ],
                [
                    'coa_id' => $this->piutangCoa->id,
                    'debit' => 0,
                    'kredit' => 5000000,
                    'contactable_type_id' => "App\\Models\\Klien:{$this->klien->id}",
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.jurnal.store'), $postData);

        $response->assertRedirect(route('admin.jurnal.index'));
        $response->assertSessionHasNoErrors();

        // Check that a NEW journal with a DIFFERENT nomor_referensi was created
        $journals = JurnalHeader::where('referensi_type', Invoice::class)
            ->where('referensi_id', $this->invoice->id)
            ->get();

        $this->assertCount(2, $journals);

        $pelunasanJournal = $journals->firstWhere('id', '!=', $piutangJournal->id);
        $this->assertNotNull($pelunasanJournal);
        $this->assertNotEquals($originalNoRef, $pelunasanJournal->nomor_referensi);
        $this->assertStringContainsString('Penerimaan Pembayaran Pelunasan', $pelunasanJournal->deskripsi);

        // Verify Invoice status changed to Paid
        $this->invoice->refresh();
        $this->assertEquals('Paid', $this->invoice->status);

        // Verify BukuPembantu settled
        $bp = BukuPembantu::where('contactable_type', Klien::class)
            ->where('contactable_id', $this->klien->id)
            ->first();

        $this->assertNotNull($bp);
        $this->assertEquals('lunas', $bp->status);
        $this->assertEquals($pelunasanJournal->id, $bp->settled_by_jurnal_header_id);
    }

    public function test_subsequent_pelunasan_for_same_invoice_creates_new_journal_reference(): void
    {
        $postData1 = [
            'tanggal' => '2026-08-26',
            'deskripsi' => "Penerimaan Pembayaran Pelunasan Invoice {$this->invoice->nomor_invoice} - {$this->klien->nama_klien}",
            'referensi_type' => Invoice::class,
            'referensi_id' => $this->invoice->id,
            'details' => [
                [
                    'coa_id' => $this->bankCoa->id,
                    'debit' => 2500000,
                    'kredit' => 0,
                    'contactable_type_id' => null,
                ],
                [
                    'coa_id' => $this->piutangCoa->id,
                    'debit' => 0,
                    'kredit' => 2500000,
                    'contactable_type_id' => "App\\Models\\Klien:{$this->klien->id}",
                ],
            ],
        ];

        // First payment succeeds
        $this->actingAs($this->user)->post(route('admin.jurnal.store'), $postData1);

        $postData2 = [
            'tanggal' => '2026-08-27',
            'deskripsi' => "Penerimaan Pembayaran Pelunasan Tahap 2 Invoice {$this->invoice->nomor_invoice} - {$this->klien->nama_klien}",
            'referensi_type' => Invoice::class,
            'referensi_id' => $this->invoice->id,
            'details' => [
                [
                    'coa_id' => $this->bankCoa->id,
                    'debit' => 2500000,
                    'kredit' => 0,
                    'contactable_type_id' => null,
                ],
                [
                    'coa_id' => $this->piutangCoa->id,
                    'debit' => 0,
                    'kredit' => 2500000,
                    'contactable_type_id' => "App\\Models\\Klien:{$this->klien->id}",
                ],
            ],
        ];

        // Second payment attempt for the same invoice also succeeds and generates its own unique reference number
        $response = $this->actingAs($this->user)->post(route('admin.jurnal.store'), $postData2);
        $response->assertRedirect(route('admin.jurnal.index'));
        $response->assertSessionHasNoErrors();

        $journals = JurnalHeader::where('referensi_type', Invoice::class)
            ->where('referensi_id', $this->invoice->id)
            ->get();

        // 1 Piutang journal + 2 Payment journals = 3 journals total
        $this->assertCount(3, $journals);

        $noRefs = $journals->pluck('nomor_referensi')->unique();
        $this->assertCount(3, $noRefs);
    }

    public function test_purging_pelunasan_journal_reverts_invoice_status_to_sent(): void
    {
        $postData = [
            'tanggal' => '2026-08-26',
            'deskripsi' => "Penerimaan Pembayaran Pelunasan Invoice {$this->invoice->nomor_invoice} - {$this->klien->nama_klien}",
            'referensi_type' => Invoice::class,
            'referensi_id' => $this->invoice->id,
            'details' => [
                [
                    'coa_id' => $this->bankCoa->id,
                    'debit' => 5000000,
                    'kredit' => 0,
                    'contactable_type_id' => null,
                ],
                [
                    'coa_id' => $this->piutangCoa->id,
                    'debit' => 0,
                    'kredit' => 5000000,
                    'contactable_type_id' => "App\\Models\\Klien:{$this->klien->id}",
                ],
            ],
        ];

        $this->actingAs($this->user)->post(route('admin.jurnal.store'), $postData);
        $this->invoice->refresh();
        $this->assertEquals('Paid', $this->invoice->status);

        $pelunasanJournal = JurnalHeader::where('referensi_type', Invoice::class)
            ->where('referensi_id', $this->invoice->id)
            ->where('deskripsi', 'like', '%Pelunasan%')
            ->first();

        // Purge pelunasan journal (route uses POST)
        $response = $this->actingAs($this->user)->post(route('admin.jurnal.purge', $pelunasanJournal));
        $response->assertRedirect(route('admin.jurnal.index'));

        // Invoice status reverted to Sent
        $this->invoice->refresh();
        $this->assertEquals('Sent', $this->invoice->status);

        // BukuPembantu reverted to pending
        $bp = BukuPembantu::where('contactable_type', Klien::class)
            ->where('contactable_id', $this->klien->id)
            ->first();
        $this->assertEquals('pending', $bp->status);
        $this->assertNull($bp->settled_by_jurnal_header_id);
    }
}
