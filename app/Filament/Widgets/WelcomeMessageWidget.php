<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan informasi user yang login

class WelcomeMessageWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-message-widget';

    // Mengatur urutan widget di dashboard, angka lebih kecil akan tampil lebih dulu/atas.
    // Jika Anda ingin ini paling atas, set ke 0 atau 1.
    protected static ?int $sort = 0;

    // Properti ini akan diisi di metode mount() dan bisa diakses dari file Blade widget.
    public ?string $userName = null;
    public ?string $userRole = null;

    /**
     * Metode mount() dipanggil saat komponen widget pertama kali di-render.
     * Kita gunakan ini untuk mengambil data user yang sedang login.
     */
    public function mount(): void
    {
        $user = Auth::user();

        if ($user) {
            $this->userName = $user->name ?? 'User'; // Fallback jika name null
            // Asumsi model User memiliki atribut 'role' (misalnya 'admin', 'user')
            // ucfirst() digunakan untuk membuat huruf pertama kapital (Admin, User)
            $this->userRole = isset($user->role) && $user->role ? ucfirst($user->role) : 'Admin';
        } else {
            // Fallback jika karena suatu alasan user tidak terautentikasi saat widget di-load
            $this->userName = 'Guest';
            $this->userRole = null;
        }
    }
}
