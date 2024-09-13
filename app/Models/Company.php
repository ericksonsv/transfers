<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_name',
        'tradename',
        'logo_url',
        'rnc',
        'is_active'
    ];

    /**
     * Get the phones for the company.
     */
    public function phones(): HasMany
    {
        return $this->hasMany(CompanyPhone::class);
    }

    /**
     * Get the mails for the company.
     */
    public function mails(): HasMany
    {
        return $this->hasMany(CompanyMail::class);
    }

    /**
     * Get the customers for the company.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the orders for the company.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
