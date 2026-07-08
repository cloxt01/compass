<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil')
            ->greeting('Halo, ' . $notifiable->name . ' 👋')
            ->line('Pembayaran subscription Anda berhasil kami terima.')
            ->line('Invoice: ' . $this->invoice->invoice_number)
            ->line('Total: Rp ' . number_format($this->invoice->total, 0, ',', '.'))
            ->line('Tanggal Pembayaran: ' . now()->format('d M Y H:i'))
            ->action(
                'Lihat Invoice',
                route('billing.invoice.show', $this->invoice)
            )
            ->line('Terima kasih telah menggunakan ' . config('app.name') . '.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
        ];
    }
}
