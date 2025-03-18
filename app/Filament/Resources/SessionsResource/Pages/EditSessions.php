<?php

namespace App\Filament\Resources\SessionsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SessionsResource;
use App\Filament\Resources\SessionsResource\Widgets\DashboardStats;

class EditSessions extends EditRecord
{
    protected static string $resource = SessionsResource::class;

    
    protected function getHeaderActions(): array
    {

        
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
