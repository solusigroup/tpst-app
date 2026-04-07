<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class MachineLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'machine_id',
        'waktu_cek',
        'status_lampu',
        'keterangan',
        'user_id',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
