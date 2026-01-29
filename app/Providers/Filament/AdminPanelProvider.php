<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
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
use Kenepa\Banner\BannerPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Swis\Filament\Backgrounds\ImageProviders\Triangles;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                ->showAttribution(false),
                // \Hasnayeen\Themes\ThemesPlugin::make()
                // ->canViewThemesPage(function(){
                //     $user = auth()->user()->isAdmin();
                //     if($user){
                //         return true;
                //     }
                //     else{
                //         return false;
                //     }
                // }),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                BannerPlugin::make()
                ->persistsBannersInDatabase()
                ->bannerManagerAccessPermission('')
                ->title('Bildirişlər')
                ->subheading('Bildirişlərin idarə olunması')
                ->navigationLabel('Bildirişlər')
                ->bannerManagerAccessPermission('page_BannerManagerPage')
                ,
                // ->navigationGroup('Marketing'),
                SpotlightPlugin::make(),
                // OverlookPlugin::make()
                // ->sort(2)
                // ->columns([
                //     'default' => 1,
                //     'sm' => 2,
                //     'md' => 3,
                //     'lg' => 4,
                //     'xl' => 5,
                //     '2xl' => null,
                // ]),
                FilamentApexChartsPlugin::make()
            ])
            ->resources([
                config('filament-logger.activity_resource')
            ])
            // ->profile()
            ->globalSearchKeyBindings(['command+f', 'ctrl+f'])
            ->brandName('KOBIM PANELI')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->path('panel')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // OverlookWidget::class,
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
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }


}
