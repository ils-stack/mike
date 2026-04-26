<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incomes extends Model
{
    public $timestamps = false;

    protected $table = 'ss_incomes';

    protected $primaryKey = 'incomeid';
}
