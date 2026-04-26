<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';

    protected $fillable = [
        'unique_id',
        'investor_entity_id',
        'advisor_code',
        'contract_number',
        'account_number',
        'account_description',
        'market_value',
        'irr',
    ];

    public $timestamps = true;
}
