<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour_Image extends Model
{
    use HasFactory;
    protected $table = 'tour_images';
    protected $primaryKey = 'image_id';
    protected $fillable = [
        'tour_id',
        'image_url',
    ];
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
