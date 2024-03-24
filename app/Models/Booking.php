<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'place_id',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at' => 'date:Y-m-d',
        'end_at' => 'date:Y-m-d'
    ];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
