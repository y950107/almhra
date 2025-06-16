<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AlMaherRecitationResource;
use App\Filament\Resources\AlMaqraaRecitationResource;
use App\Filament\Resources\AlMutqinRecitationResource;
use App\Models\RecitationSession;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 4;
   // protected static string $view = 'filament.widgets.calendar-widget';

  /* public static function canView(): bool
   {
       return auth()->user()?->hasPermissionTo('widget_CalendarWidget');
   }*/
    public function fetchEvents(array $fetchInfo): array
    {

        return RecitationSession::with(['halaka', 'almaqraaRecitation', 'almutqinRecitation', 'almaherRecitation'])
            ->where('session_date', '>=', $fetchInfo['start'])
            ->where('session_date', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (RecitationSession $event) {
                // Determine which recitation type it has

                if ($event->almaqraaRecitation) {
                    $resource = AlMaqraaRecitationResource::getUrl(name: 'edit', parameters: ['record' => $event->almaqraaRecitation->id]);
                    $color = '#28a745'; // green
                } elseif ($event->almutqinRecitation) {
                    $resource = AlMutqinRecitationResource::getUrl(name: 'edit', parameters: ['record' => $event->almutqinRecitation->id]);
                    $color = '#ffc107'; // yellow
                } elseif ($event->almaherRecitation) {
                    $resource = AlMaherRecitationResource::getUrl(name: 'edit', parameters: ['record' => $event->almaherRecitation->id]);
                    $color = '#007bff'; // blue
                }
                else return [];

                return [
                    'title' => $event->halaka->name,
                    'start' => $event->session_date,
                    'end' => $event->session_date,
                    'color' => $color,
                    'url' => $resource,
                    'shouldOpenUrlInNewTab' => true,
                ];
            })
            ->all();
    }

}
