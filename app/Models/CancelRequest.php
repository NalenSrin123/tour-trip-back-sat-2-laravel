<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CancelRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'cancel_request_id';

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

  
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

   
    public function cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class, 'cancel_request_id', 'cancel_request_id');
    }
}