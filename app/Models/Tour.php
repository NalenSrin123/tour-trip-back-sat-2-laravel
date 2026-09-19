<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    protected $primaryKey = 'tour_id';

    protected $fillable = [
        'title',
        'description',
        'price',
        'duration_days',
        'max_participants',
        'status',
        'category_id',
        'destination_id',
    ];

    protected $attributes = [
        'price' => 0,
        'duration_days' => 1,
        'status' => 'active',
    ];


    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id', 'tour_id');
    }
}