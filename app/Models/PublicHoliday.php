<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Це свято, яке відзначається в календарі
 * Це може бути вихідний день, або день, коли працюється
 */
class PublicHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
    ];

    public function getFutureHolidays()
    {
        return PublicHoliday::where()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->andWhere('date', '<=', now()->endOfYear()->format('Y-m-d'))
            ->get();
    }
}
