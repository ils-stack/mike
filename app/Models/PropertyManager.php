<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyManager extends Model
{
    use HasFactory;

    protected $table = 'property_managers';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'company_name',
        'entity_name',
        'manager_name',
        'contact_person',
        'telephone',
        'cell_number',
        'email',
    ];

    /**
     * 🔗 Assigned Properties (Pivot: property_manager_property)
     */
    public function properties()
    {
        return $this->belongsToMany(
            Properties::class,
            'property_manager_property',
            'manager_id',
            'property_id'
        );
    }
}
