<?php

namespace Antidote\LaravelCart\Tests\Fixtures\Filament;

use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomAdjustmentResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomCustomerResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomOrderResource;
use Antidote\LaravelCartFilament\CartPanelPlugin;
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

class TestPanelProviderWithCustomResources extends \Filament\PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('testing')
            ->path('testing')
            ->default()
            ->plugins([
                CartPanelPlugin::make()
                    ->config([
                        'resources' => [
                            'order' => CustomOrderResource::class,
                            'customer' => CustomCustomerResource::class,
                            'adjustment' => CustomAdjustmentResource::class
                        ]
                    ])
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
