<?php

namespace App\Http\Controllers;

use App\Models\DayoffRequest;
use App\Models\DayoffType;
use App\Models\PublicHoliday;
use App\Models\UserDayoff;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getDayoffTypes()
    {
        return $this->prepareDayoffTypesResponse(DayoffType::all());
    }

    private function prepareDayoffTypesResponse($array)
    {
        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th>Dayoff Type Name</th>';
        $html .= '<th>Description</th>';
        $html .= '<th>Days per year</th>';
        $html .= '<th>Color</th>';
        $html .= '</tr>';

        foreach ($array as $record) {
            $days = $record['default_days_per_year']
                ?: 'unlimited';
            $html .= '<tr>';
            $html .= '<td>' . $record['name'] . '</td>';
            $html .= '<td>' . $record['description'] . '</td>';
            $html .= '<td>' . $days . '</td>';
            $html .= '<td style="background-color:' . $record['color'] . '">' . $record['color'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public function getPublicHolidays()
    {
        return $this->preparePublicHolidaysResponse(PublicHoliday::all());
    }

    private function preparePublicHolidaysResponse($array)
    {
        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th>Public Holiday Name</th>';
        $html .= '<th>Date</th>';
        $html .= '</tr>';

        foreach ($array as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record['name'] . '</td>';
            $html .= '<td>' . $record['date'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public function getDayoffRequests()
    {
        return $this->prepareDayoffRequestsResponse(
            DayoffRequest::where('user_id', auth()->id())->get()
        );
    }

    public function getManagedDayoffRequests()
    {
        if (!auth()->user()->isManager()) {
            return 'You are not authorized to view this page.';
        }
        return $this->prepareDayoffRequestsResponse(DayoffRequest::all());
    }

    private function prepareDayoffRequestsResponse($array)
    {
        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th>Dayoff Request ID</th>';
        $html .= '<th>Dayoff Type</th>';
        $html .= '<th>Start Date</th>';
        $html .= '<th>End Date</th>';
        $html .= '<th>Status</th>';
        $html .= '</tr>';

        foreach ($array as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record['id'] . '</td>';
            $html .= '<td>' . $record['dayoff_type_id'] . '</td>';
            $html .= '<td>' . $record['start_date'] . '</td>';
            $html .= '<td>' . $record['end_date'] . '</td>';
            $html .= '<td>' . $record['status'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public function getUserDayoffs()
    {
        return $this->prepareUserDayoffsResponse(
            UserDayoff::where('user_id', auth()->id())
                ->with('dayoffType')
                ->get()
        );
    }

    private function prepareUserDayoffsResponse($array)
    {
        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th>Dayoff Type</th>';
        $html .= '<th>Days Remaining</th>';
        $html .= '</tr>';

        foreach ($array as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->dayoffType->name . '</td>';
            $html .= '<td>' . $record->remaining_days . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
