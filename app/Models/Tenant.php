<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'address',
        'email',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'director_name',
        'manager_name',
        'finance_name',
    ];

    /**
     * Get all users for the tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all klien for the tenant.
     */
    public function klien(): HasMany
    {
        return $this->hasMany(Klien::class);
    }

    /**
     * Get all armada for the tenant.
     */
    public function armada(): HasMany
    {
        return $this->hasMany(Armada::class);
    }

    /**
     * Get all ritase for the tenant.
     */
    public function ritase(): HasMany
    {
        return $this->hasMany(Ritase::class);
    }

    /**
     * Get all produksi harian records for the tenant.
     */
    public function produksiHarian(): HasMany
    {
        return $this->hasMany(ProduksiHarian::class);
    }

    /**
     * Get all penjualan for the tenant.
     */
    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Get all chart of accounts for the tenant.
     */
    public function coa(): HasMany
    {
        return $this->hasMany(Coa::class);
    }

    /**
     * Get all jurnal headers for the tenant.
     */
    public function jurnalHeader(): HasMany
    {
        return $this->hasMany(JurnalHeader::class);
    }
}
