<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceFileImportBatch extends Model
{
    use HasFactory;

    protected $table = 'source_file_import_batches';

    protected $fillable = [
        'source_file_metadata_id',
        'status',
        'imported_at',
        'row_count',
        'column_count',
    ];

    protected $casts = [
        'source_file_metadata_id' => 'integer',
        'row_count' => 'integer',
        'column_count' => 'integer',
        'imported_at' => 'datetime',
    ];
}
