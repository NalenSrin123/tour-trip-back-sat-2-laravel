<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * 
     */
    protected $fillable = [
        'booking_id',
        'request_reason',
        'status',
        'requested_at',
        'responded_at',
        'admin_note',
    ];


    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * 
     */
   

    /**
     * Get the attributes that should be cast.
     *
     * 
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class, 'cancel_request_id', 'cancel_request_id');
    }
    
}
