@extends('layouts.public')
@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto animate-fade-in-up">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: #fef2f2; border: 1px solid #fecaca;">
            <svg class="w-8 h-8" style="color: var(--cabot-red);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Login</h1>
        <p class="text-gray-500 text-sm">Akses dashboard terbatas untuk personel yang berwenang.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="card-elevated p-6 sm:p-8 space-y-5">
        @csrf

        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@ptcabot.com" class="form-input w-full px-4 py-3.5 text-sm" required autofocus>
            @error('email')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Password</label>
            <div class="relative">
                <input type="password" name="password" placeholder="••••••••" class="form-input w-full px-4 py-3.5 text-sm pr-12" required id="passwordInput">
                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eyeIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500/30">
                <span class="text-sm text-gray-500">Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="btn-primary w-full py-3.5 text-sm tracking-wide">
            <svg class="w-4 h-4 inline mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Masuk ke Dashboard
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="/" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">← Kembali ke Form Pelaporan</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush
