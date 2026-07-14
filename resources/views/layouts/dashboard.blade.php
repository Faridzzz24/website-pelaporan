<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard HSE — Sistem Pelaporan Insiden K3 PT Cabot Indonesia">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HSE PT Cabot</title>
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

        body { background: #f8f9fa; color: var(--cabot-charcoal); }

        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 1rem; border-radius: 0.5rem;
            color: #6b7280; font-size: 0.875rem; transition: all 0.2s ease;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: #fef2f2; color: var(--cabot-red);
        }
        .sidebar-link.active { border-left: 3px solid var(--cabot-red); }

        .form-input-dash {
            background: #fff; border: 1px solid #d1d5db; color: var(--cabot-charcoal);
            border-radius: 0.5rem; transition: all 0.3s ease;
        }
        .form-input-dash:focus {
            border-color: var(--cabot-red); box-shadow: 0 0 0 3px rgba(210, 38, 48, 0.1); outline: none;
        }

        .kpi-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: all 0.3s ease;
        }
        .kpi-card:hover { border-color: #d1d5db; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

        .glass-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }

        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-count { animation: countUp 0.6s ease-out forwards; }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 1023px) {
            .sidebar { position: fixed; left: -280px; top: 0; bottom: 0; z-index: 50; transition: left 0.3s ease; }
            .sidebar.open { left: 0; }
        }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <aside class="sidebar w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 relative" id="sidebar">
            <div class="absolute top-0 left-0 w-full h-1" style="background: var(--cabot-gradient);"></div>
            <div class="py-5 px-4 border-b border-gray-100 flex justify-center">
                <a href="{{ route('dashboard') }}" class="block w-full px-2">
                    <img src="{{ asset('img/cabot-logo.png') }}" alt="PT Cabot" class="h-16 w-full object-contain">
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                
                <div class="relative group">
                    <button class="sidebar-link w-full text-left justify-between">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Laporan
                        </span>
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="hidden group-hover:block pl-11 pr-4 py-2 space-y-2">
                        <a href="{{ route('reports.export', ['format' => 'csv']) }}" class="block text-sm text-gray-500 hover:text-gray-900 transition-colors">📄 Format CSV</a>
                        <a href="{{ route('reports.export', ['format' => 'pdf']) }}" class="block text-sm text-gray-500 hover:text-gray-900 transition-colors" target="_blank">📑 Format PDF</a>
                        <a href="{{ route('reports.export', ['format' => 'word']) }}" class="block text-sm text-gray-500 hover:text-gray-900 transition-colors">📝 Format Word</a>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Kelola User
                </a>
                @endif
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <a href="/" class="sidebar-link" target="_blank">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Lihat Form Publik
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-semibold text-white" style="background: var(--cabot-red);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->role_label }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Bar with Cabot Red line --}}
            <div style="height: 4px; background: var(--cabot-red);"></div>
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                </div>
                <span class="text-xs text-gray-400">{{ now()->translatedFormat('d M Y, H:i') }}</span>
            </header>

            <main class="flex-1 p-4 sm:p-6 overflow-auto">
                @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade-in-up">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm animate-fade-in-up">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $errors->first() }}
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
