<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'properties';

    public $timestamps = true;

    protected $fillable = [
        'building_name',
        'location',
        'address',
        'blurb',
        'type_id',
        'status_type',
        'erf_no',
        'erf_size',
        'gla',
        'zoning',
        'property_locale',
        'latitude',
        'longitude',
        'scheme_number',
    ];

    /**
     * 🔎 Accessor: readable label for type
     */
    public function getTypeNameAttribute()
    {
        return $this->type_id == 1
            ? 'Freehold'
            : ($this->type_id == 2 ? 'Leasehold' : 'Unknown');
    }

    /**
     * 🔎 Accessor: readable label for status
     */
    public function getStatusNameAttribute()
    {
        return $this->status_type == 1
            ? 'Tenanted'
            : ($this->status_type == 2 ? 'Vacant' : 'Unknown');
    }

    /**
     * 🔗 Many-to-Many relationship:
     * A property can belong to multiple landlords,
     * and a landlord can own multiple properties.
     *
     * 👉 Using landlords.* avoids ambiguous "id" column issues
     */
    public function landlords()
    {
        return $this->belongsToMany(
            \App\Models\Landlord::class,
            'landlord_property',
            'property_id',
            'landlord_id'
        )
        ->withTimestamps()
        ->select('landlords.*'); // ✅ avoids ambiguous id
    }

    // App\Models\Properties.php
    public function propertyManagers()
    {
        return $this->belongsToMany(
            \App\Models\PropertyManager::class,
            'property_manager_property',
            'property_id',
            'manager_id'
        );
    }

    public function tenants()
    {
        return $this->belongsToMany(
            \App\Models\Tenants::class,
            'tenant_property',
            'property_id',
            'tenant_id'
        )->withTimestamps();
    }

    // App\Models\Properties.php
    public function agents()
    {
        return $this->belongsToMany(
            \App\Models\Agents::class,
            'agent_property',
            'property_id',
            'agent_id'
        )->withTimestamps();
    }

    /**
     * 🔗 One-to-Many relationship:
     * A property can have many units.
     */
    // public function units()
    // {
    //     return $this->hasMany(\App\Models\UnitDetail::class, 'property_id');
    // }

    /**
     * 🔗 Many-to-Many relationship:
     * A property can have many units through unit_property pivot table.
     */
    public function units()
    {
        return $this->belongsToMany(
            \App\Models\UnitDetail::class,
            'unit_property',   // pivot table
            'property_id',     // FK to properties.id
            'unit_id'          // FK to unit_details.id
        )->withTimestamps();
    }

    // public function propertyLocation()
    // {
    //     return $this->belongsTo(
    //         \App\Models\PropertyLocation::class,
    //         'location', // FK in properties table
    //         'id'
    //     );
    // }

    public function propertyLocation()
{
    return $this->belongsTo(PropertyLocation::class, 'location', 'id');
}

public function propertyType()
{
    return $this->belongsTo(PropertyType::class, 'type_id', 'id');
}

public function propertyStatus()
{
    return $this->belongsTo(PropertyStatus::class, 'status_type', 'id');
}

public function propertyZoning()
{
    return $this->belongsTo(PropertyZoning::class, 'zoning', 'id');
}

public function propertyArea()
{
    return $this->belongsTo(PropertyArea::class, 'property_locale', 'id');
}

}
