<?php

namespace App\Notifications;

use App\Models\IncidentReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewIncidentReport extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected IncidentReport $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urgencyEmoji = match ($this->report->urgency) {
            'kritis' => '🔴',
            'tinggi' => '🟠',
            'sedang' => '🟡',
            'rendah' => '🟢',
            default => '⚪',
        };

        return (new MailMessage)
            ->subject("{$urgencyEmoji} Laporan Insiden Baru — {$this->report->tracking_code}")
            ->greeting('Laporan Insiden K3 Baru')
            ->line("**Kode Tracking:** {$this->report->tracking_code}")
            ->line("**Jenis:** {$this->report->incident_type_label}")
            ->line("**Lokasi:** {$this->report->location}")
            ->line("**Urgensi:** {$urgencyEmoji} {$this->report->urgency_label}")
            ->line("**Tanggal Kejadian:** {$this->report->incident_date->format('d M Y')}")
            ->line('')
            ->line("**Deskripsi:**")
            ->line($this->report->description)
            ->action('Lihat di Dashboard', url('/dashboard/reports/' . $this->report->id))
            ->line('Laporan ini membutuhkan perhatian Anda segera.');
    }
}
