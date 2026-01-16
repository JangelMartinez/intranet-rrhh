<?php

namespace App\Filament\App\Resources\Timesheets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TimesheetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('calendar_id')
                    ->relationship('calendar', 'name')
                    ->required(),
                /*Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),*/
                Select::make('status')
                    ->options(['work' => 'Work', 'pause' => 'Pause'])
                    ->required()
                    ->default('work'),
                DateTimePicker::make('day_in')
                    ->required(),
                DateTimePicker::make('day_out')
                    ->required(),
            ]);
    }
}
