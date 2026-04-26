<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'asset_id', 'module_type', 'module_id'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function module()
    {
        return $this->morphTo(null, 'module_type', 'module_id');
    }
}
