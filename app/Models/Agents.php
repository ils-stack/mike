<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    use HasFactory;

    protected $table = 'agents';
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

    // 🔗 Optional future relation to properties
    public function properties()
    {
        return $this->belongsToMany(Properties::class, 'agent_property', 'agent_id', 'property_id')
                    ->withTimestamps();
    }
}
