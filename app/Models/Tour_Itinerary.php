<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour_Itinerary extends Model
{
    use HasFactory;
     protected $table = 'tour_itineraries';
     protected $primaryKey = 'itinerary_id';
     protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
        'sort_order',
     ];
     public function tour(){
        return $this->belongsTo(Tour::class, 'tour_id');
     }
}
