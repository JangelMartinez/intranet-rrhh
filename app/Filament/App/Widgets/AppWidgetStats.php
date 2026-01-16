<?php

namespace App\Filament\App\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppWidgetStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {

        return [
            //
            Stat::make('Pending Holidays', $this->getPendingHolidays(Auth::user())),
            Stat::make('Approved Holidays', $this->getApprovedHolidays(Auth::user())),
            Stat::make('Total work', $this->getTotalWork(Auth::user())),
        ];
    }

    protected function getPendingHolidays(User $user): int
    {
        return Holiday::where('user_id', $user->id)
            ->where('status', 'pending')->count();
    }

    protected function getApprovedHolidays(User $user): int
    {
        return Holiday::where('user_id', $user->id)
            ->where('status', 'approved')->count();
    }

    protected function getTotalWork(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('status', 'work')->get();

        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $endTime = Carbon::parse($timesheet->day_out);

            $duration = $startTime->diffInSeconds($endTime);
            //dd($duration);
            $sumSeconds += $duration;
            //dd($sumSeconds);
        }

        $timeformat = gmdate("H:i:s", $sumSeconds);
        return $timeformat;
    }
}
