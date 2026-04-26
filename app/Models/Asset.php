<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Asset extends Model
{
    protected $fillable = [
        'user_id', 'folder', 'file_name', 'file_type', 'file_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
