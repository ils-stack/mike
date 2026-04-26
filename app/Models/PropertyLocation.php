<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyLocation extends Model
{
    protected $table = 'property_location';

    protected $fillable = [
        'location',
    ];

    public $timestamps = true;
}
