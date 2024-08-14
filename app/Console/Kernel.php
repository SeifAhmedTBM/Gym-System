<?php

namespace App\Console;

use App\Jobs\LogOutEmployees;
use App\Models\Setting;
use App\Jobs\GeneratePayroll;
use App\Jobs\UpdatingInvoices;
use App\Jobs\UpdatingMemberships;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpgradableMembershipsJob;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\UpdateExpiredMembershipsJob;
use App\Jobs\UpdateExpiringMembershipsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $day = DB::table('settings')->select('payroll_day')->first()->payroll_day ?? 30;
        // $schedule->job(new GeneratePayroll)->monthlyOn($day, '00:00');
        $schedule->job(new GeneratePayroll)->everyMinute();
        $schedule->job(new UpdatingMemberships)->daily();
        // $schedule->job(new UpdatingInvoices)->daily();
        $schedule->job(new UpgradableMembershipsJob)->daily();
        $schedule->job(new UpdateExpiredMembershipsJob)->daily();
        $schedule->job(new UpdateExpiringMembershipsJob)->daily();
        $schedule->job(new LogOutEmployees())->dailyAt('00:01');
        $schedule->command('queue:work --stop-when-empty')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
