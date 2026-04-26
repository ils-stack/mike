<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentProperty extends Model
{
    protected $table = 'agent_property';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'agent_id',
        'property_id',
    ];

    public function agent()
    {
        return $this->belongsTo(Agents::class, 'agent_id');
    }

    public function property()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }
}
