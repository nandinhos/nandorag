<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\CustomDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Vite;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->assets([
                \Filament\Support\Assets\Js::make('app-scripts', Vite::asset('resources/js/app.js'))->module(),
            ])
            ->authGuard('web')
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): HtmlString => new HtmlString(
                    '<div class="neo-topbar-brand">'
                    .'<span class="neo-topbar-badge">NandoRAG</span>'
                    .'<span class="neo-topbar-version">v1.0 &mdash; '.now()->format('d/m/Y H:i').'</span>'
                    .'</div>'
                ),
            )
            ->colors([
                // Neo-brutalist: primary = teal (#22D3EE), mapped to cyan scale
                'primary' => Color::Cyan,
                // danger = magenta/fuchsia
                'danger' => Color::Fuchsia,
                // warning = yellow
                'warning' => Color::Yellow,
                // success = neo-green → emerald closest
                'success' => Color::Emerald,
                // info = teal
                'info' => Color::Cyan,
                // gray = stone (warm, fits cream bg)
                'gray' => Color::Stone,
                'purple' => Color::Purple,
            ])
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->pages([
                CustomDashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
