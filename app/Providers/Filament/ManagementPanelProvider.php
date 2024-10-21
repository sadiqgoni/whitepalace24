<?php

namespace App\Providers\Filament;

use App\Filament\Management\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ManagementPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('management')
            ->path('management')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Front Desk')
                    ->url('/frontdesk')
                    ->icon('heroicon-o-users')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager'),
                MenuItem::make()
                    ->label('Restaurant')
                    ->url('/restaurant')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager')
            ])
            ->discoverResources(in: app_path('Filament/Management/Resources'), for: 'App\\Filament\\Management\\Resources')
            ->discoverPages(in: app_path('Filament/Management/Pages'), for: 'App\\Filament\\Management\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Management/Widgets'), for: 'App\\Filament\\Management\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                StatsOverview::class,

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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/management/theme.css');
            
    }
}
