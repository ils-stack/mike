<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceFileImportColumn extends Model
{
    use HasFactory;

    protected $table = 'source_file_import_columns';

    protected $fillable = [
        'batch_id',
        'column_index',
        'original_name',
        'display_name',
        'ignored',
        'mapped_field',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'column_index' => 'integer',
        'ignored' => 'boolean',
    ];
}
