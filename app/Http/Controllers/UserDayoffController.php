<?php

namespace App\Http\Controllers;

use App\Models\UserDayoff;
use Illuminate\Http\Request;

class UserDayoffController extends Controller
{
    public function getUserDayoffs()
    {
        return view('userdayoffs', [
            'userDayoffs' => UserDayoff::where('user_id', auth()->id())->with('dayoffType')->get()
        ]);
    }
}
