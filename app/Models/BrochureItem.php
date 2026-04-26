<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrochureItem extends Model
{
    protected $table = 'brochure_items';

    protected $fillable = [
        'brochure_id',
        'unit_id',      // updated
        'sort_order',
    ];

    public function brochure()
    {
        return $this->belongsTo(Brochure::class, 'brochure_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\UnitDetails::class, 'unit_id', 'id');
    }
}
