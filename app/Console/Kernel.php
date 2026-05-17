<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Kirim reminder jadwal posyandu setiap hari jam 08:00 (H-1)
        $schedule->command('poscare:reminder-jadwal')->dailyAt('08:00');

        // Kirim reminder 30 menit sebelum jadwal (cek setiap menit)
        $schedule->command('poscare:reminder-30menit')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
