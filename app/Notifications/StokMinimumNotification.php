<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class StokMinimumNotification extends Notification
{
    public function __construct(private Collection $barangs) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('[Gudang GCW] Peringatan Stok Minimum')
            ->greeting('Halo ' . $notifiable->username . ',')
            ->line('Berikut barang yang stoknya mencapai batas minimum:');

        foreach ($this->barangs as $barang) {
            $totalStok = $barang->stoks->sum('stok_akhir');
            $message->line("• {$barang->nama_barang} ({$barang->merk}) — Stok: {$totalStok}, Minimum: {$barang->stok_minimum}");
        }

        return $message
            ->line('Segera lakukan pengadaan barang.')
            ->salutation('Salam, Sistem Gudang GCW');
    }
}
