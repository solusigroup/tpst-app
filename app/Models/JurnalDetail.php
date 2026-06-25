<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalDetail extends Model
{
    use TenantTrait;

    protected $table = 'jurnal_detail';

    protected $fillable = [
        'tenant_id',
        'jurnal_header_id',
        'coa_id',
        'debit',
        'kredit',
        'contactable_type',
        'contactable_id',
    ];

    /**
     * Get the parent contactable model (Klien or Vendor).
     */
    public function contactable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    /**
     * Get the jurnal header this detail belongs to.
     */
    public function jurnalHeader(): BelongsTo
    {
        return $this->belongsTo(JurnalHeader::class);
    }

    /**
     * Get the COA this detail belongs to.
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class);
    }
}
