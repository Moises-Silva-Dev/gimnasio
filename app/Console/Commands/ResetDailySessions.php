<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMembership;
use Carbon\Carbon;

class ResetDailySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:reset-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetea las sesiones diarias de todas las membresías activas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Obtener todas las membresías activas que hayan iniciado
        $memberships = UserMembership::where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        $count = 0;
        foreach ($memberships as $membership) {
            $membership->resetDailySessions();
            $count++;
        }

        $this->info("✅ Se resetearon las sesiones de {$count} membresías.");

        return Command::SUCCESS;
    }
}
