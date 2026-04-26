<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsUsersFields extends Model
{
    public $timestamps = false;

    protected $table = 'ss_users_fields';

    protected $primaryKey = 'ufid';

    protected $fillable = [
        'userid',
        'typeid',
        'field',
        'value',
    ];
}
