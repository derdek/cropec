<?php

namespace App\Http\Controllers;

use App\Models\DayoffRequest;
use App\Models\DayoffType;
use App\Models\PublicHoliday;
use App\Models\User;
use App\Models\UserDayoff;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getDashboard()
    {
        $calendar = $this->getCalendar();
        $curMonth = $calendar['month'];
        while ($curMonth > 12){
            $curMonth -= 12;
        }
        $today = Carbon::now();
        $holidays = PublicHoliday::where('date', '>=', Carbon::create($calendar['year'], $curMonth)->subMonth()->startOfMonth())
            ->where('date', '<=', Carbon::create($calendar['year'], $curMonth)->addMonth()->endOfMonth())
            ->get();
        $holidaysArray = [];
        foreach ($holidays as $holiday) {
            $holidaysArray[$holiday->date] = $holiday->name;
        }
        $dayoffTypes = DayoffType::all();
        $dayoffTypeColors = [];
        foreach ($dayoffTypes as $dayoffType) {
            $dayoffTypeColors[$dayoffType->id] = $dayoffType->color;
        }
        return view('dashboard', [
            'users' => User::paginate(20),
            'userRole' => auth()->user()->role,
            'roles' => ['admin', 'manager', 'user'],
            'userDayoff' => UserDayoff::where('user_id', auth()->id())->where('dayoff_type_id', 1)->first(),
            'dayoffTypeColors' => $dayoffTypeColors,
            'calendar' => $calendar['calendar'],
            'month' => $calendar['month'],
            'curMonth' => $curMonth,
            'year' => $calendar['year'],
            'dayoffs' => $calendar['approvedDayoffs'],
            'holidays' => $holidaysArray,
            'today' => $today,
        ]);
    }

    private function getCalendar()
    {
        $month = request()->query('month', Carbon::now()->month);
        $year = request()->query('year', Carbon::now()->year);
        while ($month > 12) {
            $month -= 12;
            $year++;
        }
        while ($month < 1) {
            $month += 12;
            $year--;
        }

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $firstDayOfMonth = Carbon::create($year, $month)->startOfMonth()->dayOfWeekIso; // Start from Monday

        $approvedDayoffsDB = DayoffRequest::where('status', 'approved')
            ->where('user_id', auth()->id())
            ->where('date_from', '>=', Carbon::create($year, $month)->startOfYear()->startOfMonth())
            ->where('date_to', '<=', Carbon::create($year, $month)->endOfYear()->endOfMonth())
            ->get();

        $approvedDayoffs = [];
        foreach ($approvedDayoffsDB as $dayoff) {
            $dateFrom = Carbon::create($dayoff->date_from);
            $dateTo = Carbon::create($dayoff->date_to);
            while ($dateFrom->lte($dateTo)) {
                $approvedDayoffs[$dateFrom->toDateString()] = $dayoff->dayoff_type_id;
                $dateFrom->addDay();
            }
        }

        $calendar = [];
        $week = [];

        // Previous month days
        $prevMonth = Carbon::create($year, $month)->subMonth();
        $daysInPrevMonth = $prevMonth->daysInMonth;
        for ($i = $firstDayOfMonth - 1; $i > 0; $i--) {
            $week[] = $prevMonth->copy()->addDays($daysInPrevMonth - $i);
        }


        // Current month days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $week[] = Carbon::create($year, $month, $day);

            if (count($week) === 7) {
                $calendar[] = $week;
                $week = [];
            }
        }


        // Next month days
        $nextMonth = Carbon::create($year, $month)->addMonth();
        for ($i = 1; count($week) < 7; $i++) {
            $week[] = $nextMonth->copy()->startOfMonth()->addDays($i - 1);
        }

        if (count($week) > 0) {
            $calendar[] = $week;
        }

        return [
            'calendar' => $calendar,
            'month' => $month,
            'year' => $year,
            'approvedDayoffs' => $approvedDayoffs,
        ];
    }
}
