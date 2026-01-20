<?php

namespace App\Filament\Resources\Holidays\Pages;

use App\Filament\Resources\Holidays\HolidayResource;
use App\Mail\HolidayApproved;
use App\Mail\HolidayDecline;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        $user = User::find($record->user_id);
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day
            );

        $title = '';
        $message = '';
        // SEND EMAIL ONLY IF APPROVED
        if($record->status == 'approved'){
            //Mail::to($user)->send(new HolidayApproved($data));
            $title = 'Solicitud de vacaciones aprobada';
            $message = "La solicitud para el dia {$data['day']} ha sido aprovado.";

        }

        // SEND EMAIL ONLY IF DECLINED
        if($record->status == 'declined'){
            //Mail::to($user)->send(new HolidayDecline($data));
            $title = 'Solicitud de vacaciones rechazada';
            $message = "La solicitud para el dia {$data['day']} ha sido rechazada.";
        }

        Notification::make()
            ->title($title)
            ->body($message)
            ->success()
            //->send()
            ->sendToDatabase($user);

        return $record;
    }
}
