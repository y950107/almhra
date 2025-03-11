<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentIcon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']) // also accepts a closure
                ->visible(outsidePanels: true);
                /* ->outsidePanelRoutes([
                    'profile',
                    'home',
                    
                ]); */
                /* ->flags([
                    'ar' => asset('flags/saudi-arabia.svg'),
                    'fr' => asset('flags/france.svg'),
                    'en' => asset('flags/usa.svg'),
                ]); */
        });
        


      
    }
}
