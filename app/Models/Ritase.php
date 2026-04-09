<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ritase extends Model
{
    use TenantTrait, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'ritase';

    protected $fillable = [
        'tenant_id',
        'armada_id',
        'klien_id',
        'invoice_id',
        'nomor_tiket',
        'tiket',
        'foto_tiket',
        'foto_tiket_bruto',
        'foto_tiket_tarra',
        'waktu_masuk',
        'waktu_keluar',
        'berat_bruto',
        'berat_tarra',
        'berat_netto',
        'jenis_sampah',
        'biaya_tipping',
        'status',
        'status_invoice',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'berat_bruto' => 'decimal:2',
        'berat_tarra' => 'decimal:2',
        'berat_netto' => 'decimal:2',
        'biaya_tipping' => 'decimal:2',
        'status' => 'string',
        'invoice_id' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Ritase $ritase) {
            if (empty($ritase->nomor_tiket)) {
                $prefix = 'RT-' . now()->format('Ym') . '-';
                
                // Get the last ticket number for this month
                $lastRitase = self::withoutGlobalScope(TenantScope::class)
                    ->where('tenant_id', $ritase->tenant_id)
                    ->where('nomor_tiket', 'like', $prefix . '%')
                    ->orderBy('nomor_tiket', 'desc')
                    ->first();
                
                $nextSequence = 1;
                if ($lastRitase) {
                    $lastSequence = (int) substr($lastRitase->nomor_tiket, -4);
                    $nextSequence = $lastSequence + 1;
                }
                
                $ritase->nomor_tiket = $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (Ritase $ritase) {
            $ritase->berat_netto = ($ritase->berat_bruto ?? 0) - ($ritase->berat_tarra ?? 0);
        });
    }

    /**
     * Get the tenant this ritase belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the armada this ritase belongs to.
     */
    public function armada(): BelongsTo
    {
        return $this->belongsTo(Armada::class);
    }

    /**
     * Get the klien this ritase belongs to.
     */
    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class);
    }

    /**
     * Get the invoice this ritase belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders()
    {
        return $this->morphMany(JurnalHeader::class, 'referensi');
    }
}
