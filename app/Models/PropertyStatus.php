<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyStatus extends Model
{
    protected $table = 'property_status';

    protected $fillable = [
        'status',
        'marker_letter',
        'marker_color',
    ];

    public $timestamps = true;
}
