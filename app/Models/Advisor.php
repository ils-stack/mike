<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    protected $table = 'advisors';

    protected $fillable = [
        'advisor_code',
        'advisor_name',
        'advisor_entity_unique_id',
        'brokerage_code',
        'brokerage_name',
        'brokerage_entity_unique_id',
    ];

    public $timestamps = true;
}
