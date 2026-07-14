@extends('layouts.dashboard')
@section('title', 'Dashboard HSE')
@section('page-title', 'Dashboard')

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="kpi-card p-5 animate-fade-in-up" style="animation-delay: 0.05s">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 animate-count">{{ $kpi['total'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Laporan</p>
    </div>

    <div class="kpi-card p-5 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-amber-600 animate-count">{{ $kpi['baru'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Belum Ditangani</p>
    </div>

    <div class="kpi-card p-5 animate-fade-in-up" style="animation-delay: 0.15s">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-purple-600 animate-count">{{ $kpi['dalam_proses'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Dalam Proses</p>
    </div>

    <div class="kpi-card p-5 animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-emerald-600 animate-count">{{ $kpi['selesai'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Selesai</p>
    </div>

    <div class="kpi-card p-5 animate-fade-in-up {{ $kpi['kritis'] > 0 ? 'border-red-200 bg-red-50' : '' }}" style="animation-delay: 0.25s">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-red-600 animate-count">{{ $kpi['kritis'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Kritis Aktif</p>
    </div>
</div>

{{-- Monthly Trend --}}
<div class="glass-card p-5 mb-8 animate-fade-in-up" style="animation-delay: 0.3s">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Tren Laporan (6 Bulan Terakhir)</h3>
    <div class="flex items-end gap-3 h-32">
        @php 
            $counts = array_column($monthlyTrend, 'count');
            $maxCount = count($counts) > 0 ? max($counts) : 1;
            if ($maxCount == 0) $maxCount = 1;
        @endphp
        @foreach($monthlyTrend as $month)
        <div class="flex-1 flex flex-col items-center gap-2">
            <span class="text-xs font-medium text-gray-700">{{ $month['count'] }}</span>
            <div class="w-full rounded-t-lg transition-all duration-500 hover:opacity-80"
                 style="height: {{ $maxCount > 0 ? max(($month['count'] / $maxCount) * 100, 4) : 4 }}%; background: var(--cabot-red);"></div>
            <span class="text-xs text-gray-400 truncate w-full text-center">{{ $month['month'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Filters --}}
<div class="glass-card p-4 mb-6 animate-fade-in-up" style="animation-delay: 0.35s">
    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs text-gray-400 mb-1 block">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode tracking, deskripsi, lokasi..." class="form-input-dash w-full px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs text-gray-400 mb-1 block">Jenis</label>
            <select name="type" class="form-input-dash px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="near_miss" {{ request('type') === 'near_miss' ? 'selected' : '' }}>Near Miss</option>
                <option value="unsafe_act" {{ request('type') === 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                <option value="unsafe_condition" {{ request('type') === 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                <option value="kecelakaan_ringan" {{ request('type') === 'kecelakaan_ringan' ? 'selected' : '' }}>Kecelakaan Ringan</option>
                <option value="kecelakaan_berat" {{ request('type') === 'kecelakaan_berat' ? 'selected' : '' }}>Kecelakaan Berat</option>
                <option value="kebakaran" {{ request('type') === 'kebakaran' ? 'selected' : '' }}>Kebakaran</option>
                <option value="tumpahan_kimia" {{ request('type') === 'tumpahan_kimia' ? 'selected' : '' }}>Tumpahan Kimia</option>
                <option value="lainnya" {{ request('type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-400 mb-1 block">Urgensi</label>
            <select name="urgency" class="form-input-dash px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="rendah" {{ request('urgency') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                <option value="sedang" {{ request('urgency') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="tinggi" {{ request('urgency') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="kritis" {{ request('urgency') === 'kritis' ? 'selected' : '' }}>Kritis</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-400 mb-1 block">Status</label>
            <select name="status" class="form-input-dash px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                <option value="ditinjau" {{ request('status') === 'ditinjau' ? 'selected' : '' }}>Ditinjau</option>
                <option value="dalam_penanganan" {{ request('status') === 'dalam_penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm font-medium transition-all duration-300" style="background: var(--cabot-red);">
            Filter
        </button>
        @if(request()->hasAny(['search', 'type', 'urgency', 'status']))
        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm transition-all duration-300">
            Reset
        </a>
        @endif
    </form>
</div>

{{-- Reports Table --}}
<div class="glass-card overflow-hidden animate-fade-in-up" style="animation-delay: 0.4s">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Lokasi</th>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Urgensi</th>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Pelapor</th>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Tanggal</th>
                    <th class="px-5 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-5 py-4">
                        <span class="font-mono text-xs font-semibold" style="color: var(--cabot-red);">{{ $report->tracking_code }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-gray-700 text-xs">{{ $report->incident_type_label }}</span>
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="text-gray-500 text-xs">{{ Str::limit($report->location, 20) }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $report->urgency === 'rendah' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $report->urgency === 'sedang' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $report->urgency === 'tinggi' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $report->urgency === 'kritis' ? 'bg-red-100 text-red-700' : '' }}
                        ">{{ $report->urgency_label }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $report->status === 'baru' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $report->status === 'ditinjau' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $report->status === 'dalam_penanganan' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $report->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $report->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                        ">{{ $report->status_label }}</span>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell">
                        <span class="text-xs text-gray-700">{{ $report->reporter_name }}</span>
                    </td>
                    <td class="px-5 py-4 hidden lg:table-cell">
                        <span class="text-xs text-gray-400">{{ $report->created_at->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('reports.show', $report->id) }}" class="text-xs font-medium opacity-0 group-hover:opacity-100 transition-opacity" style="color: var(--cabot-red);">
                            Lihat →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm text-gray-400">Belum ada laporan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection
