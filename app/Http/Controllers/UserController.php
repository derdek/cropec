<?php

namespace App\Http\Controllers;

use App\Models\DayoffType;
use App\Models\User;
use App\Models\UserDayoff;
use Illuminate\Http\Request;

class UserController extends Controller
{


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
}
