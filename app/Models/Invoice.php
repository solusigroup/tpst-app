<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use TenantTrait;

    protected $fillable = [
        'tenant_id',
        'klien_id',
        'nomor_invoice',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'periode_bulan',
        'periode_tahun',
        'total_tagihan',
        'status',
        'keterangan',
        'deskripsi_layanan',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'total_tagihan' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Invoice $invoice) {
            if (empty($invoice->nomor_invoice)) {
                $year = $invoice->periode_tahun ?? date('Y');
                $month = $invoice->periode_bulan ?? date('m');
                $prefix = "INV/TPST/{$year}/" . str_pad($month, 2, '0', STR_PAD_LEFT) . "/";
                
                $lastInvoice = self::withoutGlobalScope(TenantScope::class)
                    ->where('tenant_id', $invoice->tenant_id)
                    ->where('nomor_invoice', 'like', $prefix . '%')
                    ->orderBy('nomor_invoice', 'desc')
                    ->first();
                
                $nextSequence = 1;
                if ($lastInvoice) {
                    $lastSequence = (int) substr($lastInvoice->nomor_invoice, -3);
                    $nextSequence = $lastSequence + 1;
                }
                
                $invoice->nomor_invoice = $prefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
            }
        });
        static::saved(function (Invoice $invoice) {
            // Cascade status down to attached items
            if ($invoice->isDirty('status')) {
                $invoice->ritase()->update(['status_invoice' => $invoice->status]);
                $invoice->penjualan()->update(['status_invoice' => $invoice->status]);
            }
        });
    }

    /**
     * Get the tenant this invoice belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the client this invoice belongs to.
     */
    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class);
    }

    /**
     * Get all ritase records linked to this invoice.
     */
    public function ritase(): HasMany
    {
        return $this->hasMany(Ritase::class);
    }

    /**
     * Get all penjualan records linked to this invoice.
     */
    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders()
    {
        return $this->morphMany(JurnalHeader::class, 'referensi');
    }
}
