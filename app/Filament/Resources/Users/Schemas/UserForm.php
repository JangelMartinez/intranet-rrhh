<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\City;
use App\Models\State;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
//use Filament\Notifications\Collection;
use Illuminate\Support\Collection;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Information')
                    //->description('Prevent abuse by limiting the number of requests per period')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        // ...
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        //DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->password()
                            ->hiddenOn('edit')
                            ->required(),
                    ]),

                Section::make('Address Information')
                    //->description('Prevent abuse by limiting the number of requests per period')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        // ...
                        TextInput::make('address')
                            ->required(),
                        Select::make('country_id')->relationship('country', 'name')
                            ->label('Country')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            } )
                            ->required(),
                        Select::make('state_id')
                            ->label('State')
                            //->relationship('state', 'name')
                            ->options(fn (Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                            ->required(),
                        Select::make('city_id')
                            ->label('City')
                            //->relationship('city', 'name')
                            ->options(fn (Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            //->live()
                            ->required(),
                        TextInput::make('zip_code')
                            ->label('Zip Code')
                            ->required(),
                    ]),
                
            ]);
    }
}
