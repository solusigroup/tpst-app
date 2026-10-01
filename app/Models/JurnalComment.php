<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalComment extends Model
{
    use TenantTrait;

    protected $table = 'jurnal_comments';

    protected $fillable = [
        'tenant_id',
        'jurnal_header_id',
        'user_id',
        'comment',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jurnalHeader(): BelongsTo
    {
        return $this->belongsTo(JurnalHeader::class, 'jurnal_header_id');
    }
}
