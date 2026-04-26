<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brochure extends Model
{
    protected $table = 'brochures';

    protected $fillable = [
        'title',
        'user_id',
        'file_path',
    ];

    public function items()
    {
        return $this->hasMany(BrochureItem::class, 'brochure_id', 'id')
                    ->orderBy('sort_order', 'ASC');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
