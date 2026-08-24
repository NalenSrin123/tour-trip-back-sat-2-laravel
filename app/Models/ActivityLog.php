<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'action',
        'entity_type',
        'entity_id',
        'ip_address',
    ];
}
