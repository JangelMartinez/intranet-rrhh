<?php

namespace App\Filament\App\Resources\Timesheets\Pages;

use App\Filament\App\Resources\Timesheets\TimesheetResource;
use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inwork')
                ->label('In Work')
                ->color('warning')
                ->icon(Heroicon::OutlinedClock)
                ->requiresConfirmation()
                ->action(function () {
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->status = 'work';
                    $timesheet->day_in = now();
                    $timesheet->day_out = now();
                    $timesheet->save();
                }),
            Action::make('inpause')
                ->label('In Pause')
                ->color('info')
                ->icon(Heroicon::OutlinedClock)
                ->requiresConfirmation(),
            CreateAction::make(),
        ];
    }
}
