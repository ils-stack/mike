<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantProperty extends Model
{
    protected $table = 'tenant_property';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'tenant_id',
        'property_id',
    ];

    // 🔗 Optional relationships for convenience
    public function tenant()
    {
        return $this->belongsTo(Tenants::class, 'tenant_id');
    }

    public function property()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }
}
