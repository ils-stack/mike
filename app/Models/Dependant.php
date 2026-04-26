<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependant extends Model
{
    protected $table = 'dependants';

    protected $fillable = [
        'investor_id',
        'client_number',
        'first_name',
        'surname',
        'relationship',
        'id_number',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'notes',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
