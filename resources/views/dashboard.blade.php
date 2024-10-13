<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>

        <a href="{{ route('user-create') }}" class="mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Create User</a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
{{--                divide a page by 2 sections, left section has 65% width, right section has 35%. Write a tailwindcss code to create this --}}
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 leading-tight">Users</h2>
                        <table class="min-w-full divide-y divide-gray-200 mt-6">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Dayoff type</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">dates</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div class="flex items center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm leading-5 font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm leading-5 text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                        <a href="{{ route('user-edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 leading-tight">Remaining vacation days:</h2>
                        <h2 class="text-2xl font-semibold text-gray-800 leading-tight">{{ $userDayoff->remaining_days }}</h2>
                        <div class="min-w-full divide-y divide-gray-200 mt-6">
                            <div class="container mx-auto px-4 py-8">
                                <div class="flex justify-between items-center mb-4">
                                    <h1 class="text-2xl font-bold">{{ Carbon\Carbon::create($year, $month)->format('F Y') }}</h1>
                                    <div class="flex space-x-2">
                                        @php
                                            $prevYear = $year;
                                            $prevMonth = $month - 1;
                                            if ($prevMonth < 1) {
                                                $prevMonth = 12;
                                                $prevYear--;
                                            }
                                            $nextYear = $year;
                                            $nextMonth = $month + 1;
                                            if ($nextMonth > 12) {
                                                $nextMonth = 1;
                                                $nextYear++;
                                            }
                                            $current = \Carbon\Carbon::now();
                                        @endphp
                                        <a href="{{ request()->fullUrlWithQuery(['month' => $prevMonth, 'year' => $prevYear]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Previous</a>
                                        <a href="{{ request()->fullUrlWithQuery(['month' => $current->month, 'year' => $current->year]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Current</a>
                                        <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth, 'year' => $nextYear]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Next</a>
                                    </div>
                                </div>
                                <table class="w-full bg-white rounded shadow table-fixed">
                                    <thead>
                                    <tr>
                                        <th class="p-2 border-b text-center aspect-square">Mon</th>
                                        <th class="p-2 border-b text-center aspect-square">Tue</th>
                                        <th class="p-2 border-b text-center aspect-square">Wed</th>
                                        <th class="p-2 border-b text-center aspect-square">Thu</th>
                                        <th class="p-2 border-b text-center aspect-square">Fri</th>
                                        <th class="p-2 border-b text-center aspect-square">Sat</th>
                                        <th class="p-2 border-b text-center aspect-square">Sun</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($calendar as $week)
                                        <tr>
                                            @foreach($week as $day)
                                                <td class="p-2 border-b text-center aspect-square">
                                                    @php
                                                    $todayIsHoliday = in_array($day->toDateString(), array_keys($holidays));
                                                    $backColorClass = '';
                                                    $backColorStyle = '';
                                                    $isDayoff = in_array($day->toDateString(), array_keys($dayoffs));

                                                    if ($isDayoff) {
                                                        $backColorStyle = 'background-color: ' . $dayoffTypeColors[$dayoffs[$day->toDateString()]];
                                                    } else {
                                                        $backColorClass = $todayIsHoliday ? 'bg-yellow-500' : $backColorClass;
                                                        $backColorClass = $day->toDateString() == $today->toDateString()
                                                            ? 'bg-gray-500'
                                                            : $backColorClass;
                                                    }
                                                    @endphp
                                                    @if ($todayIsHoliday)
                                                    <div class="group flex relative">
                                                    @endif
                                                        <div class="{{ $backColorClass }} rounded-full h-6 w-6 mx-auto" style="{{ $backColorStyle }}">
                                                            @php
                                                                $textColor = 'text-black';

                                                                if ($day->month != $curMonth) {
                                                                    if ($day->isSunday() || $day->isSaturday()) {
                                                                        $textColor = 'text-red-200';
                                                                    } else {
                                                                        $textColor = 'text-gray-400';
                                                                    }
                                                                } elseif ($day->isSunday() || $day->isSaturday()) {
                                                                    $textColor = 'text-red-400';
                                                                }
                                                            @endphp
                                                            <span class="{{ $textColor }}">
                                                                {{ $day->format('d') }}
                                                            </span>
                                                        </div>
                                                    @if ($todayIsHoliday)
                                                    <span class="group-hover:opacity-100 transition-opacity bg-gray-800 px-1 text-sm text-gray-100 rounded-md absolute left-1/2-translate-x-1/2 translate-y-full opacity-0 m-4 mx-auto">{{ $holidays[$day->toDateString()] }}</span>
                                                    </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <a
                                    href="{{ route('create-dayoff-request-form') }}"
                                    class="mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >Create Dayoff Request</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
