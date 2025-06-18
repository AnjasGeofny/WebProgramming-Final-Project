# Web Programming Final Project - Sports Field Booking System

## 📖 Deskripsi Proyek

Sistem Booking Lapangan Olahraga adalah aplikasi web yang memungkinkan pengguna untuk memesan lapangan olahraga (Badminton dan Futsal) secara online. Aplikasi ini dibangun menggunakan Laravel 11 dengan Filament sebagai admin panel.

## ✨ Fitur Utama

### Frontend (User)
- **Halaman Home**: Landing page dengan informasi umum
- **Daftar Lapangan**: Menampilkan semua lapangan yang tersedia
- **Detail Lapangan**: Informasi detail lapangan dengan jadwal waktu
- **Sistem Booking**: Interface booking dengan visual feedback
- **Upload Gambar**: Dukungan upload gambar lapangan
- **Responsive Design**: Tampilan yang responsif di berbagai perangkat

### Backend (Admin Panel)
- **Dashboard Admin**: Overview statistik dan informasi penting
- **Manajemen Lapangan**: CRUD lapangan dengan upload gambar
- **Manajemen Jadwal**: Pembuatan dan pengelolaan jadwal bulk
- **Manajemen Booking**: Tracking dan pengelolaan booking
- **Widget Dashboard**: Live statistics dan monitoring
- **Filter dan Search**: Pencarian dan filter yang canggih

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 11
- **Admin Panel**: Filament 3
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS
- **File Storage**: Laravel Storage dengan symbolic link
- **Development Environment**: Laragon (Windows)

## 📋 Fitur Detail

### 1. Sistem Lapangan
- Dukungan multiple court per lapangan
- Tipe olahraga: Badminton, Futsal
- Upload gambar lapangan dengan fallback ke gambar default
- Harga per jam yang dapat dikustomisasi

### 2. Sistem Jadwal
- Pembuatan jadwal bulk untuk multiple time slots
- Jadwal operasional dari 07:00 - 23:00
- Toggle ketersediaan jadwal
- Deteksi konflik booking otomatis

### 3. Sistem Booking
- Booking single atau multiple time slots
- Informasi customer lengkap
- Status booking yang dapat ditrack
- Visual feedback untuk slot yang sudah dibooking

### 4. Admin Dashboard
- Live statistics widget
- Field status monitoring
- Booking overview
- Schedule management tools

## 🚀 Instalasi dan Setup

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL
- Laragon atau XAMPP

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/AnjasGeofny/WebProgramming-Final-Project.git
   cd WebProgramming-Final-Project
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   - Buat database MySQL baru
   - Update file `.env` dengan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=proyek_proweb
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Database Migration dan Seeding**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Create Admin User**
   ```bash
   php artisan make:filament-user
   ```

8. **Run Application**
   ```bash
   php artisan serve
   ```

## 📁 Struktur Database

### Tables Utama:
- `fields` - Data lapangan olahraga
- `schedules` - Jadwal operasional lapangan
- `bookings` - Data booking customer
- `users` - Data pengguna (admin)

### Relasi:
- Field -> Schedule (1:N)
- Schedule -> Booking (1:N)
- Field -> Booking (1:N through schedules)

## 🎯 Cara Penggunaan

### Admin Panel
1. Akses `/admin` untuk masuk ke admin panel
2. Login dengan akun admin yang telah dibuat
3. Kelola lapangan, jadwal, dan booking melalui menu navigasi

### User Interface
1. Akses homepage untuk melihat informasi umum
2. Browse lapangan yang tersedia di `/fields`
3. Klik detail lapangan untuk melihat jadwal dan booking
4. Gunakan form booking untuk memesan lapangan

## 🔧 Konfigurasi Khusus

### Upload Gambar
- Directory: `storage/app/public/field-images/`
- Format: JPG, PNG, WEBP
- Max size: 2MB
- Automatic resize dan optimization

### Time Slots
- Operasional: 07:00 - 23:00
- Interval: 1 jam per slot
- Bulk creation support

### Booking System
- Support multiple time slots dalam satu booking
- Customer information tracking
- Automatic conflict detection

## 👥 Tim Pengembang

- **Anjas Geofany** - Full Stack Developer

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik (Final Project Web Programming).

## 🐛 Known Issues & Limitations

1. Payment system belum diimplementasi
2. Email notification belum tersedia
3. Real-time booking updates menggunakan manual refresh

## 🔄 Future Enhancements

1. Implementasi payment gateway
2. Real-time notifications
3. Mobile app version
4. Advanced reporting system
5. Customer membership system

---

**Note**: Proyek ini merupakan final project untuk mata kuliah Web Programming dan telah dikembangkan dengan fitur-fitur lengkap untuk sistem booking lapangan olahraga.
