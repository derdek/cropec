<?php

namespace App\Http\Controllers;

use App\Models\DayoffType;
use Illuminate\Http\Request;

class DayoffTypeController extends Controller
{
    public function getDayoffTypes()
    {
        return view('dayofftypes', [
            'dayoffTypes' => DayoffType::all()
        ]);
    }

    public function getDayoffTypePage($dayoffTypeId)
    {
        return view('dayofftypes.edit', [
            'dayoffType' => DayoffType::find($dayoffTypeId)
        ]);
    }

    public function deleteDayoffType($dayoffTypeId)
    {
        $dayoffType = DayoffType::where(['id' => $dayoffTypeId, ['id', '<>', 1]])->first();
        if ($dayoffType) {
            $dayoffType->delete();
        }
        return redirect()->route('dayofftypes');
    }

    public function updateDayoffType(Request $request, $dayoffTypeId)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $dayoffType = DayoffType::find($dayoffTypeId);
        $dayoffType->name = $request->name;
        $dayoffType->description = $request->description;
        $dayoffType->default_days_per_year = $request->default_days_per_year;
        $dayoffType->color = $request->color;
        $dayoffType->save();

        return redirect()->route('dayofftypes');
    }

    public function createDayoffType(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'default_days_per_year' => 'nullable|numeric',
            'color' => 'required|string',
        ]);

        $dayoffType = new DayoffType([
            'name' => $request->name,
            'description' => $request->description,
            'default_days_per_year' => $request->default_days_per_year,
            'color' => $request->color,
        ]);
        $dayoffType->save();

        return redirect()->route('dayofftypes');
    }

    public function getDayoffTypeCreatePage()
    {
        return view('dayofftypes.create');
    }
}
