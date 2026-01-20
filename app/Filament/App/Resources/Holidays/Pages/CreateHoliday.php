<?php

namespace App\Filament\App\Resources\Holidays\Pages;

use App\Filament\App\Resources\Holidays\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

// Solo para preparar los datos del modelo
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        $data['status'] = 'pending';

        return $data;
    }

    // Se ejecuta después de que el registro se guarda con éxito
    protected function afterCreate(): void
    {
        \Illuminate\Support\Facades\Log::info('Entrando en afterCreate');
        $holiday = $this->record; // Aquí tienes acceso al registro ya creado
        \Illuminate\Support\Facades\Log::info('holiday: ' . $holiday);
        $user = Filament::auth()->user();
        if (!$user) {
            \Illuminate\Support\Facades\Log::error('No se encontró usuario autenticado');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Usuario encontrado: ' . $user->email);
        // 1. Notificación a la base de datos para el usuario que crea la solicitud
        Notification::make()
            ->title('Solicitud de vacaciones pendiente')
            ->body("La solicitud para el día {$holiday->day} ha sido enviada correctamente.")
            ->success()
            //->send()
            ->sendToDatabase($user);
        //dd("llega hasta aqui");
        // 2. Envío de Email al Admin
        $userAdmin = User::find(1);
        \Illuminate\Support\Facades\Log::info('Usuario Admin: ' . $userAdmin->email);
        if ($userAdmin) {
           // Mail::to($userAdmin->email)->send(new HolidayPending($holiday));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
