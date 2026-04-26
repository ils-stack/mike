<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitProperty extends Model
{
    protected $table = 'unit_property';
    protected $fillable = ['unit_id', 'property_id'];
}
