@extends('layouts.public')
@section('title', 'Laporan Terkirim')

@section('content')
<div class="max-w-lg mx-auto text-center animate-fade-in-up">
    {{-- Success Icon --}}
    <div class="mb-8">
        <div class="w-24 h-24 rounded-full bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3">Laporan Berhasil Dikirim!</h1>
        <p class="text-gray-500 text-sm">Terima kasih atas laporan Anda. Tim HSE akan segera meninjau.</p>
    </div>

    {{-- Tracking Code --}}
    <div class="card-elevated p-6 mb-8">
        <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">Kode Tracking Anda</p>
        <div class="text-3xl sm:text-4xl font-extrabold tracking-wider mb-4 font-mono" style="color: var(--cabot-red);">
            {{ $report->tracking_code }}
        </div>
        <p class="text-xs text-gray-500 mb-4">Simpan kode ini untuk memantau status laporan Anda.</p>
        <button onclick="copyCode()" id="copyBtn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 border border-gray-200 text-sm text-gray-700 hover:text-gray-900 transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span id="copyText">Salin Kode</span>
        </button>
    </div>

    {{-- Report Summary --}}
    <div class="card p-6 mb-8 text-left">
        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4" style="color: var(--cabot-red);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Ringkasan Laporan
        </h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-400">Jenis Kejadian</span>
                <span class="text-gray-800 font-medium">{{ $report->incident_type_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Lokasi</span>
                <span class="text-gray-800">{{ $report->location }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Urgensi</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $report->urgency === 'rendah' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $report->urgency === 'sedang' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $report->urgency === 'tinggi' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ $report->urgency === 'kritis' ? 'bg-red-100 text-red-700' : '' }}
                ">{{ $report->urgency_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Tanggal</span>
                <span class="text-gray-800">{{ $report->incident_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Status</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ $report->status_label }}</span>
            </div>
            @if($report->is_anonymous)
            <div class="flex justify-between">
                <span class="text-gray-400">Pelapor</span>
                <span class="text-gray-500 italic text-xs">🔒 Anonim</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('report.track') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-gray-50 border border-gray-200 text-sm text-gray-700 hover:text-gray-900 transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Lacak Status
        </a>
        <a href="{{ route('report.create') }}" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Laporan Baru
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyCode() {
        navigator.clipboard.writeText('{{ $report->tracking_code }}').then(() => {
            const btn = document.getElementById('copyText');
            btn.textContent = '✓ Tersalin!';
            setTimeout(() => { btn.textContent = 'Salin Kode'; }, 2000);
        });
    }
</script>
@endpush
