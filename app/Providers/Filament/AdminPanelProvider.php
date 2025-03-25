<?php

namespace App\Providers\Filament;



use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use App\Filament\Pages\UserProfile;
use Filament\Forms\Components\Grid;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\DashboardStats;
use Filament\Navigation\NavigationGroup;
use App\Filament\Widgets\StatDashboardNew;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\SettingsResource\Pages\GeneralSettingsPage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            
            ->font('Noto Kufi Arabic' , provider: GoogleFontProvider::class)
            //->brandLogo(asset('storage/'.Setting::first()->app_logo))
            //->favicon(asset('storage/'.Setting::first()->favicon))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                ->gridColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3
                ])
                ->sectionColumnSpan(1),
                FilamentShieldPlugin::make(),
                FilamentFullCalendarPlugin::make()
                
            ])

            ->resources([
                RoleResource::class,
                /* \App\Filament\Resources\SessionsResource::class,
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\TeacherResource::class,
                \App\Filament\Resources\EvaluationResource::class,
                \App\Filament\Resources\SettingsResource::class,
                \App\Filament\Resources\StudentResource::class,    */
            ])
    

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                GeneralSettingsPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                // StatDashboardNew::class,
                // CalendarWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\IsAdmin::class,
                


            ])
            
            ->authMiddleware([
                Authenticate::class,
                
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');

            /* ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make(__('filament.dashboard'))
                        ->url('/admin')
                        ->icon('heroicon-o-home'),

                    NavigationItem::make(__('filament.evaluations'))
                        ->url('/admin/evaluations')
                        ->icon('heroicon-o-document-text'),

                    NavigationItem::make(__('filament.candidates'))
                        ->url('/admin/candidates')
                        ->icon('heroicon-o-user-group'),

                    NavigationItem::make(__('filament.settings'))
                        ->url('/admin/settings')
                        ->icon('heroicon-o-cog'),
                ]);
            }); */
            
        
    }
}






















    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    






















    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            





















    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    


            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            





















    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    


            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            





















    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    


            
                
            
    























    
    
        
            
            
            
            
            
                
            
            
            
            
                
            
            
            
                
                
            
            
                
                
                
                
                
                
                
                
                
            
            
                
            
    

    