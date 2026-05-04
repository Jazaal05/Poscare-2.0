<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Jadwal Lansia - Jadwal Kegiatan Posyandu
 * 
 * Menyimpan jadwal kegiatan posyandu lansia.
 * Format waktu: 24 jam (HH:mm), tanggal tidak boleh masa lalu.
 */
class JadwalLansia extends Model
{
    protected $table = 'jadwal_lansia';

    protected $fillable = [
        'judul_kegiatan',
        'deskripsi',
        'tanggal',
        'waktu_mulai',
        'lokasi',
        'jenis_kegiatan',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ============================================================
    // RELASI
    // ============================================================

    /**
     * Relasi ke User (pembuat jadwal)
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope untuk jadwal yang akan datang
     */
    public function scopeAkanDatang($query)
    {
        return $query->where('tanggal', '>=', now()->toDateString())
            ->where('status', 'dijadwalkan')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc');
    }

    /**
     * Scope untuk jadwal hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    /**
     * Scope untuk jadwal bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);
    }

    /**
     * Scope untuk jadwal yang sudah terlaksana
     */
    public function scopeTerlaksana($query)
    {
        return $query->where('status', 'terlaksana');
    }

    /**
     * Scope untuk jadwal yang dibatalkan
     */
    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    /**
     * Scope untuk filter berdasarkan jenis kegiatan
     */
    public function scopeByJenisKegiatan($query, string $jenis)
    {
        return $query->where('jenis_kegiatan', $jenis);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Accessor untuk waktu mulai display (format: 08:00)
     */
    public function getWaktuMulaiDisplayAttribute(): string
    {
        return substr($this->waktu_mulai, 0, 5); // HH:mm
    }

    /**
     * Accessor untuk tanggal display (format: Senin, 4 Mei 2026)
     */
    public function getTanggalDisplayAttribute(): string
    {
        return $this->tanggal->isoFormat('dddd, D MMMM YYYY');
    }

    /**
     * Accessor untuk tanggal singkat (format: 4 Mei 2026)
     */
    public function getTanggalSingkatAttribute(): string
    {
        return $this->tanggal->isoFormat('D MMMM YYYY');
    }

    /**
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'dijadwalkan' => [
                'label' => 'Dijadwalkan',
                'color' => '#3B82F6',
                'bg' => '#DBEAFE',
            ],
            'terlaksana' => [
                'label' => 'Terlaksana',
                'color' => '#10B981',
                'bg' => '#D1FAE5',
            ],
            'dibatalkan' => [
                'label' => 'Dibatalkan',
                'color' => '#EF4444',
                'bg' => '#FEE2E2',
            ],
            default => [
                'label' => '-',
                'color' => '#9CA3AF',
                'bg' => '#F3F4F6',
            ],
        };
    }

    /**
     * Accessor untuk jenis kegiatan badge
     */
    public function getJenisKegiatanBadgeAttribute(): array
    {
        return match($this->jenis_kegiatan) {
            'Posyandu' => [
                'label' => 'Posyandu',
                'color' => '#10B981',
                'bg' => '#D1FAE5',
                'icon' => 'fa-hospital',
            ],
            'Senam Lansia' => [
                'label' => 'Senam Lansia',
                'color' => '#F59E0B',
                'bg' => '#FEF3C7',
                'icon' => 'fa-running',
            ],
            'Penyuluhan' => [
                'label' => 'Penyuluhan',
                'color' => '#3B82F6',
                'bg' => '#DBEAFE',
                'icon' => 'fa-chalkboard-teacher',
            ],
            'Pemeriksaan Kesehatan' => [
                'label' => 'Pemeriksaan Kesehatan',
                'color' => '#8B5CF6',
                'bg' => '#EDE9FE',
                'icon' => 'fa-stethoscope',
            ],
            'Lainnya' => [
                'label' => 'Lainnya',
                'color' => '#6B7280',
                'bg' => '#F3F4F6',
                'icon' => 'fa-calendar',
            ],
            default => [
                'label' => '-',
                'color' => '#9CA3AF',
                'bg' => '#F3F4F6',
                'icon' => 'fa-question',
            ],
        };
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Cek apakah jadwal sudah lewat
     */
    public function isSudahLewat(): bool
    {
        return $this->tanggal->isPast();
    }

    /**
     * Cek apakah jadwal hari ini
     */
    public function isHariIni(): bool
    {
        return $this->tanggal->isToday();
    }

    /**
     * Cek apakah jadwal besok
     */
    public function isBesok(): bool
    {
        return $this->tanggal->isTomorrow();
    }

    /**
     * Tandai jadwal sebagai terlaksana
     */
    public function tandaiTerlaksana(): bool
    {
        return $this->update(['status' => 'terlaksana']);
    }

    /**
     * Batalkan jadwal
     */
    public function batalkan(): bool
    {
        return $this->update(['status' => 'dibatalkan']);
    }

    /**
     * Get sisa hari hingga jadwal
     */
    public function getSisaHariAttribute(): int
    {
        if ($this->tanggal->isPast()) {
            return 0;
        }
        
        return now()->diffInDays($this->tanggal, false);
    }

    /**
     * Get countdown text
     */
    public function getCountdownTextAttribute(): string
    {
        $sisaHari = $this->sisa_hari;

        if ($sisaHari < 0) {
            return 'Sudah lewat';
        }

        if ($sisaHari === 0) {
            return 'Hari ini';
        }

        if ($sisaHari === 1) {
            return 'Besok';
        }

        return "$sisaHari hari lagi";
    }
}
