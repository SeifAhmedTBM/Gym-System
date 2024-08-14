<?php

namespace App\Jobs;

use App\Models\EmployeeAttendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogOutEmployees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function start()
    {
        $employees_attendance = EmployeeAttendance::where('clock_in', '!=', Null)
                            ->where('clock_out', Null)
                            ->get();

        foreach ($employees_attendance as $attendance) {
            $attendance->update([
                'clock_out' => '23:59:59'
            ]);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->start();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
