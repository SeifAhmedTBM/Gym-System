<?php

namespace App\Jobs;

use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveFingerprintSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function start()
    {
        foreach($this->data['finger_print_id'] as $key => $entry) {
            $clock_in = Carbon::parse($this->data['clock_in'][$key]);
            $clock_out = Carbon::parse($this->data['clock_out'][$key]);
            $work_time = $clock_out->diff($clock_in);
            EmployeeAttendance::create([
                'clock_in'              => $this->data['clock_in'][$key],
                'clock_out'             => $this->data['clock_out'][$key],
                'work_time'             => $work_time->format('%h:%i'),
                'finger_print_id'       => $entry,
                'date'                  => $this->data['date'][$key],
                'absent'                => $this->data['absent'][$key]
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
        }catch(\Exception $e) {
            dd($e->getMessage());
        }
    }
}
