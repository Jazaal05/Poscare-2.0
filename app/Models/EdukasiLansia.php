<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Edukasi Lansia - Konten Edukasi
 * 
 * Menyimpan konten edukasi dari berbagai platform.
 * Validasi URL sesuai platform yang dipilih.
 */
class EdukasiLansia extends Model
{
    protected $table = 'edukasi_lansia';

    protected $fillable = [
        'judul',
        'deskripsi',
        'platform',
        'tautan',
        'thumbnail',
        'kategori',
        'dibuat_oleh',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ============================================================
    // RELASI
    // ============================================================

    /**
     * Relasi ke User (pembuat konten)
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope untuk konten aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan platform
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope untuk konten terbaru
     */
    public function scopeTerbaru($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Accessor untuk platform badge
     */
    public function getPlatformBadgeAttribute(): array
    {
        return match($this->platform) {
            'Youtube' => [
                'label' => 'YouTube',
                'color' => '#FF0000',
                'bg' => '#FEE2E2',
                'icon' => 'fab fa-youtube',
            ],
            'Tiktok' => [
                'label' => 'TikTok',
                'color' => '#000000',
                'bg' => '#F3F4F6',
                'icon' => 'fab fa-tiktok',
            ],
            'Facebook' => [
                'label' => 'Facebook',
                'color' => '#1877F2',
                'bg' => '#DBEAFE',
                'icon' => 'fab fa-facebook',
            ],
            'Instagram' => [
                'label' => 'Instagram',
                'color' => '#E4405F',
                'bg' => '#FCE7F3',
                'icon' => 'fab fa-instagram',
            ],
            'Artikel' => [
                'label' => 'Artikel',
                'color' => '#10B981',
                'bg' => '#D1FAE5',
                'icon' => 'fas fa-newspaper',
            ],
            default => [
                'label' => '-',
                'color' => '#9CA3AF',
                'bg' => '#F3F4F6',
                'icon' => 'fas fa-link',
            ],
        };
    }

    /**
     * Accessor untuk kategori badge
     */
    public function getKategoriBadgeAttribute(): array
    {
        return match($this->kategori) {
            'Kesehatan Lansia' => [
                'label' => 'Kesehatan Lansia',
                'color' => '#10B981',
                'bg' => '#D1FAE5',
            ],
            'Pola Hidup Sehat' => [
                'label' => 'Pola Hidup Sehat',
                'color' => '#3B82F6',
                'bg' => '#DBEAFE',
            ],
            'Pencegahan Penyakit' => [
                'label' => 'Pencegahan Penyakit',
                'color' => '#F59E0B',
                'bg' => '#FEF3C7',
            ],
            'Gizi Lansia' => [
                'label' => 'Gizi Lansia',
                'color' => '#8B5CF6',
                'bg' => '#EDE9FE',
            ],
            'Olahraga Lansia' => [
                'label' => 'Olahraga Lansia',
                'color' => '#EF4444',
                'bg' => '#FEE2E2',
            ],
            'Tips Lansia' => [
                'label' => 'Tips Lansia',
                'color' => '#06B6D4',
                'bg' => '#CFFAFE',
            ],
            'Lainnya' => [
                'label' => 'Lainnya',
                'color' => '#6B7280',
                'bg' => '#F3F4F6',
            ],
            default => [
                'label' => '-',
                'color' => '#9CA3AF',
                'bg' => '#F3F4F6',
            ],
        };
    }

    /**
     * Accessor untuk embed URL (untuk YouTube)
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->platform !== 'Youtube') {
            return null;
        }

        // Extract video ID from YouTube URL
        $videoId = $this->extractYoutubeVideoId($this->tautan);
        
        if (!$videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}";
    }

    // ============================================================
    // STATIC METHODS - VALIDASI URL
    // ============================================================

    /**
     * Validasi URL sesuai platform
     */
    public static function validateUrlForPlatform(string $url, string $platform): bool
    {
        return match($platform) {
            'Youtube' => str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be'),
            'Tiktok' => str_contains($url, 'tiktok.com'),
            'Facebook' => str_contains($url, 'facebook.com') || str_contains($url, 'fb.com'),
            'Instagram' => str_contains($url, 'instagram.com'),
            'Artikel' => filter_var($url, FILTER_VALIDATE_URL) !== false,
            default => false,
        };
    }

    /**
     * Get pesan error validasi URL
     */
    public static function getValidationMessage(string $platform): string
    {
        return match($platform) {
            'Youtube' => 'Tautan harus dari YouTube (youtube.com atau youtu.be)',
            'Tiktok' => 'Tautan harus dari TikTok (tiktok.com)',
            'Facebook' => 'Tautan harus dari Facebook (facebook.com atau fb.com) dan konten harus publik',
            'Instagram' => 'Tautan harus dari Instagram (instagram.com) dan konten harus publik',
            'Artikel' => 'Tautan harus berupa URL yang valid',
            default => 'Tautan tidak valid',
        };
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Extract YouTube video ID dari URL
     */
    private function extractYoutubeVideoId(string $url): ?string
    {
        // Pattern untuk youtube.com/watch?v=VIDEO_ID
        if (preg_match('/[?&]v=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Pattern untuk youtu.be/VIDEO_ID
        if (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Pattern untuk youtube.com/embed/VIDEO_ID
        if (preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Auto-fetch thumbnail dari YouTube
     */
    public function fetchYoutubeThumbnail(): ?string
    {
        if ($this->platform !== 'Youtube') {
            return null;
        }

        $videoId = $this->extractYoutubeVideoId($this->tautan);
        
        if (!$videoId) {
            return null;
        }

        // YouTube thumbnail URL
        return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
    }

    /**
     * Aktifkan konten
     */
    public function aktifkan(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Nonaktifkan konten
     */
    public function nonaktifkan(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Toggle status aktif
     */
    public function toggleAktif(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Cek apakah URL valid untuk platform
     */
    public function isUrlValid(): bool
    {
        return self::validateUrlForPlatform($this->tautan, $this->platform);
    }
}
