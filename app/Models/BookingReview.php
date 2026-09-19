<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingReview extends Model
{
    protected $table = 'booking_reviews';
    protected $primaryKey = 'booking_review_id';

    protected $fillable = [
        'booking_id',
        'rating',
        'review_text',
    ];

    public $timestamps = true;

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
