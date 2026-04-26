<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceFileMetadata extends Model
{
    use HasFactory;

    protected $table = 'source_file_metadata';

    protected $fillable = [
        'name',
        'path',
        'path_hash',
        'provider_id',
        'month',
        'year',
        'processed_status',
        'deleted',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'provider_id' => 'integer',
        'processed_status' => 'integer',
        'deleted' => 'boolean',
    ];
}
