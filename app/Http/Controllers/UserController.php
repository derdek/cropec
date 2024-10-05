<?php

namespace App\Http\Controllers;

use App\Models\DayoffRequest;
use App\Models\DayoffType;
use App\Models\PublicHoliday;
use App\Models\User;
use App\Models\UserDayoff;
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
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'comment' => 'nullable|string',
        ]);

        $dayoffRequest = new DayoffRequest();
        $dayoffRequest->user_id = auth()->id();
        $dayoffRequest->dayoff_type_id = $request->dayoff_type_id;
        $dayoffRequest->date_from = $request->start_date;
        $dayoffRequest->date_to = $request->end_date;
        $dayoffRequest->comment = $request->comment;
        $dayoffRequest->status = 'pending';
        $dayoffRequest->save();

        return redirect()->route('dayoff-requests');
    }

    public function getDashboard()
    {
        return view('dashboard', [
            'users' => User::paginate(20),
            'userRole' => auth()->user()->role,
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
}
