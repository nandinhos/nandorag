<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\CustomDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
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
            ->viteTheme('resources/css/filament/admin/theme.css')
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
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString("
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const observer = new MutationObserver(function(mutations) {
                                document.querySelectorAll('.fi-ta-table').forEach(table => {
                                    const headers = Array.from(table.querySelectorAll('.fi-ta-header-cell'))
                                        .map(th => th.innerText.trim());
                                    
                                    table.querySelectorAll('.fi-ta-row').forEach(row => {
                                        row.querySelectorAll('.fi-ta-cell').forEach((cell, index) => {
                                            if (headers[index] && !cell.hasAttribute('data-label')) {
                                                cell.setAttribute('data-label', headers[index]);
                                            }
                                        });
                                    });
                                });
                            });
                            
                            observer.observe(document.body, { childList: true, subtree: true });
                        });
                    </script>
                ")
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
