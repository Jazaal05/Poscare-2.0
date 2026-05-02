<?php

namespace App\Events;

use App\Models\RiwayatPengukuran;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PengukuranRecorded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public RiwayatPengukuran $pengukuran;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RiwayatPengukuran $pengukuran)
    {
        $this->pengukuran = $pengukuran;
    }
}