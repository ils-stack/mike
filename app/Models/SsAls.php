<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsAls extends Model
{
    public $timestamps = false;

    protected $table = 'ss_als';

    protected $primaryKey = 'alid';
}
