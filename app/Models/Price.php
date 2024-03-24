<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'start_at',
        'end_at',
        'is_weekend',
        'is_default',
        'price'
    ];

    protected $casts = [
        'start_at' => 'date:Y-m-d',
        'end_at' => 'date:Y-m-d'
    ];
}
