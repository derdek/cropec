<?php

namespace App\Observers;

use App\Models\DayoffType;
use App\Models\User;
use App\Models\UserDayoff;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $dayOffTypes = DayoffType::whereNotNull('default_days_per_year')->get();
        foreach ($dayOffTypes as $dayOffType) {
            $userDayoff = new UserDayoff();
            $userDayoff->user_id = $user->id;
            $userDayoff->dayoff_type_id = $dayOffType->id;
            $userDayoff->remaining_days = $dayOffType->default_days_per_year;
            $userDayoff->save();
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
