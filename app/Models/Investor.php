<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    protected $table = 'investors';

    protected $fillable = [
        'advisor_code',
        'entity_unique_id',
        'client_number',
        'investor_name',
        'market_value',
    ];

    public $timestamps = true;
}
