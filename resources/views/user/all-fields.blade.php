@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('user.layout')

@section('title', 'Explore Our Sport Fields')

@section('content')
<!-- Fields Main Section - sesuai dengan referensi gambar -->
<section class="min-h-screen bg-gradient-to-br from-teal-400 via-teal-500 to-cyan-600 py-16">
    <!-- Navigation Menu di kanan atas -->
    <nav class="absolute top-8 right-8 z-50">
        <div class="flex space-x-8">
            <a href="{{ route('home') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium">Home</a>
            <a href="{{ route('about') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium">About</a>
            <a href="{{ route('fields') }}" class="text-white hover:text-blue-200 transition-colors duration-300 text-lg font-medium border-b-2 border-white">Field</a>
        </div>
    </nav>
    
    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-6 lg:px-8">        <!-- Header Section -->
        <div class="text-left mb-12">
            <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-8 inline-flex items-center">
                <a href="{{ route('fields') }}" class="mr-3 hover:text-blue-200 transition-colors duration-300">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Explore Our Sport Fields
            </h1>
            
            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-4 mb-8">
                <button class="filter-btn active px-6 py-3 bg-white text-gray-800 rounded-full font-medium hover:bg-gray-100 transition-all duration-300 shadow-lg" data-filter="all">
                    All
                </button>
                <button class="filter-btn px-6 py-3 bg-white/20 text-white rounded-full font-medium hover:bg-white hover:text-gray-800 transition-all duration-300 backdrop-blur-sm" data-filter="badminton-futsal">
                    Badminton & Futsal
                </button>
                <button class="filter-btn px-6 py-3 bg-white/20 text-white rounded-full font-medium hover:bg-white hover:text-gray-800 transition-all duration-300 backdrop-blur-sm" data-filter="badminton">
                    Badminton
                </button>
                <button class="filter-btn px-6 py-3 bg-white/20 text-white rounded-full font-medium hover:bg-white hover:text-gray-800 transition-all duration-300 backdrop-blur-sm" data-filter="futsal">
                    Futsal
                </button>
            </div>
        </div>
            <!-- Fields Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 field-grid">
            @if($fieldsGrouped->count() > 0)
                @foreach($fieldsGrouped as $venue)
                <div class="field-card bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" 
                     data-type="{{ strtolower(str_replace(' & ', '-', $venue->display_type)) }}">                    <!-- Field Image -->
                    <div class="relative h-48 overflow-hidden">                        @php
                            // Priority: uploaded image first, then fallback to sport-specific defaults
                            if ($venue->image && Storage::disk('public')->exists($venue->image)) {
                                $imageUrl = asset('storage/' . $venue->image);
                            } else {
                                // Generate unique image for each field based on type and ID
                                $fieldIdMod = $venue->id % 3; // Create 3 variations per sport type
                                
                                // Clean the display_type for proper matching
                                $sportType = strtolower($venue->display_type);
                                
                                if (str_contains($sportType, 'futsal') && !str_contains($sportType, 'badminton')) {
                                    $futsalImages = [
                                        'https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1551698618-1dbc5d1e7950?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $futsalImages[$fieldIdMod];
                                } elseif (str_contains($sportType, 'badminton') && !str_contains($sportType, 'futsal')) {
                                    $badmintonImages = [
                                        'https://images.unsplash.com/photo-1544717301-9cdcb1f5940f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1594736797933-d0300ba55db0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $badmintonImages[$fieldIdMod];
                                } elseif (str_contains($sportType, 'basketball')) {
                                    $basketballImages = [
                                        'https://images.unsplash.com/photo-1546519638-68e109498ffc?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1574623452334-1e0ac2b3ccb4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1627627256672-027a4613d028?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $basketballImages[$fieldIdMod];
                                } elseif (str_contains($sportType, 'tennis')) {
                                    $tennisImages = [
                                        'https://images.unsplash.com/photo-1542144582-1ba00456b5e3?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1551698618-1dbc5d1e7950?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $tennisImages[$fieldIdMod];
                                } elseif (str_contains($sportType, 'volleyball')) {
                                    $volleyballImages = [
                                        'https://images.unsplash.com/photo-1547347298-4074fc3086f0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $volleyballImages[$fieldIdMod];
                                } elseif (str_contains($sportType, 'badminton') && str_contains($sportType, 'futsal')) {
                                    // Mixed sports - show varied facility images
                                    $mixedImages = [
                                        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1577223625816-7546f13df25d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1558618666-fbd6c327cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $mixedImages[$fieldIdMod];
                                } else {
                                    // Default sports facility images
                                    $defaultImages = [
                                        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1577223625816-7546f13df25d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1558618666-fbd6c327cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $defaultImages[$fieldIdMod];
                                }
                            }
                        @endphp
                        
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $venue->name }}" 
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                        
                        <!-- Sport Type Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full shadow-lg">
                                {{ $venue->display_type }}
                            </span>
                        </div>
                        
                        <!-- Courts Count Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="px-2 py-1 bg-teal-500/90 backdrop-blur-sm text-white text-xs font-medium rounded-full shadow-lg">
                                {{ $venue->total_courts }} Courts
                            </span>
                        </div>
                    </div>
                      <!-- Field Info -->
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-2">{{ $venue->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $venue->location ?? 'Balikpapan' }}</p>
                        
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-teal-600">
                                Rp {{ number_format($venue->price_per_hour, 0, ',', '.') }}
                            </span>
                            <span class="text-gray-500 text-sm">/hours</span>
                        </div>
                        
                        <!-- View Detail Button -->
                        <a href="{{ route('field.detail', $venue->id) }}" 
                           class="block w-full text-center bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white py-3 px-6 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            View Detail
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Sample fields jika database kosong -->                <div class="field-card bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-type="futsal">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             alt="Balikpapan Sport Center" 
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full shadow-lg">
                                Futsal
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-2">Balikpapan Sport Center</h3>
                        <p class="text-gray-600 mb-4">Jl. Jenderal Sudirman</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-teal-600">Rp 75.000</span>
                            <span class="text-gray-500 text-sm">/hours</span>
                        </div>
                        <a href="#" class="block w-full text-center bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white py-3 px-6 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            View Detail
                        </a>
                    </div>
                </div>
                
                <div class="field-card bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-type="badminton">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1544717301-9cdcb1f5940f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             alt="Global Sport" 
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full shadow-lg">
                                Badminton
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-2">Global Sport</h3>
                        <p class="text-gray-600 mb-4">Jl. MT Haryono</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-teal-600">Rp 75.000</span>
                            <span class="text-gray-500 text-sm">/hours</span>
                        </div>
                        <a href="#" class="block w-full text-center bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white py-3 px-6 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            View Detail
                        </a>
                    </div>
                </div>
                
                <div class="field-card bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-type="futsal">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1551698618-1dbc5d1e7950?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             alt="Family Sport Clinic" 
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full shadow-lg">
                                Futsal
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-2">Family Sport Clinic</h3>
                        <p class="text-gray-600 mb-4">Jl. Soekarno Hatta</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-teal-600">Rp 75.000</span>
                            <span class="text-gray-500 text-sm">/hours</span>
                        </div>
                        <a href="#" class="block w-full text-center bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white py-3 px-6 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            View Detail
                        </a>
                    </div>
                </div>
                
                <div class="field-card bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-type="badminton">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             alt="Sepinggan Pratama" 
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full shadow-lg">
                                Badminton
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-2">Sepinggan Pratama</h3>
                        <p class="text-gray-600 mb-4">Jl. Marsma R. Iswahyudi</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-teal-600">Rp 85.000</span>
                            <span class="text-gray-500 text-sm">/hours</span>
                        </div>
                        <a href="#" class="block w-full text-center bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white py-3 px-6 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            View Detail
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- JavaScript untuk Filter -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const fieldGrid = document.querySelector('.field-grid');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('bg-white', 'text-gray-800');
                btn.classList.add('bg-white/20', 'text-white');
            });
            
            this.classList.add('active');
            this.classList.remove('bg-white/20', 'text-white');
            this.classList.add('bg-white', 'text-gray-800');
            
            // For now, use client-side filtering
            // In production, you can implement AJAX filtering by sending request to server
            const fieldCards = document.querySelectorAll('.field-card');
            
            fieldCards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                
                if (filter === 'all') {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);                } else if (filter === 'badminton-futsal') {
                    // Only show venues that have exactly "badminton-futsal" type (both sports)
                    if (cardType === 'badminton-futsal') {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }                } else {
                    // For individual sport filters, only show exact matches
                    if (cardType === filter) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                }
            });
        });
    });
    
    // Set initial state for smooth animations
    const fieldCards = document.querySelectorAll('.field-card');
    fieldCards.forEach(card => {
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
    });
    
    // Add hover effects
    fieldCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1)';
        });
    });
});
</script>

@endsection
