@extends('layouts.public')
@section('title', 'Status Laporan ' . $report->tracking_code)

@section('content')
<div class="max-w-lg mx-auto animate-fade-in-up">
    <div class="text-center mb-8">
        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Status Laporan</p>
        <h1 class="text-2xl font-extrabold text-gray-900 font-mono tracking-wider">{{ $report->tracking_code }}</h1>
    </div>

    {{-- Status Timeline --}}
    <div class="card-elevated p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-5">Progress Penanganan</h3>
        @php
            $steps = ['baru', 'ditinjau', 'dalam_penanganan', 'selesai'];
            $stepLabels = ['Baru', 'Ditinjau', 'Dalam Penanganan', 'Selesai'];
            $stepIcons = ['📩', '👁️', '🔧', '✅'];
            $currentIndex = $report->status === 'ditolak' ? -1 : array_search($report->status, $steps);
        @endphp

        @if($report->status === 'ditolak')
        <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-200 mb-5">
            <span class="text-lg">❌</span>
            <div>
                <p class="text-sm font-medium text-red-700">Laporan Ditolak</p>
                @if($report->resolution_notes)
                <p class="text-xs text-gray-500 mt-1">{{ $report->resolution_notes }}</p>
                @endif
            </div>
        </div>
        @endif

        <div class="space-y-4">
            @foreach($steps as $i => $step)
            @php
                $isCompleted = $currentIndex !== false && $i <= $currentIndex;
                $isCurrent = $i === $currentIndex;
            @endphp
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    {{ $isCompleted ? 'bg-emerald-50 border-2 border-emerald-300' : 'bg-gray-100 border border-gray-200' }}
                    {{ $isCurrent ? 'ring-2 ring-emerald-200 ring-offset-2' : '' }}">
                    @if($isCompleted)
                        <span class="text-sm">{{ $stepIcons[$i] }}</span>
                    @else
                        <span class="text-xs text-gray-400 font-medium">{{ $i + 1 }}</span>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">{{ $stepLabels[$i] }}</p>
                    @if($isCurrent)
                        <p class="text-xs text-emerald-600 font-medium">Status saat ini</p>
                    @endif
                </div>
                @if($isCompleted)
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            @if($i < count($steps) - 1)
            <div class="ml-5 w-px h-4 {{ $isCompleted && $i < $currentIndex ? 'bg-emerald-300' : 'bg-gray-200' }}"></div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Report Info --}}
    <div class="card p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Detail Laporan</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-400">Jenis</span>
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
                <span class="text-gray-400">Tanggal Kejadian</span>
                <span class="text-gray-800">{{ $report->incident_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Dilaporkan</span>
                <span class="text-gray-800">{{ $report->created_at->diffForHumans() }}</span>
            </div>
            @if($report->assigned_to && $report->assignedUser)
            <div class="flex justify-between">
                <span class="text-gray-400">Ditangani oleh</span>
                <span class="text-gray-800">{{ $report->assignedUser->name }}</span>
            </div>
            @endif
            @if($report->resolution_notes && $report->status === 'selesai')
            <div class="pt-3 border-t border-gray-100">
                <p class="text-gray-400 mb-1">Catatan Resolusi:</p>
                <p class="text-gray-700 text-xs">{{ $report->resolution_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('report.track') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white hover:bg-gray-50 border border-gray-200 text-sm text-gray-700 transition-all duration-300">
            Lacak Laporan Lain
        </a>
        <a href="/" class="btn-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm">
            Buat Laporan Baru
        </a>
    </div>
</div>
@endsection
