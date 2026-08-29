<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour_Image extends Model
{
    use HasFactory;
    protected $table = 'tour_images';
    protected $primaryKey = 'image_id';
    protected $fillable = [
        'tour_id',
        'image_url',
    ];
    public function tour(){
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
