<?php

namespace App\Notifications;

use App\Models\Investment;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class InvestmentSubmitted extends Notification implements ShouldQueue // Implements ShouldQueue agar pengiriman email tidak menghambat response
{
    use Queueable;

    protected $investment;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param Investment $investment
     * @param User $user
     * @return void
     */
    public function __construct(Investment $investment, User $user)
    {
        $this->investment = $investment;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Membuat Tracking ID kustom
        $trackingId = sprintf(
            '%s/%s/%s/%s',
            $this->investment->investment_id,
            $this->user->id,
            $this->investment->perolehan_id,
            now()->year
        );

        $mailMessage = (new MailMessage)
            ->subject('Konfirmasi Pengajuan Minat Investasi')
            ->greeting('Halo, ' . Str::words($this->user->investor->name, 2, '') . '!') // Ambil nama dari profil investor
            ->line('Terima kasih, pengajuan minat investasi Anda telah berhasil kami terima. Berikut adalah detail pengajuan Anda:')
            ->line('**Nomor Pelacakan (Tracking ID):** ' . $trackingId)
            ->line('**Status Saat Ini:** ' . $this->investment->status)
            ->line('---')
            ->line('**Detail Aset yang Diminati:**')
            ->line('**Nama Lokasi:** ' . $this->investment->site_name)
            ->line('**ID Perolehan Aset:** ' . $this->investment->perolehan_id)
            ->line('---')
            ->line('**Rencana Proyek Anda:**')
            ->line('**Tujuan:** ' . $this->investment->tujuan_pemanfaatan)
            ->line('**Skema:** ' . $this->investment->skema_pemanfaatan)
            ->line('---')
            ->line('Tim kami akan segera melakukan verifikasi terhadap pengajuan Anda. Anda akan menerima notifikasi lebih lanjut mengenai perkembangan status peminatan Anda.')
            ->action('Lihat Detail Peminatan', url('/dashboard/investments/' . $this->investment->investment_id)) // Ganti dengan URL frontend Anda
            ->line('Terima kasih telah menggunakan layanan kami.');

        return $mailMessage;
    }
}