<?php

namespace App\Console\Commands;

use App\Services\FcmService;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendJadwalReminder extends Command
{
    protected $signature   = 'poscare:reminder-jadwal';
    protected $description = 'Kirim notifikasi reminder H-1 untuk jadwal posyandu besok';

    public function handle(): void
    {
        $besok = Carbon::tomorrow()->toDateString();

        $jadwalList = Jadwal::whereDate('tanggal', $besok)
            ->whereNotIn('status', ['Dibatalkan', 'Selesai'])
            ->get();

        if ($jadwalList->isEmpty()) {
            $this->info("Tidak ada jadwal untuk besok ($besok)");
            return;
        }

        $this->info("Ditemukan {$jadwalList->count()} jadwal untuk besok");

        $fcm = new FcmService();

        foreach ($jadwalList as $jadwal) {
            $tgl   = Carbon::parse($jadwal->tanggal)->format('d/m/Y');
            $waktu = substr($jadwal->waktu_mulai, 0, 5);
            $title = '⏰ Pengingat Jadwal Besok!';
            $body  = "{$jadwal->nama_kegiatan} - {$tgl} pukul {$waktu} di {$jadwal->lokasi}";

            $fcm->saveNotifikasi($title, $body, 'jadwal');
            $result = $fcm->sendToAll($title, $body, ['type' => 'reminder_jadwal']);

            $msg = $result['message'] ?? ($result['success'] ? "Terkirim ke {$result['sent']} device" : 'Gagal');
            $this->info("Jadwal: {$jadwal->nama_kegiatan} → $msg");
        }

        $this->info('Reminder selesai dikirim.');
    }
}
