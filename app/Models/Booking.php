<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'schedule_id',
        'booking_code',
        'status',
        'total_amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class, 'schedule_id', 'schedule_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class, 'booking_id', 'booking_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BookingHistory::class, 'booking_id', 'booking_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookingReview::class, 'booking_id', 'booking_id');
    }
}
