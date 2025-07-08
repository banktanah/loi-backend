<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class ActivateUserAccount extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // Membuat URL verifikasi yang "signed" (aman dan berjangka waktu)
        // URL akan mengarah ke route 'users.activate' yang akan kita buat
        $verificationUrl = URL::temporarySignedRoute(
            'users.activate',
            now()->addMinutes(config('auth.verification.expire', 60)), // Link valid selama 60 menit
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // $verificationUrl = "";

        return (new MailMessage)
                    // --- CUSTOMIZE HERE ---
                    ->subject('Aktivasi Akun Letter of Interest (LOI) Badan Bank Tanah') // Change the subject line
                    ->greeting('Halo ' . $notifiable->nama . ',') // Add a personalized greeting
                    ->line('Terima kasih telah mendaftar di portal Letter of Interest (LOI).') // Change the first line
                    ->line('Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda:') // Change the second line
                    ->action('Aktivasi Akun Saya', $verificationUrl) // Change the button text
                    ->line('Jika Anda merasa tidak mendaftar, Anda dapat mengabaikan email ini.')
                    ->salutation('Hormat kami, Tim LOI Badan Bank Tanah'); // Add a closing salutation
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}