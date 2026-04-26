<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitDetails extends Model
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
        'gross_rental',
        'sale_price',
        'yield_percentage',
        'parking_bays',
        'parking_rental',
    ];

    /**
     * A Unit can belong to one or more Properties (via pivot table unit_property)
     */
    public function properties()
    {
        return $this->belongsToMany(
            \App\Models\Properties::class,
            'unit_property',
            'unit_id',
            'property_id'
        );
    }

    /**
     * Helper: Most units belong to only one property.
     * This returns the FIRST associated property (not a collection).
     */
    public function property()
    {
        return $this->properties()->first();
    }
}
