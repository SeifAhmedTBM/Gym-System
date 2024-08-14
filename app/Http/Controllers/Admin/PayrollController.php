<?php

namespace App\Http\Controllers\Admin;

use DB;
use Carbon\Carbon;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\ScheduleMain;
use Illuminate\Http\Request;
use App\Exports\PayrollExport;
use App\Models\ExpensesCategory;
use App\Models\TrainerAttendant;
use App\Models\ScheduleMainGroup;
use App\Models\EmployeeAttendance;
use App\Models\MembershipSchedule;
use App\Http\Controllers\Controller;
use App\Models\TrainerSessionAttendance;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    public function payroll(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $emp = Auth()->user()->employee;

        if ($emp && $emp->branch_id != NULL) {
            $branch_id = $emp->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $employees = Employee::whereHas('user')
            ->where('status', 'active')
            ->with(['deductions', 'bonuses', 'loans', 'days'])
            ->withSum([
                'bonuses' => fn ($q) => $q->whereYear('created_at', date('Y', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ], 'amount')
            ->withSum([
                'deductions' => fn ($q) => $q->whereYear('created_at', date('Y', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ], 'amount')
            ->withSum([
                'loans' => fn ($q) => $q->whereYear('created_at', date('Y', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ], 'amount')
            ->whereDate('start_date', '<=', date('Y-m-d', strtotime($date . '-' . date('d'))))
            ->withCount('attendances')
            ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
            ->get();

        $total_fixed_comissions = 0;
        $total_percentage_comissions = 0;
        $total_comissions = 0;
        foreach ($employees as $key => $employee) {

            $this_employee_fixed_comissions = $this->getComissionFixed($employee, $date);
            $this_employee_percentage_comissions = $this->getComissionPercentage($employee, $date);

            $total_fixed_comissions += $this_employee_fixed_comissions;
            $total_percentage_comissions += $this_employee_percentage_comissions;
            $total_comissions +=  $total_fixed_comissions + $total_percentage_comissions;
            $employee_payroll = $employee->payroll()
                ->whereYear('created_at', date('Y', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
                ->first();

            if ($employee_payroll) {
                $employee_payroll->update([
                    'employee_id'           => $employee->id,
                    'basic_salary'          => $employee->salary,
                    'bonus'                 => floatval($employee->bonuses_sum_amount),
                    'deduction'             => floatval($employee->deductions_sum_amount),
                    'loans'                 => floatval($employee->loans_sum_amount),
                    'fixed_comissions'      => $this_employee_fixed_comissions,
                    'percentage_comissions' => $this_employee_percentage_comissions,
                    // 'status'            => 'unconfirmed',
                    'net_salary'            => floatval($employee->salary) + (floatval($employee->bonuses_sum_amount) - (floatval($employee->deductions_sum_amount) + floatval($employee->loans_sum_amount)))
                ]);
            } else {
                Payroll::create([
                    'employee_id'           => $employee->id,
                    'basic_salary'          => $employee->salary,
                    'bonus'                 => floatval($employee->bonuses_sum_amount),
                    'deduction'             => floatval($employee->deductions_sum_amount),
                    'loans'                 => floatval($employee->loans_sum_amount),
                    'status'                => 'unconfirmed',
                    'fixed_comissions'      => $this_employee_fixed_comissions,
                    'percentage_comissions' => $this_employee_percentage_comissions,
                    'net_salary'            => floatval($employee->salary) + (floatval($employee->bonuses_sum_amount) - (floatval($employee->deductions_sum_amount) + floatval($employee->loans_sum_amount))),
                    'created_at'            => date('Y-m-t', strtotime($date))
                ]);
            }
        }

        $payrolls = Payroll::with([
            'employee',
            'employee.user',
            'employee.branch'
        ])
            ->whereHas('employee', function ($q) use ($branch_id, $request) {
                $q->whereHas('user', function ($y) use ($request) {
                    $y->when($request['role_id'], fn ($x) => $x->whereRelation('roles', 'id', $request['role_id']));
                })
                    ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
                    ->whereStatus('active');
            })
            ->whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get();

        $accounts = Account::when($branch_id, fn ($q) => $q->whereBranchId($branch_id))->pluck('name', 'id');
        return view('admin.employees.payroll', compact('payrolls', 'emp', 'branch_id', 'accounts', 'total_comissions'));
    }


    public function getComissionFixed($employee, $date)
    {

        $user = $employee->user;

        $date = date('Y-m', strtotime($date));
        $total_comissions = 0;
        // $trainer_attendance_search = [];
        // $trainer_attendance_ids = [];
        // $attendances = TrainerAttendant::
        //            where('trainer_id',$user->id)
        //          ->where('created_at','>=',date('Y-m-1',strtotime($date)))
        //          ->where('created_at','<=',date('Y-m-t',strtotime($date)))
        //          ->get();

        // foreach($attendances as $attend){
        //     $search  =  $attend->schedule_id."|".date('Y-m-d',strtotime($attend->created_at));
        //     if(!in_array($search,$trainer_attendance_search)){
        //         array_push($trainer_attendance_search,$search);
        //         if($attend->schedule->comission_type == 'fixed' && $attend->schedule->comission_amount > 0){
        //             array_push($trainer_attendance_ids,$attend->id);
        //             $total_comissions += $attend->schedule->comission_amount;
        //         }
        //     }
        // }         
        // $real_attendances = TrainerAttendant::whereIn('id',$trainer_attendance_ids)->get();
        // foreach($real_attendances as $att){
        //     $total_comissions += $att->schedule->comissionn_amount;
        // }

        $sessions = TrainerSessionAttendance::where('trainer_id', $user->id)
            ->where('created_at', '>=', date('Y-m-1', strtotime($date)))
            ->where('created_at', '<=', date('Y-m-t', strtotime($date)))

            ->whereHas('schedule', function ($q) {
                $q->where('comission_type', 'fixed');
            })->get();
        foreach ($sessions as $val) {
            $total_comissions += $val->schedule->comission_amount;
        }
        // dd($total_comissions);
        return $total_comissions;
    }

    public function getComissionPercentage($employee, $date)
    {

        $user = $employee->user;
        $date = date('Y-m', strtotime($date));
        $total_comissions = 0;

        $trainer_schedule_ids = Schedule::where('trainer_id', $user->id)->where('comission_type', 'percentage')->pluck('id')->toArray();
        $membership_schedules = MembershipSchedule::whereIn('schedule_id', $trainer_schedule_ids)
            ->where('created_at', '>=', date('Y-m-1', strtotime($date)))
            ->where('created_at', '<=', date('Y-m-t', strtotime($date)))
            ->get();
        foreach ($membership_schedules as $membership_sch) {
            $total_comissions += ($membership_sch->membership->invoice->net_amount * $membership_sch->schedule->comission_amount) / 100;
        }

        return $total_comissions;
    }
    public function confirm_all(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $emp = Auth()->user()->employee;

        if ($emp && $emp->branch_id != NULL) {
            $branch_id = $emp->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $payrolls = Payroll::with([
            'employee',
            'employee.user',
            'employee.branch'
        ])
            ->whereHas('employee', function ($q) use ($branch_id, $request) {
                $q->whereHas('user', function ($y) use ($request) {
                    $y->when($request['role_id'], fn ($x) => $x->whereRelation('roles', 'id', $request['role_id']));
                })
                    ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
                    ->whereStatus('active');
            })
            ->whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get();

        foreach ($payrolls as $key => $payroll) {
            $expense = Expense::create([
                'expenses_category_id'      => ExpensesCategory::whereName('Salary')->firstOrCreate(['name' => 'Salary'])->id,
                'amount'                    => $payroll->net_salary,
                'date'                      => date('Y-m-d'),
                'created_by_id'             => Auth()->id(),
                'account_id'                => $request['account_id'],
                'name'                      => $payroll->employee->name . "'s Salary of " . date('Y-m', strtotime($payroll->created_at))
            ]);

            $expense->account->balance = $expense->account->balance - $expense->amount;
            $expense->account->save();

            $transaction = Transaction::create([
                'transactionable_type'      => 'App\\Models\\Expense',
                'transactionable_id'        => $expense->id,
                'amount'                    => $expense->amount,
                'account_id'                => $expense->account_id,
                'created_at'                => $expense->created_at,
                'created_by'                => Auth()->id(),
            ]);

            $payroll->update([
                'status'        => 'confirmed'
            ]);
        }

        $this->sent_successfully();
        return back();
    }

    public function export(Request $request)
    {
        return Excel::download(new PayrollExport($request), 'Payroll.xlsx');
    }
}
