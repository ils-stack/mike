<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    public $timestamps = false;

    protected $table = 'ss_expenses';

    protected $primaryKey = 'expenseid';
}
