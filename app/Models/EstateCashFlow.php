<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstateCashFlow extends Model
{
    public $timestamps = false;

    protected $table = 'tbl_estate_assessment_cash_flow';

    protected $primaryKey = 'id';
}
