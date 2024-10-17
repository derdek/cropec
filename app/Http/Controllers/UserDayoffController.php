<?php

namespace App\Http\Controllers;

use App\Models\UserDayoff;

class UserDayoffController extends Controller
{
    public function getUserDayoffs()
    {
        return view('userdayoffs', [
            'userDayoffs' => UserDayoff::where('user_id', auth()->id())->with('dayoffType')->get()
        ]);
    }
}
