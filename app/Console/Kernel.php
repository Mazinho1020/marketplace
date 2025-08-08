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
        // Verificação de estoque baixo a cada 2 horas durante horário comercial (8h às 18h) nos dias úteis
        $schedule->command('estoque:verificar-baixo')
            ->cron('0 8,10,12,14,16,18 * * 1-5') // Segunda a Sexta, nos horários especificados
            ->withoutOverlapping()
            ->runInBackground();

        // Limpeza de notificações antigas de estoque - toda segunda-feira às 3h
        $schedule->command('estoque:verificar-baixo --limpar-antigas')
            ->weekly()
            ->mondays()
            ->at('03:00');
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
