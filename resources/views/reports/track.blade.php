@extends('layouts.public')
@section('title', 'Lacak Laporan')

@section('content')
<div class="max-w-md mx-auto animate-fade-in-up">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 border border-gray-200 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Lacak Status Laporan</h1>
        <p class="text-gray-500 text-sm">Masukkan kode tracking yang Anda terima saat mengirim laporan.</p>
    </div>

    <form method="POST" action="{{ route('report.track.result') }}" class="card-elevated p-6 space-y-5">
        @csrf
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Kode Tracking</label>
            <input type="text" name="tracking_code" value="{{ old('tracking_code') }}" placeholder="CBT-2026-00001" class="form-input w-full px-4 py-3.5 text-center text-lg font-mono tracking-wider uppercase" required autofocus>
            @error('tracking_code')
                <p class="text-red-500 text-xs text-center">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn-primary w-full py-3.5 text-sm">
            <svg class="w-4 h-4 inline mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari Laporan
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="/" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">← Kembali ke Form Pelaporan</a>
    </div>
</div>
@endsection
