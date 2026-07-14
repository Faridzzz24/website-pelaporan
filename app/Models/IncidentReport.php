<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class IncidentReport extends Model
{
    protected $fillable = [
        'tracking_code',
        'incident_type',
        'location',
        'urgency',
        'description',
        'incident_date',
        'incident_time',
        'photo_path',
        'is_anonymous',
        'reporter_name',
        'reporter_department',
        'reporter_phone',
        'status',
        'assigned_to',
        'resolution_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'incident_date' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Auto-generate tracking code before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (IncidentReport $report) {
            if (empty($report->tracking_code)) {
                $report->tracking_code = static::generateTrackingCode();
            }

            // Enforce true anonymity: strip identity fields if anonymous
            if ($report->is_anonymous) {
                $report->reporter_name = null;
                $report->reporter_department = null;
                $report->reporter_phone = null;
            }
        });
    }

    /**
     * Generate a unique tracking code in format CBT-YYYY-XXXXX
     */
    public static function generateTrackingCode(): string
    {
        $year = date('Y');
        $lastReport = static::where('tracking_code', 'like', "CBT-{$year}-%")
            ->orderByDesc('id')
            ->first();

        if ($lastReport) {
            $lastNumber = (int) substr($lastReport->tracking_code, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('CBT-%s-%05d', $year, $nextNumber);
    }

    /**
     * Get the user this report is assigned to.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get audit logs for this report.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('incident_type', $type);
    }

    public function scopeByUrgency(Builder $query, string $urgency): Builder
    {
        return $query->where('urgency', $urgency);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('incident_date', [$from, $to]);
    }

    // ── Helpers ─────────────────────────────────────────────

    /**
     * Human-readable incident type label.
     */
    public function getIncidentTypeLabelAttribute(): string
    {
        return match ($this->incident_type) {
            'near_miss' => 'Near Miss',
            'unsafe_act' => 'Unsafe Act',
            'unsafe_condition' => 'Unsafe Condition',
            'kecelakaan_ringan' => 'Kecelakaan Ringan',
            'kecelakaan_berat' => 'Kecelakaan Berat',
            'kebakaran' => 'Kebakaran',
            'tumpahan_kimia' => 'Tumpahan Bahan Kimia',
            'lainnya' => 'Lainnya',
            default => $this->incident_type,
        };
    }

    /**
     * Human-readable urgency label.
     */
    public function getUrgencyLabelAttribute(): string
    {
        return match ($this->urgency) {
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'kritis' => 'Kritis',
            default => $this->urgency,
        };
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Baru',
            'ditinjau' => 'Ditinjau',
            'dalam_penanganan' => 'Dalam Penanganan',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    /**
     * CSS color class for urgency badge.
     */
    public function getUrgencyColorAttribute(): string
    {
        return match ($this->urgency) {
            'rendah' => 'bg-emerald-500/20 text-emerald-400',
            'sedang' => 'bg-amber-500/20 text-amber-400',
            'tinggi' => 'bg-orange-500/20 text-orange-400',
            'kritis' => 'bg-red-500/20 text-red-400',
            default => 'bg-gray-500/20 text-gray-400',
        };
    }

    /**
     * CSS color class for status badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'bg-blue-500/20 text-blue-400',
            'ditinjau' => 'bg-purple-500/20 text-purple-400',
            'dalam_penanganan' => 'bg-amber-500/20 text-amber-400',
            'selesai' => 'bg-emerald-500/20 text-emerald-400',
            'ditolak' => 'bg-red-500/20 text-red-400',
            default => 'bg-gray-500/20 text-gray-400',
        };
    }
}
