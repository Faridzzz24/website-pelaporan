@extends('layouts.dashboard')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card p-6 mb-8 animate-fade-in-up">
        <h3 class="text-sm font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4" style="color: var(--cabot-red);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Tambah User Baru
        </h3>
        <form method="POST" action="{{ route('users.store') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" class="form-input-dash w-full px-3 py-2.5 text-sm" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@ptcabot.com" class="form-input-dash w-full px-3 py-2.5 text-sm" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Password</label>
                <input type="password" name="password" placeholder="Min. 8 karakter" class="form-input-dash w-full px-3 py-2.5 text-sm" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Role</label>
                <select name="role" class="form-input-dash w-full px-3 py-2.5 text-sm" required>
                    <option value="supervisor">Supervisor</option>
                    <option value="hse_officer">HSE Officer</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-6 py-2.5 rounded-lg text-white text-sm font-medium transition-all duration-300" style="background: var(--cabot-red);">
                    Tambah User
                </button>
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Daftar User ({{ $users->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Dibuat</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold text-white" style="background: var(--cabot-red);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-gray-800 font-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $user->email }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $user->role === 'hse_officer' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'supervisor' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            ">{{ $user->role_label }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-400 text-xs hidden sm:table-cell">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition-colors">Hapus</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-300">Anda</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
