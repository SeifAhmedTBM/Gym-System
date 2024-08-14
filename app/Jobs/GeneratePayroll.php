<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Payroll;
use App\Models\Setting;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class GeneratePayroll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function start()
    {
        $employees = Employee::with(['attendances', 'days'])->whereHas('attendances',function($q){
            $q->where('work_time','!=',NULL);
        })->whereHas('days')->get();
        
        
        foreach($employees as $emp) {
            $days = Setting::first()->payroll_day ?? 25;
            $working_hours = 0;
            $working_minutes = 0;
            $assignedHours = 0;
            $assignedMinutes = 0;
            $oneDayHours = 0;
            $delayDeduction = 0;
            $delayMinutes = 0;
            $hourValue = ($emp->salary / $days) / 8;


            foreach($emp->attendances()->whereMonth('created_at', date('m'))->where('work_time', '!=', NULL)->get() as $attendance) {
                $working_hours += explode(':', $attendance->work_time)[0];
                $working_minutes += explode(':', $attendance->work_time)[1];
            }

            foreach($emp->days()->where('is_offday', '!=', 1)->get() as $day) {
                $assignedHours += Carbon::parse($day->from)->diff($day->to)->format('%h');
                $assignedMinutes += Carbon::parse($day->from)->diff($day->to)->format('%i');
                foreach($emp->attendances()->where('absent', NULL)->where(DB::raw('DATE_FORMAT(date,"%a")'), $day->day)->get() as $attended) {
                    $actual_in = Carbon::createFromFormat('H:i:s', date('H:i:s', strtotime($attended->clock_in)));
                    $due_in = Carbon::createFromFormat('H:i:s', date('H:i:s', strtotime($day->from)));
                    $delay_minutes = $due_in->diffInMinutes($actual_in);
                    $delayMinutes += $delay_minutes;
                    $delay_rules = AttendanceSetting::where('from', '<=', $delay_minutes)->latest()->first()->deduction ?? 0;
                    $delayDeduction += ($delay_rules * 8) * $hourValue;
                }
            }
            $oneDayValue = $emp->salary / $days;
            
            $deductions = $emp->deductions()->whereMonth('created_at', date('m'))->sum('amount');
            $bonuses = $emp->bonuses()->whereMonth('created_at', date('m'))->sum('amount');
            $loans = $emp->loans()->whereMonth('created_at', date('m'))->sum('amount');
            $vacations = $emp->vacations()->whereMonth('created_at', date('m'))->sum('diff');
            $vacations_deduction = $vacations * $oneDayValue;
            
            $delayHours = intdiv($delayMinutes, 60).':'. ($delayMinutes % 60);
            $oneDayHours += $assignedHours / $emp->days()->where('is_offday', '!=', 1)->get()->count();
            // $should_be_counted = $oneDayHours * $emp->attendances()->where('absent', NULL)->get()->count();
            $should_be_counted = $oneDayHours * $emp->attendances()->whereMonth('created_at', date('m'))->get()->count();
            $working_hours += intval($working_minutes / 60);
            $working_minutes = (($working_minutes / 60) - floor($working_minutes / 60)) * 60;
            $total_working_hours = ''.$working_hours.' : '.$working_minutes.'';

            $wh = $working_minutes / 60;
            $total_hours = $working_hours += $wh;
            

            $hourVal = $emp->salary  / ($should_be_counted ?? 1);
            $actual_salary = round($total_hours * $hourVal);

           
            $payroll = Payroll::create(
            [
                'employee_id'               => $emp->id,
                'basic_salary'              => intval($emp->salary),
                'labor_hours'               => intval($should_be_counted),
                'working_hours'             => $total_working_hours,
                'delay_deduction'           => $delayDeduction,
                'delay_hours'               => $delayHours,
                'gross_salary'              => $actual_salary,
                'vacations'                 => $vacations_deduction,
                'deduction'                 => $deductions,
                'bonus'                     => $bonuses,
                'loans'                     => $loans,
                'net_salary'                => $actual_salary - ( $vacations_deduction +  $deductions + $loans + $delayDeduction) + $bonuses
            ]);

        }
    }


    public function end()
    {
        $alert = UserAlert::create([
            'alert_text' => __('global.payroll_has_been_generated'),
            'alert_link' => route('admin.payroll.get')
        ]);
        $admins = User::whereHas('roles', function($q) {
            $q = $q->where('title', 'Admin');
        })->pluck('name', 'id');
        foreach($admins as $id => $admin) {
            DB::table('user_user_alert')->insert(['user_alert_id' => $alert->id, 'user_id' => $id, 'read' => 0]);
        }
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->start();
        // $this->end();
    }
}
