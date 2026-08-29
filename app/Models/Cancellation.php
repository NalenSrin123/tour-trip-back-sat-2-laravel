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
     * @var list<string>
     */
    protected $primaryKey = 'cancellation_id';

    protected $fillable = [
        'cancel_request_id',
        'refund_amount',
        'refund_method',
        'status',
        'processed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $casts = [
        'refund_amount' => 'decimal:2',
        'processed_at'  => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function cancelRequest(): BelongsTo
    {
        return $this->belongsTo(CancelRequest::class, 'cancel_request_id', 'cancel_request_id');
    }
}
