<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_telp',
        'nik',
        'role',
        'profile_image_url',
        'reset_otp_code',
        'reset_otp_expires_at',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'reset_otp_code', // sembunyikan OTP dari response
    ];

    protected $casts = [
        'reset_otp_expires_at' => 'datetime',
    ];

    // =====================
    // RELASI
    // =====================

    // Satu user (orang tua) punya banyak anak
    public function anak()
    {
        return $this->hasMany(Anak::class);
    }

    // User yang membuat jadwal
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'created_by');
    }

    // User yang membuat laporan
    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'created_by');
    }

    // =====================
    // SCOPE
    // =====================

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeOrangtua($query)
    {
        return $query->where('role', 'orangtua');
    }

    public function scopeKader($query)
    {
        return $query->where('role', 'kader');
    }

    public function scopeWaliLansia($query)
    {
        return $query->where('role', 'wali_lansia');
    }

    // =====================
    // HELPERS
    // =====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKader(): bool
    {
        return in_array($this->role, ['admin', 'kader']);
    }

    public function isOrangtua(): bool
    {
        return $this->role === 'orangtua';
    }

    public function isWaliLansia(): bool
    {
        return $this->role === 'wali_lansia';
    }

    public function isMobileUser(): bool
    {
        return in_array($this->role, ['orangtua', 'wali_lansia']);
    }
}
