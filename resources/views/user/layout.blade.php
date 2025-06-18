<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Balikpapan Sport')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">    <style>
        .hero-bg {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1530549387789-4c1017266635?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .stadium-bg {
            background: linear-gradient(rgba(59, 130, 246, 0.8), rgba(37, 99, 235, 0.8)), url('https://images.unsplash.com/photo-1551698618-1dfe5d97d256?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
        }

        /* New Stadium Hero Background for Home Page */
        .stadium-hero-bg {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(59, 130, 246, 0.7) 50%, rgba(147, 197, 253, 0.5) 100%), 
                        url('https://images.unsplash.com/photo-1551698618-1dfe5d97d256?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .navbar-blur {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animation styles */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }

        /* Modern glassmorphism effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }    </style>
</head>
<body class="bg-gray-50">

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>    <!-- Footer -->
    <footer class="bg-gradient-to-br from-teal-500 via-teal-600 to-cyan-700 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl font-bold text-white mb-4">Balikpapan Sport</h3>
                    <p class="text-teal-100 mb-4">
                        Platform booking lapangan olahraga terpercaya di Balikpapan. 
                        Nikmati fasilitas modern dengan pelayanan terbaik.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-teal-100 hover:text-white transition-colors duration-300 hover:scale-110 transform">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-teal-100 hover:text-white transition-colors duration-300 hover:scale-110 transform">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-teal-100 hover:text-white transition-colors duration-300 hover:scale-110 transform">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-teal-100 hover:text-white transition-colors duration-300">Home</a></li>
                        <li><a href="{{ route('about') }}" class="text-teal-100 hover:text-white transition-colors duration-300">About</a></li>
                        <li><a href="{{ route('fields') }}" class="text-teal-100 hover:text-white transition-colors duration-300">Fields</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Contact Info</h4>
                    <ul class="space-y-2 text-teal-100">
                        <li><i class="fas fa-map-marker-alt mr-2 text-cyan-300"></i>Balikpapan, Kalimantan Timur</li>
                        <li><i class="fas fa-phone mr-2 text-cyan-300"></i>+62 123 456 789</li>
                        <li><i class="fas fa-envelope mr-2 text-cyan-300"></i>info@balikpapansport.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-teal-400 mt-8 pt-8 text-center">
                <p class="text-teal-100">&copy; {{ date('Y') }} Balikpapan Sport. All rights reserved.</p>
            </div>
        </div>
    </footer>    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
