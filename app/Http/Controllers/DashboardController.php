<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard utama dengan KPI dan daftar laporan.
     */
    public function index(Request $request)
    {
        $query = IncidentReport::query()->oldest();

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        if ($request->filled('urgency')) {
            $query->byUrgency($request->urgency);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->byDateRange($request->from, $request->to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(15)->withQueryString();

        // KPI data
        $kpi = [
            'total' => IncidentReport::count(),
            'baru' => IncidentReport::where('status', 'baru')->count(),
            'dalam_proses' => IncidentReport::whereIn('status', ['ditinjau', 'dalam_penanganan'])->count(),
            'selesai' => IncidentReport::where('status', 'selesai')->count(),
            'kritis' => IncidentReport::where('urgency', 'kritis')->where('status', '!=', 'selesai')->count(),
        ];

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyTrend[] = [
                'month' => $date->translatedFormat('M Y'),
                'count' => IncidentReport::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return view('dashboard.index', compact('reports', 'kpi', 'monthlyTrend'));
    }

    /**
     * Detail laporan dengan audit trail.
     */
    public function show(Request $request, int $id)
    {
        $report = IncidentReport::with(['assignedUser', 'auditLogs.user'])->findOrFail($id);
        $users = User::where('role', 'hse_officer')->get();

        // Log that this user viewed the report
        AuditLog::create([
            'user_id' => $request->user()->id,
            'incident_report_id' => $report->id,
            'action' => 'viewed',
            'ip_address' => $request->ip(),
        ]);

        return view('dashboard.show', compact('report', 'users'));
    }

    /**
     * Update status laporan.
     */
    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,ditinjau,dalam_penanganan,selesai,ditolak'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $report = IncidentReport::findOrFail($id);
        $oldStatus = $report->status;

        $report->update([
            'status' => $validated['status'],
            'resolution_notes' => $validated['resolution_notes'] ?? $report->resolution_notes,
            'resolved_at' => in_array($validated['status'], ['selesai', 'ditolak']) ? now() : $report->resolved_at,
        ]);

        // Audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'incident_report_id' => $report->id,
            'action' => 'status_changed',
            'details' => [
                'from' => $oldStatus,
                'to' => $validated['status'],
            ],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    /**
     * Assign laporan ke petugas.
     */
    public function assign(Request $request, int $id)
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $report = IncidentReport::findOrFail($id);
        $report->update(['assigned_to' => $validated['assigned_to']]);

        $assignedUser = User::find($validated['assigned_to']);

        // Audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'incident_report_id' => $report->id,
            'action' => 'assigned',
            'details' => [
                'assigned_to_name' => $assignedUser->name,
                'assigned_to_id' => $assignedUser->id,
            ],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Laporan ditugaskan ke {$assignedUser->name}.");
    }

    /**
     * Export laporan ke CSV.
     */
    public function export(Request $request)
    {
        $query = IncidentReport::query()->oldest();

        // Apply same filters as index
        if ($request->filled('type')) $query->byType($request->type);
        if ($request->filled('urgency')) $query->byUrgency($request->urgency);
        if ($request->filled('status')) $query->byStatus($request->status);

        $reports = $query->get();

        // Log export action
        AuditLog::create([
            'user_id' => $request->user()->id,
            'incident_report_id' => $reports->first()?->id ?? 0,
            'action' => 'exported',
            'details' => [
                'count' => $reports->count(),
                'format' => $request->query('format', 'csv')
            ],
            'ip_address' => $request->ip(),
        ]);

        $format = $request->query('format', 'csv');
        $dateStr = now()->format('Y-m-d');

        if ($format === 'pdf') {
            return response()->view('exports.reports', compact('reports', 'format'));
        }

        if ($format === 'word') {
            $headers = [
                'Content-Type' => 'application/vnd.ms-word',
                'Content-Disposition' => "attachment; filename=\"laporan-insiden-{$dateStr}.doc\"",
            ];
            return response()->view('exports.reports', compact('reports', 'format'), 200, $headers);
        }

        // Default to CSV
        $filename = "laporan-insiden-{$dateStr}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Tracking Code', 'Jenis Kejadian', 'Lokasi', 'Urgensi',
                'Deskripsi', 'Tanggal', 'Waktu', 'Status',
                'Pelapor', 'Departemen', 'Dibuat',
            ]);

            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->tracking_code,
                    $report->incident_type_label,
                    $report->location,
                    $report->urgency_label,
                    $report->description,
                    $report->incident_date->format('d/m/Y'),
                    $report->incident_time,
                    $report->status_label,
                    $report->reporter_name,
                    $report->reporter_department,
                    $report->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Hapus laporan.
     */
    public function destroy(Request $request, int $id)
    {
        $report = IncidentReport::findOrFail($id);
        
        // Hapus foto jika ada
        if ($report->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($report->photo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($report->photo_path);
        }
        
        $report->delete();

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dihapus secara permanen.');
    }
}
