<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Notifications\NewIncidentReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Daftar lokasi area PT Cabot.
     */
    public const LOCATIONS = [
        'Plant Area',
        'Warehouse / Gudang',
        'Loading / Unloading Dock',
        'Tank Farm',
        'Control Room',
        'Laboratory',
        'Workshop / Bengkel',
        'Office Area',
        'Kantin / Mess',
        'Parking Area',
        'Jalan / Area Terbuka',
        'Lainnya',
    ];

    /**
     * Show the public incident reporting form.
     */
    public function create()
    {
        return view('reports.create', [
            'locations' => self::LOCATIONS,
        ]);
    }

    /**
     * Store a new incident report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_type' => ['required', 'in:near_miss,unsafe_act,unsafe_condition,kecelakaan_ringan,kecelakaan_berat,kebakaran,tumpahan_kimia,lainnya'],
            'location' => ['required', 'string', 'max:255'],
            'urgency' => ['required', 'in:rendah,sedang,tinggi,kritis'],
            'description' => ['required', 'string', 'min:10'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'incident_time' => ['nullable', 'date_format:H:i'],
            'photo' => ['nullable', 'image', 'max:5120'], // max 5MB
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_department' => ['nullable', 'string', 'max:255'],
            'reporter_phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('incident-photos', 'public');
        }

        // Create the report — model will enforce anonymity rules
        $report = IncidentReport::create([
            'incident_type' => $validated['incident_type'],
            'location' => $validated['location'],
            'urgency' => $validated['urgency'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'],
            'incident_time' => $validated['incident_time'] ?? null,
            'photo_path' => $photoPath,
            'reporter_name' => $request->reporter_name,
            'reporter_name' => $validated['reporter_name'] ?? null,
            'reporter_department' => $validated['reporter_department'] ?? null,
            'reporter_phone' => $validated['reporter_phone'] ?? null,
        ]);

        // Notify HSE officers
        $hseUsers = User::whereIn('role', ['admin', 'hse_officer'])->get();
        if ($hseUsers->isNotEmpty()) {
            Notification::send($hseUsers, new NewIncidentReport($report));
        }

        return redirect()->route('report.confirmation', $report->tracking_code);
    }

    /**
     * Show confirmation page with tracking code.
     */
    public function confirmation(string $trackingCode)
    {
        $report = IncidentReport::where('tracking_code', $trackingCode)->firstOrFail();

        return view('reports.confirmation', [
            'report' => $report,
        ]);
    }

    /**
     * Show the tracking form.
     */
    public function track()
    {
        return view('reports.track');
    }

    /**
     * Show tracking result.
     */
    public function trackResult(Request $request)
    {
        $validated = $request->validate([
            'tracking_code' => ['required', 'string'],
        ]);

        $report = IncidentReport::where('tracking_code', strtoupper($validated['tracking_code']))->first();

        if (!$report) {
            return back()->withErrors([
                'tracking_code' => 'Kode tracking tidak ditemukan. Pastikan kode yang dimasukkan benar.',
            ])->withInput();
        }

        return view('reports.track-result', [
            'report' => $report,
        ]);
    }
}
