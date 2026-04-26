<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDetail extends Model
{
    protected $table = 'client_details';

    protected $fillable = [
        'investor_id',
        'client_number',
        'marital_status',
        'entity',
        'surname',
        'first_name',
        'id_number',
        'tax_number',
        'email',
        'date_of_birth',
        'physical_address',
        'postal_address',
        'cellular',
        'home_tel',
        'work_tel',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }
}
