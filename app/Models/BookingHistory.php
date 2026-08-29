<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model
{
    protected $table = 'booking_histories';
    protected $primaryKey = 'history_id';

    protected $fillable = [
        'booking_id',
        'action',
        'action_at',
        'note',
    ];

    public $timestamps = true;

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
