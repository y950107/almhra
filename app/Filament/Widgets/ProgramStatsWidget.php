<?php

namespace App\Filament\Widgets;

use App\Models\AlMaherRecitation;
use App\Models\AlMaqraaRecitation;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class ProgramStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.program-stats-widget';

    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';



}
