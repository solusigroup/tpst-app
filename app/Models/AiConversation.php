<?php

namespace App\Models;

use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiConversation extends Model
{
    use TenantTrait;

    protected $table = 'ai_conversations';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'session_id',
        'role',
        'content',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    /* ───────────────── Relationships ───────────────── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ───────────────── Scopes ───────────────── */

    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeRecent($query, int $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
