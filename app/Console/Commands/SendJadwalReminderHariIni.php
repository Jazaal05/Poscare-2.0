<?php

namespace App\Console\Commands;

use App\Services\FcmService;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendJadwalReminderHariIni extends Command
{
    protected $signature   = 'poscare:reminder-30menit';
    protected $description = 'Kirim notifikasi 30 menit sebelum jadwal posyandu hari ini';

    public function handle(): void
    {
        $now        = Carbon::now();
        $target     = $now->copy()->addMinutes(30);
        $hari_ini   = $now->toDateString();
        $jam_target = $target->format('H:i');

        // Cari jadwal balita DAN lansia yang mulai 30 menit dari sekarang (toleransi ±1 menit)
        $jadwalList = Jadwal::whereDate('tanggal', $hari_ini)
            ->whereNotIn('status', ['Dibatalkan', 'Selesai'])
            ->get()
            ->filter(function ($jadwal) use ($target) {
                // Ambil hanya bagian tanggal (tanpa waktu) lalu gabung dengan waktu_mulai
                $tanggalOnly = Carbon::parse($jadwal->tanggal)->toDateString();
                $jadwalTime  = Carbon::parse($tanggalOnly . ' ' . $jadwal->waktu_mulai);
                $diff = abs($jadwalTime->diffInMinutes($target));
                return $diff <= 1;
            });

        if ($jadwalList->isEmpty()) {
            $this->info("Tidak ada jadwal yang mulai sekitar jam $jam_target");
            return;
        }

        $this->info("Ditemukan {$jadwalList->count()} jadwal yang mulai 30 menit lagi");

        $fcm = new FcmService();

        foreach ($jadwalList as $jadwal) {
            $tgl     = Carbon::parse($jadwal->tanggal)->format('d/m/Y');
            $waktu   = substr($jadwal->waktu_mulai, 0, 5);
            $layanan = ($jadwal->layanan === 'lansia') ? 'Lansia' : 'Balita';
            $title   = '⏰ Jadwal Dimulai 30 Menit Lagi!';
            $body    = "[{$layanan}] {$jadwal->nama_kegiatan} - {$tgl} pukul {$waktu} di {$jadwal->lokasi}";

            $tipe    = ($jadwal->layanan === 'lansia') ? 'jadwal_lansia' : 'jadwal';
            $fcm->saveNotifikasi($title, $body, $tipe);
            $result = $fcm->sendToAll($title, $body, ['type' => 'reminder_30menit']);

            $msg = $result['message'] ?? ($result['success'] ? "Terkirim ke {$result['sent']} device" : 'Gagal');
            $this->info("Jadwal: {$jadwal->nama_kegiatan} → $msg");
        }

        $this->info('Reminder 30 menit selesai dikirim.');
    }
}
