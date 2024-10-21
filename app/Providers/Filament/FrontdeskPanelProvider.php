<?php

namespace App\Providers\Filament;

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

class FrontdeskPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('frontdesk')
            ->path('frontdesk')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Frontdesk/Resources'), for: 'App\\Filament\\Frontdesk\\Resources')
            ->discoverResources(in: app_path('Filament/Management/Resources'), for: 'App\\Filament\\Management\\Resources')
            ->discoverPages(in: app_path('Filament/Frontdesk/Pages'), for: 'App\\Filament\\Frontdesk\\Pages')
            ->navigationGroups([
                'Daily Operations',
                'Operations Management',
                'Rooms Management',
                'Guest Management',
                'Transport Management',
                'Group Management',
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Management')
                    ->url('/management')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager'),
                MenuItem::make()
                    ->label(label: 'Restaurant')
                    ->url('/restaurant')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager')
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Frontdesk/Widgets'), for: 'App\\Filament\\Frontdesk\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->viteTheme('resources/css/filament/frontdesk/theme.css');
            
    }
}
