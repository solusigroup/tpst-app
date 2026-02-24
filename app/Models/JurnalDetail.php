<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';

    protected $fillable = [
        'jurnal_header_id',
        'coa_id',
        'debit',
        'kredit',
    ];

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
