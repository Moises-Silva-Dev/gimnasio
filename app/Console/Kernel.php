<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Ejecutar el comando todos los días a las 8:30 AM
        // $schedule->command('documents:send-licitacion-reminder')->dailyAt('08:30');

        // Resetear sesiones diarias a las 00:01 todos los días
        $schedule->command('memberships:reset-sessions')
            ->dailyAt('00:01')
            ->timezone('America/Mexico_City');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
