<?php

namespace App\Filament\App\Resources\Timesheets\Pages;

use App\Filament\App\Resources\Timesheets\TimesheetResource;
use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;


    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        
        if($lastTimesheet == null) {
            return [
                Action::make('inwork')
                    ->label('In Work')
                    ->color('success')
                    ->icon(Heroicon::OutlinedClock)
                    ->requiresConfirmation()
                    ->action(function () {
                        $user = Auth::user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->status = 'work';
                        $timesheet->day_in = now();
                        $timesheet->save();

                        Notification::make()
                            ->title('You are now in work')
                            ->success()
                            ->send();
                        
                    }),
                CreateAction::make(),
            ];
        }

        return [
            Action::make('inwork')
                ->label('In Work')
                ->color('warning')
                ->visible(!$lastTimesheet->day_out == null)
                ->disabled($lastTimesheet->day_out == null)
                ->icon(Heroicon::OutlinedClock)
                ->requiresConfirmation()
                ->action(function () {
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->status = 'work';
                    $timesheet->day_in = now();
                    $timesheet->save();

                    Notification::make()
                            ->title('You are now in work')
                            ->color('success')
                            ->success()
                            ->send();
                }),
            Action::make('stopwork')
                ->label('Stop Work')
                ->color('warning')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->status != 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->icon(Heroicon::OutlinedClock)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet) {
                    $lastTimesheet->day_out = now();
                    $lastTimesheet->save();

                    Notification::make()
                            ->title('You are now stopped working')
                            ->color('success')
                            ->success()
                            ->send();
                }),
            Action::make('inpause')
                ->label('In Pause')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->status != 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->icon(Heroicon::OutlinedClock)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet) {
                    $lastTimesheet->day_out = now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->status = 'pause';
                    $timesheet->day_in = now();
                    $timesheet->save();

                    Notification::make()
                            ->title('You are now in pause')
                            ->color('info')
                            ->success()
                            ->send();
                }),
            Action::make('stoppause')
                ->label('Stop Pause')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->status == 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->icon(Heroicon::OutlinedClock)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet) {
                    $lastTimesheet->day_out = now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->status = 'work';
                    $timesheet->day_in = now();
                    $timesheet->save();

                    Notification::make()
                            ->title('You are now in work')
                            ->color('info')
                            ->success()
                            ->send();
                }),
            CreateAction::make(),
        ];
    }
}
