<?php

namespace App\Http\Controllers;

use App\Models\PublicHoliday;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
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

    public function getPublicHolidays()
    {
        return view('publicholidays', [
            'publicHolidays' => PublicHoliday::where('date', '>=', now()->startOfYear())
                ->where('date', '<=', now()->endOfYear())
                ->get()
        ]);
    }
}
