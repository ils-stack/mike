<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceAltName extends Model
{
    use HasFactory;

    protected $table = 'source_alt_names';

    protected $fillable = [
        'alt_name',
        'broker_name',
    ];
}
