<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenants extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'company_name',
        'entity_name',
        'contact_person',
        'telephone',
        'cell_number',
        'email',
    ];

    // 🔗 Future relationship with properties (optional)
    public function properties()
    {
        return $this->belongsToMany(Properties::class, 'tenant_property', 'tenant_id', 'property_id')
                    ->withTimestamps();
    }
}
