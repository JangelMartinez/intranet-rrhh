<?php

namespace App\Filament\App\Resources\Holidays;

use App\Filament\App\Resources\Holidays\Pages\CreateHoliday;
use App\Filament\App\Resources\Holidays\Pages\EditHoliday;
use App\Filament\App\Resources\Holidays\Pages\ListHolidays;
use App\Filament\App\Resources\Holidays\Schemas\HolidayForm;
use App\Filament\App\Resources\Holidays\Tables\HolidaysTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Holiday;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id);
    }
    
    public static function form(Schema $schema): Schema
    {
        return HolidayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HolidaysTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHolidays::route('/'),
            'create' => CreateHoliday::route('/create'),
            'edit' => EditHoliday::route('/{record}/edit'),
        ];
    }
}
