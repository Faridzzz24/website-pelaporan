<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pelaporan Insiden K3 PT Cabot Indonesia — Laporkan kejadian keselamatan kerja dengan cepat dan mudah.">
    <title>@yield('title', 'Pelaporan Insiden K3') — PT Cabot Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }

        :root {
            --cabot-black: #000000;
            --cabot-yellow: #F99D1B;
            --cabot-orange: #E76B30;
            --cabot-red: #E43D22;
            --cabot-red-dark: #CD171F;

            --cabot-charcoal: #1A1A1A;
            --cabot-dark-gray: #333333;
            --cabot-mid-gray: #666666;
            --cabot-light-gray: #F4F4F4;
            --cabot-off-white: #F9F9F9;
            
            --cabot-gradient: linear-gradient(90deg, var(--cabot-red-dark) 0%, var(--cabot-red) 40%, var(--cabot-orange) 80%, var(--cabot-yellow) 100%);
        }

        body {
            background: var(--cabot-off-white);
            color: var(--cabot-charcoal);
        }

        /* Cabot Red Gradient Bar at top */
        .cabot-topbar {
            height: 4px;
            background: linear-gradient(90deg, var(--cabot-red) 0%, var(--cabot-red-light) 100%);
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        }

        .card-elevated {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
        }

        /* Form inputs */
        .form-input {
            background: #ffffff;
            border: 1px solid #d1d5db;
            color: var(--cabot-charcoal);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: var(--cabot-red);
            box-shadow: 0 0 0 3px rgba(210, 38, 48, 0.1);
            outline: none;
        }
        .form-input::placeholder {
            color: #9ca3af;
        }

        /* Primary button */
        .btn-primary {
            background: var(--cabot-red);
            color: #fff;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: var(--cabot-red-dark);
            box-shadow: 0 4px 15px rgba(210, 38, 48, 0.25);
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        /* Fade in animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Toggle switch */
        .toggle-switch {
            position: relative;
            width: 48px;
            height: 26px;
            background: #d1d5db;
            border-radius: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background: #ffffff;
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .toggle-switch.active {
            background: var(--cabot-red);
        }
        .toggle-switch.active::after {
            transform: translateX(22px);
        }

        /* Urgency radio selectors — using data attributes for JS-based styling */
        .urgency-option input[type="radio"]:checked + .urgency-label-rendah {
            background: #ecfdf5; border-color: #10b981; color: #065f46;
        }
        .urgency-option input[type="radio"]:checked + .urgency-label-sedang {
            background: #fffbeb; border-color: #f59e0b; color: #92400e;
        }
        .urgency-option input[type="radio"]:checked + .urgency-label-tinggi {
            background: #fff7ed; border-color: #f97316; color: #9a3412;
        }
        .urgency-option input[type="radio"]:checked + .urgency-label-kritis {
            background: #fef2f2; border-color: #ef4444; color: #991b1b;
        }

        /* Incident type radio selectors */
        .type-option input[type="radio"]:checked + .type-label {
            background: #fef2f2;
            border-color: var(--cabot-red);
            box-shadow: 0 0 0 2px rgba(210, 38, 48, 0.15);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--cabot-off-white); }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="antialiased">
    {{-- Cabot Red Top Bar --}}
    <div class="cabot-topbar"></div>

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/cabot-logo.png') }}" alt="PT Cabot" class="h-12 w-auto object-contain -mt-1.5">
                        <span class="hidden sm:block text-sm text-gray-500 font-medium border-l-2 border-gray-200 pl-3">Safety Reporting System</span>
                    </div>
                </a>
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('report.track') }}" class="text-sm text-gray-500 hover:text-cabot-red transition-colors px-3 py-2 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="hidden sm:inline">Lacak Laporan</span>
                    </a>
                    <a href="{{ route('login') }}" class="text-sm bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition-all duration-300">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="py-10 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
            <p class="text-sm text-gray-400">&copy; {{ date('Y') }} PT Cabot Indonesia — Safety Reporting System</p>
            <p class="text-xs text-gray-300 mt-1">Keselamatan adalah prioritas utama.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
