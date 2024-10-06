<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Це many to one зв'язок між користувачем та неробочими днями
 * У користувача може бути декілька видів не робочих днів
 */

/**
 * @mi
 */
class UserDayoff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dayoff_type_id',
        'remaining_days',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dayoffType()
    {
        return $this->belongsTo(DayoffType::class);
    }
}
