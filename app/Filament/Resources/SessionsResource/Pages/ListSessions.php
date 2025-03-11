<?php

namespace App\Filament\Resources\SessionsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\StatDashboardNew;
use App\Filament\Resources\SessionsResource;

class ListSessions extends ListRecords
{
    protected static string $resource = SessionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            // StatDashboardNew::class, // عرض الإحصائيات في الـ Header
        ];
    }
}
