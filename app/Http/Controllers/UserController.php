<?php

namespace App\Http\Controllers;

use App\Models\DayoffRequest;
use App\Models\DayoffType;
use App\Models\PublicHoliday;
use App\Models\User;
use App\Models\UserDayoff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getDayoffTypes()
    {
        return view('dayofftypes', [
            'dayoffTypes' => DayoffType::all()
        ]);
    }

    public function getPublicHolidays()
    {
        return view('publicholidays', [
            'publicHolidays' => PublicHoliday::all()
        ]);
    }

    public function getDayoffRequests()
    {
        return view('dayoffrequests.dayoffrequests', [
            'dayoffRequests' => DayoffRequest::where('user_id', auth()->id())->with('dayoffType')->get()
        ]);
    }

    public function getManagedDayoffRequests()
    {
        if (!auth()->user()->isManager()) {
            return 'You are not authorized to view this page.';
        }
        return view('dayoffrequests', [
            'dayoffRequests' => DayoffRequest::where('status', 'pending')->get()
        ]);
    }

    public function getUserDayoffs()
    {
        return view('userdayoffs', [
            'userDayoffs' => UserDayoff::where('user_id', auth()->id())->with('dayoffType')->get()
        ]);
    }

    public function createDayoffRequestForm()
    {
        return view('dayoffrequests.create',[
            'dayoffTypes' => DayoffType::all()
        ]);
    }

    public function createDayoffRequest(Request $request)
    {
        $request->validate([
            'dayoff_type_id' => 'required|exists:dayoff_types,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'comment' => 'nullable|string',
        ]);

        $publicHolidaysInsideDayoffs = PublicHoliday::where('date', '>=', $request->date_from)
            ->where('date', '<=', $request->date_to)
            ->get();
        $dayoffCost = 0;
        $dateFrom = Carbon::create($request->date_from);
        $dateTo = Carbon::create($request->date_to);
        while ($dateFrom->lte($dateTo)) {
            if (!$dateFrom->isWeekend() && !$publicHolidaysInsideDayoffs->contains('date', $dateFrom->toDateString())) {
                $dayoffCost++;
            }
            $dateFrom->addDay();
        }

        $userDayoff = UserDayoff::where('user_id', auth()->id())->where('dayoff_type_id', $request->dayoff_type_id)->first();
        if ($userDayoff->remaining_days < $dayoffCost) {
            return redirect()->back()->withErrors(['dayoff_type_id' => 'Not enough days left.']);
        }

        $dayoffRequest = new DayoffRequest();
        $dayoffRequest->user_id = auth()->id();
        $dayoffRequest->dayoff_type_id = $request->dayoff_type_id;
        $dayoffRequest->date_from = $request->date_from;
        $dayoffRequest->date_to = $request->date_to;
        $dayoffRequest->comment = $request->comment;
        $dayoffRequest->status = 'approved';
        $dayoffRequest->saveOrFail();

        $userDayoff->remaining_days -= $dayoffCost;
        $userDayoff->save();

        return redirect()->route('dayoff-requests');
    }

    public function getDashboard()
    {
        $calendar = $this->getCalendar();
        $curMonth = $calendar['month'];
        while ($curMonth > 12){
            $curMonth -= 12;
        }
        $holidays = PublicHoliday::where('date', '>=', Carbon::create($calendar['year'], $curMonth)->subMonth()->startOfMonth())
            ->where('date', '<=', Carbon::create($calendar['year'], $curMonth)->addMonth()->endOfMonth())
            ->get();
        $holidaysArray = [];
        foreach ($holidays as $holiday) {
            $holidaysArray[$holiday->date] = $holiday->name;
        }
        return view('dashboard', [
            'users' => User::paginate(20),
            'userRole' => auth()->user()->role,
            'roles' => ['admin', 'manager', 'user'],
            'userDayoff' => UserDayoff::where('user_id', auth()->id())->where('dayoff_type_id', 1)->first(),
            'calendar' => $calendar['calendar'],
            'month' => $calendar['month'],
            'curMonth' => $curMonth,
            'year' => $calendar['year'],
            'dayoffs' => $calendar['approvedDayoffs'],
            'holidays' => $holidaysArray,
        ]);
    }

    public function getUserEditPage($id)
    {
        $user = User::find($id);
        $userDayoffs = UserDayoff::where('user_id', $id)->get();
        return view('user.edit', [
            'user' => $user,
            'userDayoffs' => $userDayoffs,
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|in:admin,manager,user',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        foreach ($request->dayoff as $dayoffTypeId => $days) {
            $userDayoff = UserDayoff::where('user_id', $id)->where('dayoff_type_id', $dayoffTypeId)->first();
            $userDayoff->remaining_days = $days;
            $userDayoff->save();
        }

        return redirect()->route('dashboard');
    }

    public function getCreateUserPage()
    {
        return view('user.create', [
            'dayoffTypes' => DayoffType::whereNotNull('default_days_per_year')->get()
        ]);
    }

    public function createUserWithUserDayoffs(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|in:admin,manager,user',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->password = bcrypt('changeMe123!');
        $user->save();

        foreach ($request->dayoff as $dayoffTypeId => $days) {
            $userDayoff = new UserDayoff();
            $userDayoff->user_id = $user->id;
            $userDayoff->dayoff_type_id = $dayoffTypeId;
            $userDayoff->remaining_days = $days;
            $userDayoff->save();
        }

        return redirect()->route('dashboard');
    }

    public function createPublicHoliday(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
        ]);

        $publicHoliday = new PublicHoliday();
        $publicHoliday->name = $request->name;
        $publicHoliday->date = $request->date;
        $publicHoliday->save();

        return redirect()->route('public-holidays');
    }

    public function getCalendar()
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
            ->where('date_from', '>=', Carbon::create($year, $month)->startOfMonth())
            ->where('date_to', '<=', Carbon::create($year, $month)->endOfMonth())
            ->get();

        // create a list with keys as Carbon dates from date_from and date_to
        $approvedDayoffs = [];
        foreach ($approvedDayoffsDB as $dayoff) {
            $dateFrom = Carbon::create($dayoff->date_from);
            $dateTo = Carbon::create($dayoff->date_to);
            while ($dateFrom->lte($dateTo)) {
                $approvedDayoffs[] = $dateFrom->toDateString();
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
