<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewManagement extends Model
{
    protected $table = 'review_management';
    protected $primaryKey = 'review_mgmt_id';
    public $timestamps = false;

    protected $fillable = [
        'review_type',
        'review_id', 
        'status',
        'actioned_by',
        'actioned_at',
        'note'
    ];
}
