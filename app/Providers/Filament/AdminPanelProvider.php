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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Impor semua widget kustom Anda
use App\Filament\Widgets\WelcomeMessageWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\BookingsChart;
use App\Filament\Widgets\ScheduleOverview;
use App\Filament\Widgets\FieldStatusWidget;
use App\Filament\Widgets\LiveBookingWidget;
use App\Filament\Widgets\FieldListWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin') // URL untuk panel admin Anda, e.g., yoursite.com/admin
            ->login() // Mengaktifkan halaman login default Filament
            ->brandName('Admin Balikpapan Sport') // Nama brand yang ditampilkan di halaman login
            ->colors([
                'primary' => Color::Amber, // Anda bisa mengganti warna primer sesuai keinginan
            ])
            ->resources([
                \App\Filament\Resources\BookingResource::class,
                \App\Filament\Resources\FieldResource::class,
                \App\Filament\Resources\ScheduleResource::class,
                \App\Filament\Resources\UserResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class, // Halaman dashboard default
            ])
            ->widgets([
                    // Hanya widget utama yang diperlukan
                WelcomeMessageWidget::class,
                StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class, // Middleware autentikasi Filament
            ]);
        // Anda bisa menambahkan fitur lain seperti:
        // ->navigationGroups([
        //     'Manajemen Booking',
        //     'Data Master',
        //     'Pengguna',
        // ])
        // ->viteTheme('resources/css/filament/admin/theme.css') // Jika menggunakan tema Vite kustom
    }
}
