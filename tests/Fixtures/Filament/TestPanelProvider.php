<?php

namespace Antidote\LaravelCart\Tests\Fixtures\Filament;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TestPanelProvider extends \Filament\PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('testing')
            ->path('testing')
            ->default()
            ->plugins([
                CartPanelPlugin::make()
                    ->orderResource(OrderResource::class)
                    ->customerResource(CustomerResource::class)
                    ->adjustmentResource(AdjustmentResource::class)
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
            ]);
    }
}
