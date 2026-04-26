<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Landlord extends Model
{
    protected $table = 'landlords';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'company_name',
        'entity_name',
        'registration_number', // ✅ NEW
        'contact_person',
        'telephone',
        'cell_number',
        'email',
    ];

    /**
     * 🔗 Many-to-Many relationship:
     * A landlord can own multiple properties,
     * and a property can belong to multiple landlords.
     *
     * 👉 Using properties.* avoids ambiguous "id" column issues
     */
    public function properties()
    {
        return $this->belongsToMany(
            \App\Models\Properties::class,
            'landlord_property',
            'landlord_id',
            'property_id'
        )
        ->withTimestamps()
        ->select('properties.*');
    }
}
