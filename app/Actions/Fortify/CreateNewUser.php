<?php

namespace App\Actions\Fortify;

use App\Models\DayoffType;
use App\Models\User;
use App\Models\UserDayoff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $dayOffTypes = DayoffType::whereNotNull('default_days_per_year')->get();
        foreach ($dayOffTypes as $dayOffType) {
            $userDayoff = new UserDayoff();
            $userDayoff->user_id = $user->id;
            $userDayoff->dayoff_type_id = $dayOffType->id;
            $userDayoff->remaining_days = $dayOffType->default_days_per_year;
            $userDayoff->save();
        }

        return $user;
    }
}
