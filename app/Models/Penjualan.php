<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use TenantTrait, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'penjualan';

    protected $fillable = [
        'tenant_id',
        'klien_id',
        'tanggal',
        'jenis_produk',
        'berat_kg',
        'harga_satuan',
        'total_harga',
        'jumlah_bayar',
        'invoice_id',
        'status_invoice',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berat_kg' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
        
        static::saving(function (Penjualan $penjualan) {
            $penjualan->total_harga = ($penjualan->berat_kg ?? 0) * ($penjualan->harga_satuan ?? 0);
        });
    }

    /**
     * Get the tenant this penjualan belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the klien this penjualan belongs to.
     */
    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class);
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders()
    {
        return $this->morphMany(JurnalHeader::class, 'referensi');
    }

    /**
     * Get the invoice this penjualan belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
