<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengangkutanResidu extends Model
{
    use TenantTrait, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'armada_id',
        'nomor_tiket',
        'tanggal',
        'waktu_keluar',
        'waktu_masuk',
        'berat_bruto',
        'berat_tarra',
        'berat_netto',
        'biaya_retribusi',
        'tujuan',
        'keterangan',
        'jurnal_header_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berat_bruto' => 'decimal:2',
        'berat_tarra' => 'decimal:2',
        'berat_netto' => 'decimal:2',
        'biaya_retribusi' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (PengangkutanResidu $residu) {
            if (empty($residu->nomor_tiket)) {
                $prefix = 'PR-' . now()->format('Ym') . '-';
                
                $lastResidu = self::withoutGlobalScope(TenantScope::class)
                    ->withTrashed()
                    ->where('tenant_id', $residu->tenant_id)
                    ->where('nomor_tiket', 'like', $prefix . '%')
                    ->orderBy('nomor_tiket', 'desc')
                    ->first();
                
                $nextSequence = 1;
                if ($lastResidu) {
                    $lastSequence = (int) substr($lastResidu->nomor_tiket, -4);
                    $nextSequence = $lastSequence + 1;
                }
                
                $residu->nomor_tiket = $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (PengangkutanResidu $residu) {
            $residu->berat_netto = ($residu->berat_bruto ?? 0) - ($residu->berat_tarra ?? 0);
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function armada(): BelongsTo
    {
        return $this->belongsTo(Armada::class);
    }

    public function jurnalHeader(): BelongsTo
    {
        return $this->belongsTo(JurnalHeader::class);
    }
}
