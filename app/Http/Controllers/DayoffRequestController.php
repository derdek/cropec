<?php

namespace App\Http\Controllers;

use App\Models\DayoffRequest;
use App\Models\DayoffType;
use App\Models\PublicHoliday;
use App\Models\UserDayoff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DayoffRequestController extends Controller
{
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
        if ($userDayoff && !is_null($userDayoff->remaining_days) && $userDayoff->remaining_days < $dayoffCost) {
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

        if ($userDayoff) {
            if (!is_null($userDayoff->remaining_days)) {
                $userDayoff->remaining_days -= $dayoffCost;
            }
            $userDayoff->save();
        }

        return redirect()->route('dayoff-requests');
    }

    public function getDayoffRequests()
    {
        return view('dayoffrequests.dayoffrequests', [
            'dayoffRequests' => DayoffRequest::where('user_id', auth()->id())->with('dayoffType')->get()
        ]);
    }
}
