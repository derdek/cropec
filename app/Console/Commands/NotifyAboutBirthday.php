<?php

namespace App\Console\Commands;

use App\Mail\BirthdayNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyAboutBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-about-birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private array $managerEmails;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->managerEmails = User::where('role', 'admin')->pluck('email')->toArray();
        $this->notifyManagersAboutTodayBirthday();
        $this->notifyManagersAboutUpcomingBirthday();
    }

    private function notifyManagersAboutTodayBirthday(): void
    {
        $today = now();
        $users = User::whereMonth('birthday', $today->month)
            ->whereDay('birthday', $today->day)
            ->get();
        foreach ($users as $user) {
            $message = 'Today is the birthday of ' . $user->name . '. Please send him a birthday wish.';
            Mail::to($this->managerEmails)->send(new BirthdayNotification($message));
        }
    }

    private function notifyManagersAboutUpcomingBirthday(): void
    {
        $today = now();
        $users = User::whereMonth('birthday', $today->addMonth()->month)
            ->whereDay('birthday', $today->day)
            ->get();
        foreach ($users as $user) {
            $message = sprintf(
                'In a month, on %s, it will be the birthday of %s. Please send him a birthday wish.',
                $user->birthday->format('Y-m-d'),
                $user->name
            );
            Mail::to($this->managerEmails)->send(new BirthdayNotification($message));
        }
    }
}
