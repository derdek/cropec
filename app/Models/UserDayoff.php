<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Це many to one зв'язок між користувачем та неробочими днями
 * У користувача може бути декілька видів не робочих днів
 */
class UserDayoff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dayoff_type_id',
        'remaining_days',
    ];
}
