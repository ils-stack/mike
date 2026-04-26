<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyZoning extends Model
{
    protected $table = 'property_zoning';

    protected $fillable = [
        'zoning',
    ];

    public $timestamps = true;
}
