<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyArea extends Model
{
    protected $table = 'property_area';

    protected $fillable = [
        'area',
    ];

    public $timestamps = true;
}
