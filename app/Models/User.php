<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['username', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['is_active' => 'boolean'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isKepalaGudang(): bool { return $this->role === 'kepala_gudang'; }
    public function isOperator(): bool { return $this->role === 'operator'; }

    /**
     * Route notifications for the mail channel.
     * Username digunakan sebagai alamat email karena kolom email tidak ada di tabel users.
     * Ganti dengan kolom email nyata jika tersedia.
     */
    public function routeNotificationForMail(): string
    {
        return $this->username;
    }

    public function transaksis() { return $this->hasMany(Transaksi::class, 'id_user'); }
    public function laporans() { return $this->hasMany(Laporan::class, 'id_user'); }
}
