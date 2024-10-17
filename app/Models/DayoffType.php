<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Це тип не робочого дня
 * Це може бути відпустка, вихідний за власний рахунок, лікарняний, конференція, поїздка
 */
class DayoffType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'default_days_per_year',
    ];
}
