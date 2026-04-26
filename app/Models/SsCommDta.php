<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsCommDta extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'ss_comm_dta';

    protected $fillable = [
        'broker_id',
        'broker_name',
        'import_type',
        'amt',
        'comm_dt',
        'brokerage',
        'csv_name',
        'dumper_id',
        'percent',
        'active',
        'created_at',
        'updated_at',
    ];
}
