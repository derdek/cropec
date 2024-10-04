<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Це запит на не робочий день
 */
class DayoffRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'dayoff_type_id',
        'user_id',
        'date_from',
        'date_to',
        'status',
        'comment',
        'answered_at',
        'answered_by',
        'answered_comment',
    ];

    public function getDayoffType()
    {
        return $this->belongsTo(DayoffType::class);
    }

    public function getUser()
    {
        return $this->belongsTo(User::class);
    }

    public function getAnsweredBy()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function removeFromPeriodPublicHolidaysAndWeekends($dateFrom, $dateTo)
    {
        $period = new \DatePeriod(
            new \DateTime($dateFrom),
            new \DateInterval('P1D'),
            new \DateTime($dateTo)
        );

        $result = [];
        foreach ($period as $date) {
            $dayOfWeek = $date->format('N');
            if ($dayOfWeek < 6) {
                $result[] = $date->format('Y-m-d');
            }
        }

        $publicHolidays = PublicHoliday::whereIn('date', $result)->get();
        $publicHolidays = $publicHolidays->pluck('date')->toArray();

        $result = array_diff($result, $publicHolidays);

        return $result;
    }
}
