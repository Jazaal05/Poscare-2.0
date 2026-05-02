<?php

namespace App\Listeners;

use App\Events\PengukuranRecorded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateAnakStatusGizi implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\PengukuranRecorded  $event
     * @return void
     */
    public function handle(PengukuranRecorded $event)
    {
        $pengukuran = $event->pengukuran;
        $anak = $pengukuran->anak;

        if ($anak) {
            // Update snapshot data di tabel anak
            $anak->update([
                'berat_badan' => $pengukuran->bb_kg,
                'tinggi_badan' => $pengukuran->tb_pb_cm,
                'lingkar_kepala' => $pengukuran->lk_cm,
                'cara_ukur' => $pengukuran->cara_ukur,
                'status_gizi' => $pengukuran->overall_8,
                'status_gizi_detail' => [
                    'zscore' => [
                        'tbu' => $pengukuran->z_tbu,
                        'bbu' => $pengukuran->z_bbu,
                        'bbtb' => $pengukuran->z_bbtb,
                    ],
                    'kategori' => [
                        'tbu' => $pengukuran->kat_tbu,
                        'bbu' => $pengukuran->kat_bbu,
                        'bbtb' => $pengukuran->kat_bbtb,
                    ],
                    'overall_8' => $pengukuran->overall_8,
                ],
                'tanggal_penimbangan_terakhir' => $pengukuran->tanggal_ukur,
            ]);
        }
    }
}