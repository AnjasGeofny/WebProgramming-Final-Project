@extends('user.layout')

@section('title', 'Home - Balikpapan Sport')

@section('content')
<!-- Hero Section persis seperti gambar referensi -->
<section class="relative min-h-screen overflow-hidden">    <!-- Background Image Stadium -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/home.jpg') }}" 
             alt="Balikpapan Stadium" 
             class="w-full h-full object-cover">
        <!-- Dark overlay untuk readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-black/20"></div>
    </div>
    
    <!-- Navigation Menu di kanan atas - persis seperti referensi -->
    <nav class="absolute top-8 right-8 z-50">
        <div class="flex space-x-8">
            <a href="{{ route('home') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium {{ request()->routeIs('home') ? 'border-b-2 border-white' : '' }}">Home</a>
            <a href="{{ route('about') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium">About</a>
            <a href="{{ route('fields') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium">Field</a>
        </div>
    </nav>    <!-- Content Container -->
    <div class="relative z-40 h-screen flex flex-col justify-between">
        <!-- Main Title at top left -->
        <div class="pt-20 pl-8 sm:pl-12 lg:pl-16">
            <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                Balikpapan<br>
                <span class="text-white">Sport</span>
            </h1>
        </div>
        
        <!-- Subtitle at bottom left -->
        <div class="pb-20 pl-8 sm:pl-12 lg:pl-16">
            <a href="{{ route('fields') }}" class="inline-block">
                <p class="text-white text-xl md:text-2xl font-medium hover:text-blue-200 transition-colors duration-300 cursor-pointer">
                    Look at Your Field Now!
                </p>
            </a>
        </div>
    </div>
</section>
@endsection
