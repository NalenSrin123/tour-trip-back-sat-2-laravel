<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    use HasFactory;

    protected $table = 'cancellations';
    protected $primaryKey = 'cancellation_id';

    protected $fillable = [
        'cancel_request_id',
        'refund_amount',
        'refund_method',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function cancelRequest(): BelongsTo
    {
        return $this->belongsTo(CancelRequest::class, 'cancel_request_id', 'cancel_request_id');
    }
}
