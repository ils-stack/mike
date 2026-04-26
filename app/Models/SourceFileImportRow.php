<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceFileImportRow extends Model
{
    use HasFactory;

    protected $table = 'source_file_import_rows';

    protected $fillable = [
        'batch_id',
        'csv_row_number',
        'row_data',
        'deleted',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'csv_row_number' => 'integer',
        'row_data' => 'array',
        'deleted' => 'boolean',
    ];
}
