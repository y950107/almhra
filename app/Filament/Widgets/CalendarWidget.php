<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\RecitationSession;
use App\Filament\Resources\RecitationSessionResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort =2;
   // protected static string $view = 'filament.widgets.calendar-widget';
   public function fetchEvents(array $fetchInfo): array
    {
        return RecitationSession::query()
            ->where('created_at', '>=', $fetchInfo['start'])
            ->where('session_date', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (RecitationSession $event) => [
                    'title' => $event->halaka->name,
                    'start' => $event->created_at,
                    'end' => $event->session_date   ,
                    'color'=> '',
                    'url' => RecitationSessionResource::getUrl(name: 'edit', parameters: ['record' => $event->id]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }
}
