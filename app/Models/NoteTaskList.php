<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoteTaskList extends Model
{
    protected $table = 'notes_task_lists';

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'slug',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function notesTasks(): HasMany
    {
        return $this->hasMany(NoteTask::class, 'list_id');
    }
}
