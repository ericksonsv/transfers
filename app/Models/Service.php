<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'client',
        'pickup_date',
        'pickup_time',
        'pickup_place',
        'dropoff_place',
        'flight_number',
        'flight_time',
        'passengers',
        'amount',
        'service_currency_id',
        'service_type_id',
        'service_status_id',
        'note',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class)->withDefault();
    }

    public function serviceStatus(): BelongsTo
    {
        return $this->belongsTo(ServiceStatus::class)->withDefault();
    }

    public function serviceCurrency(): BelongsTo
    {
        return $this->belongsTo(ServiceCurrency::class)->withDefault();
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class)->withTimestamps();;
    }
}
