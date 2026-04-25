<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengobatanLansia extends Model
{
    protected $table = 'pengobatan_lansia';
    public $timestamps = false;

    protected $fillable = [
        'lansia_id', 'tanggal', 'keluhan', 'obat_diberikan',
        'vitamin_diberikan', 'ada_keluhan', 'catatan', 'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'keluhan'          => 'array',
        'obat_diberikan'   => 'array',
        'vitamin_diberikan'=> 'array',
        'ada_keluhan'      => 'boolean',
    ];

    public function lansia()
    {
        return $this->belongsTo(Lansia::class);
    }
}
