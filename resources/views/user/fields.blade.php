@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('user.layout')

@section('title', 'Choose Your Field - Balikpapan Sport')

@section('content')
<!-- Fields Main Section - konsisten dengan All Fields -->
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
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-left mb-12">
            <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-8">
                Choose Your Field
            </h1>
            
            <h2 class="text-white text-2xl md:text-3xl font-medium mb-8">
                Top Fields in Balikpapan
            </h2>
        </div>
        
        <!-- Fields Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 field-grid">
            @foreach($topFields as $field)
            <div class="field-card bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">                <!-- Field Image -->                <div class="relative h-48 overflow-hidden">
                    @php
                        // Use uploaded image if available, otherwise use default based on sport type
                        if ($field->image && Storage::disk('public')->exists($field->image)) {
                            $imageUrl = asset('storage/' . $field->image);
                        } else {
                            // Fallback to sport-specific default images
                            $fieldIdMod = $field->id % 3;
                            
                            switch($field->type) {
                                case 'Futsal':
                                    $futsalImages = [
                                        'https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1551698618-1dfe5cc97123?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $futsalImages[$fieldIdMod];
                                    break;
                                    
                                case 'Badminton':
                                    $badmintonImages = [
                                        'https://images.unsplash.com/photo-1544717301-9cdcb1f5940f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1594736797933-d0300ba55db0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $badmintonImages[$fieldIdMod];
                                    break;
                                    
                                case 'Basketball':
                                    $basketballImages = [
                                        'https://images.unsplash.com/photo-1546519638-68e109498ffc?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1574623452334-1e0ac2b3ccb4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1627627256672-027a4613d028?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $basketballImages[$fieldIdMod];
                                    break;
                                    
                                case 'Tennis':
                                    $tennisImages = [
                                        'https://images.unsplash.com/photo-1542144582-1ba00456b5e3?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1551698618-1dbc5d1e7950?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $tennisImages[$fieldIdMod];
                                    break;
                                    
                                case 'Volleyball':
                                    $volleyballImages = [
                                        'https://images.unsplash.com/photo-1547347298-4074fc3086f0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $volleyballImages[$fieldIdMod];
                                    break;
                                    
                                case 'Badminton & Futsal':
                                    $mixedImages = [
                                        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                                        'https://images.unsplash.com/photo-1544717301-9cdcb1f5940f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                                    ];
                                    $imageUrl = $mixedImages[$fieldIdMod];
                                    break;
                                    
                                default:
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
                         alt="{{ $field->name }}" 
                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    
                    <!-- Sport Type Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full shadow-lg">
                            {{ $field->type }}
                        </span>
                    </div>
                </div>
                
                <!-- Field Info -->
                <div class="p-6">
                    <h3 class="font-bold text-xl text-gray-800 mb-2">{{ $field->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $field->location ?? 'Balikpapan' }}</p>
                    
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold text-teal-600">
                            Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}
                        </span>
                        <span class="text-gray-500 text-sm">/hours</span>
                    </div>
                      <!-- View Detail Button -->
                    <a href="{{ route('field.detail', $field->id) }}" 
                       class="block w-full text-center bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white py-3 px-6 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                        View Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- View All Field Link - Kanan Bawah -->
        <div class="flex justify-end mt-8">
            <a href="{{ route('fields.all') }}" class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-full font-medium hover:bg-white hover:text-gray-800 transition-all duration-300 shadow-lg">
                View All Field
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
@endsection
