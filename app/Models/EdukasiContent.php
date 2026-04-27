<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdukasiContent extends Model
{
    protected $table = 'edukasi_content';

    public $timestamps = false;

    protected $fillable = [
        'platform',
        'url',
        'title',
        'category',
        'thumbnail',
        'duration',
        'penulis_id',
        'layanan',
    ];

    // =====================
    // RELASI
    // =====================

    // Konten dibuat oleh satu user
    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // =====================
    // SCOPE
    // =====================

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
