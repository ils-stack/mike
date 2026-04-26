<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitDetail extends Model
{
    protected $table = 'unit_details';

    protected $fillable = [
        'unit_type',
        'company_as_listing_broker',
        'listing_broker',
        'deal_file_id',
        'availability',
        'lease_expiry',
        'unit_no',
        'unit_size',
        'unit_status',
        'gross_rental',
        'sale_price',
        'yield_percentage',
        'parking_bays',
        'parking_rental',
    ];

    protected $casts = [
        'company_as_listing_broker' => 'boolean',
        'lease_expiry' => 'date',
        'unit_size' => 'decimal:2',
        'gross_rental' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // 🔗 Many-to-many relation with Properties
    public function properties()
    {
        return $this->belongsToMany(Properties::class, 'unit_property', 'unit_id', 'property_id')
                    ->withTimestamps();
    }
}
