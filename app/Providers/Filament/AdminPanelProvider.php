<?php

namespace App\Providers\Filament;



use App\Filament\Auth\Login;
use App\Filament\Pages\GeneralSettingsPage;
use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // dd(asset(app(GeneralSettings::class)->logo));
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)

            ->font('Noto Kufi Arabic' , provider: GoogleFontProvider::class)
            ->brandName(' لوحة تحكم الإدارة - '.app(\App\Settings\GeneralSettings::class)->branch_name)
            ->brandLogo(asset('storage/'.app(\App\Settings\GeneralSettings::class)->favicon))
            ->favicon(asset('storage/'.app(\App\Settings\GeneralSettings::class)->favicon))
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
                \App\Http\Middleware\RedirectAuthRoutes::class,


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