@extends('layouts.dashboard')
@section('title', 'Detail Laporan ' . $report->tracking_code)
@section('page-title', 'Detail Laporan')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-700 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Dashboard
    </a>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Header --}}
            <div class="glass-card p-6 animate-fade-in-up">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                    <div>
                        <p class="text-xs text-gray-400 font-mono tracking-wider mb-1">{{ $report->tracking_code }}</p>
                        <h2 class="text-xl font-bold text-gray-900">{{ $report->incident_type_label }}</h2>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold
                            {{ $report->urgency === 'rendah' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $report->urgency === 'sedang' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $report->urgency === 'tinggi' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $report->urgency === 'kritis' ? 'bg-red-100 text-red-700' : '' }}
                        ">{{ $report->urgency_label }}</span>
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold
                            {{ $report->status === 'baru' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $report->status === 'ditinjau' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $report->status === 'dalam_penanganan' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $report->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $report->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                        ">{{ $report->status_label }}</span>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div class="space-y-3">
                        <div><p class="text-xs text-gray-400 mb-0.5">Lokasi</p><p class="text-gray-800">{{ $report->location }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-0.5">Tanggal Kejadian</p><p class="text-gray-800">{{ $report->incident_date->format('d M Y') }} {{ $report->incident_time ? '— ' . $report->incident_time : '' }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-0.5">Dilaporkan</p><p class="text-gray-800">{{ $report->created_at->format('d M Y, H:i') }} ({{ $report->created_at->diffForHumans() }})</p></div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Pelapor</p>
                        <p class="text-sm font-medium text-gray-900">{{ $report->reporter_name }}</p>
                        @if($report->reporter_department)
                            <p class="text-xs text-gray-500">{{ $report->reporter_department }}</p>
                        @endif
                        @if($report->reporter_phone)
                            <p class="text-xs text-gray-500">{{ $report->reporter_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.1s">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Deskripsi Kejadian</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $report->description }}</p>
            </div>

            @if($report->photo_path)
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.15s">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Foto Bukti</h3>
                <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Foto bukti insiden" class="rounded-xl max-h-96 w-auto border border-gray-200">
            </div>
            @endif

            @if($report->resolution_notes)
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.2s">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Catatan Resolusi</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $report->resolution_notes }}</p>
                @if($report->resolved_at)
                <p class="text-xs text-gray-400 mt-2">Diselesaikan: {{ $report->resolved_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
            @endif

            {{-- Audit Trail --}}
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.25s">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Audit Trail</h3>
                <div class="space-y-3">
                    @forelse($report->auditLogs->sortByDesc('created_at') as $log)
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 text-xs font-medium text-gray-500 mt-0.5">
                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-700"><span class="font-medium text-gray-900">{{ $log->user->name }}</span> — {{ $log->action_label }}</p>
                            @if($log->details)
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($log->action === 'status_changed') {{ $log->details['from'] ?? '' }} → {{ $log->details['to'] ?? '' }}
                                @elseif($log->action === 'assigned') Ditugaskan ke: {{ $log->details['assigned_to_name'] ?? '' }}
                                @endif
                            </p>
                            @endif
                            <p class="text-xs text-gray-300 mt-0.5">{{ $log->created_at->format('d/m/Y H:i') }} • {{ $log->ip_address }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            <div class="glass-card p-5 animate-fade-in-up" style="animation-delay: 0.1s">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Update Status</h3>
                <form method="POST" action="{{ route('reports.updateStatus', $report->id) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <select name="status" class="form-input-dash w-full px-3 py-2.5 text-sm">
                        <option value="baru" {{ $report->status === 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="ditinjau" {{ $report->status === 'ditinjau' ? 'selected' : '' }}>Ditinjau</option>
                        <option value="dalam_penanganan" {{ $report->status === 'dalam_penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                        <option value="selesai" {{ $report->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ $report->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <textarea name="resolution_notes" rows="3" placeholder="Catatan resolusi..." class="form-input-dash w-full px-3 py-2.5 text-sm resize-none">{{ $report->resolution_notes }}</textarea>
                    <button type="submit" class="w-full text-white font-medium py-2.5 rounded-lg transition-all duration-300 text-sm" style="background: var(--cabot-red);">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <div class="glass-card p-5 animate-fade-in-up" style="animation-delay: 0.15s">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Tugaskan ke HSE</h3>
                <form method="POST" action="{{ route('reports.assign', $report->id) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <select name="assigned_to" class="form-input-dash w-full px-3 py-2.5 text-sm">
                        <option value="">— Pilih Tim HSE —</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $report->assigned_to == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role_label }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 rounded-lg transition-all duration-300 text-sm">
                        Tugaskan
                    </button>
                </form>
            </div>

            <div class="glass-card p-5 animate-fade-in-up" style="animation-delay: 0.2s">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Informasi</h3>
                <div class="space-y-2 text-xs">
                    @if($report->assignedUser)
                    <div class="flex justify-between"><span class="text-gray-400">Ditangani oleh</span><span class="text-gray-700">{{ $report->assignedUser->name }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-gray-400">Dibuat</span><span class="text-gray-700">{{ $report->created_at->diffForHumans() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Terakhir update</span><span class="text-gray-700">{{ $report->updated_at->diffForHumans() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Dilihat</span><span class="text-gray-700">{{ $report->auditLogs->where('action', 'viewed')->count() }}x</span></div>
                </div>
            </div>

            <div class="pt-2">
                <form method="POST" action="{{ route('reports.destroy', $report->id) }}" id="deleteForm">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete(this)" class="w-full text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 font-medium py-2.5 rounded-lg transition-all duration-300 text-sm">
                        Hapus Laporan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(btn) {
    if (btn.dataset.ready === 'true') {
        btn.innerHTML = 'Menghapus...';
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        document.getElementById('deleteForm').submit();
    } else {
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = 'Yakin Hapus? (Klik lagi)';
        
        // Ubah warna jadi merah solid
        btn.classList.remove('text-red-500', 'hover:text-red-700', 'bg-red-50', 'hover:bg-red-100');
        btn.classList.add('text-white', 'bg-red-600', 'hover:bg-red-700');
        btn.dataset.ready = 'true';
        
        setTimeout(() => {
            if(btn.dataset.ready === 'true') {
                btn.innerHTML = btn.dataset.originalText;
                btn.classList.remove('text-white', 'bg-red-600', 'hover:bg-red-700');
                btn.classList.add('text-red-500', 'hover:text-red-700', 'bg-red-50', 'hover:bg-red-100');
                btn.dataset.ready = 'false';
            }
        }, 3000);
    }
}
</script>
@endsection
