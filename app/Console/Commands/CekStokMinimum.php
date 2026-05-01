<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\User;
use App\Notifications\StokMinimumNotification;
use Illuminate\Console\Command;

class CekStokMinimum extends Command
{
    protected $signature   = 'gudang:cek-stok-minimum';
    protected $description = 'Kirim notifikasi email untuk barang yang stoknya mencapai minimum';

    public function handle(): void
    {
        $barangs = Barang::with('stoks')
            ->where('is_active', true)
            ->get()
            ->filter(fn($b) => $b->stoks->sum('stok_akhir') <= $b->stok_minimum)
            ->values();

        if ($barangs->isEmpty()) {
            $this->info('Semua stok aman.');
            return;
        }

        User::whereIn('role', ['admin', 'kepala_gudang'])
            ->where('is_active', true)
            ->get()
            ->each(fn($user) => $user->notify(new StokMinimumNotification($barangs)));

        $this->info("Notifikasi dikirim untuk {$barangs->count()} barang.");
    }
}
