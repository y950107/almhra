<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\BasePage;
use Filament\Pages\Dashboard as FDashboard;

class CustomDashboard extends BasePage
{

     /**
     * @var view-string
     */

    protected static string $view = 'filament-panels::pages.dashboard';
    protected static ?string $navigationIcon = 'icon-halaka';
    public static function getRoutePath(): string
    {
        return static::$routePath;
    }
}