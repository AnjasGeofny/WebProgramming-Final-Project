@extends('user.layout')

@section('title', 'About - Balikpapan Sport')

@section('content')
<!-- About Main Section - sesuai dengan referensi -->
<section class="relative bg-gradient-to-br from-teal-500 via-teal-600 to-teal-700 overflow-hidden">
    <!-- Navigation Menu di kanan atas -->
    <nav class="absolute top-8 right-8 z-50">
        <div class="flex space-x-8">
            <a href="{{ route('home') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium">Home</a>
            <a href="{{ route('about') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium border-b-2 border-white">About</a>
            <a href="{{ route('fields') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium">Field</a>
        </div>
    </nav><div class="max-w-full mx-auto px-0 w-full py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-center">
                <!-- Konten Teks di Kiri - sesuai referensi -->
                <div class="text-white space-y-7 px-10 max-w-3xl">
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-9">
                        About<br>
                        Balikpapan Sport
                    </h1>
                    
                    <p class="text-base text-white leading-relaxed mb-7">
                        Balikpapan Sport adalah website yang memudahkan 
                        kamu melihat daftar lapangan olahraga yang 
                        disewakan di Balikpapan. Tersedia tiga kategori 
                        lapangan yaitu futsal, badminton, serta futsal & 
                        badminton.
                    </p>                    <div class="space-y-4 mb-7">
                        <p class="text-base text-white font-medium">Kamu bisa:</p>
                        <ul class="space-y-2 text-white text-base pl-7">
                            <li class="flex items-start">
                                <span class="text-white mr-3">•</span>
                                Melihat jenis lapangan yang tersedia
                            </li>
                            <li class="flex items-start">
                                <span class="text-white mr-3">•</span>
                                Mengecek harga sewa per jam
                            </li>
                            <li class="flex items-start">
                                <span class="text-white mr-3">•</span>
                                Mengetahui jam-jam yang sudah full
                            </li>
                        </ul>
                    </div>

                    <p class="text-base text-white leading-relaxed">
                        Cocok untuk kamu yang ingin cari lapangan tanpa 
                        ribet dan cepat tahu jadwal kosongnya!
                    </p>
                </div>                <!-- Gambar di Kanan - menempel ke kanan halaman -->
                <div class="relative flex justify-end pr-0">
                    <!-- Single image showing both sports -->
                    <div class="w-[500px] h-[330px]">
                        <img src="{{ asset('images/sports-combined.jpg') }}" 
                             alt="Badminton and Futsal Players" 
                             class="w-full h-full object-cover rounded-l-xl shadow-2xl">
                    </div>
                </div></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection