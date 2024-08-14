<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeesAttendancesExport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;
use App\Imports\ImportFingerprintSheet;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id !== NULL) 
        {
            $branch_id = $employee->branch_id;
        }else{
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }
        $employee_attendances = EmployeeAttendance::index(request()->all())
                                ->whereHas('employee', fn($q) => $q->when($branch_id,fn($y) => $y->whereBranchId($branch_id)))
                                ->with('employee')
                                ->get()
                                ->groupBy('finger_print_id');

        return view('admin.employee_attendances.index', compact('employee_attendances'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($file = $request->file('file')) {
            $name =  date('F') . '_' . date('Y') . '.' .$file->getClientOriginalExtension();
            $file->move('attendants', $name);
            Excel::import(new ImportFingerprintSheet(), public_path('attendants/' . $name));
        }

        $this->created();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::with(['attendances' => fn($q) => $q->whereYear('created_at',date('Y'))
                                            ->whereMonth('created_at',date('m'))
                                            ->latest()
                                    ])
                                    // ->whereHas('attendances',function($q){
                                    //     $q->whereYear('created_at',date('Y'))
                                    //     ->whereMonth('created_at',date('m'))
                                    //     ->where('work_time', '!=', NULL);
                                    // })
                                    ->where('finger_print_id', $id)
                                    ->first();
        $working_hours = 0;
        $working_minutes = 0;
        $assignedHours = 0;
        $assignedMinutes = 0;
        $oneDayHours = 0;

        if ($employee->attendances != NULL) 
        {
            foreach($employee->attendances as $attendance) 
            {
                $working_hours += explode(':', $attendance->work_time)[0];
                $working_minutes += explode(':', $attendance->work_time)[1];
            }
    
            foreach($employee->days()->where('is_offday', '!=', 1)->get() as $day) {
                $assignedHours += Carbon::parse($day->from)->diff($day->to)->format('%h');
                $assignedMinutes += Carbon::parse($day->from)->diff($day->to)->format('%i');
            }
    
            if ($employee->days->count() < 1) {
                $this->employee_schedule();
                return back();
            }
    
            $oneDayHours += $assignedHours / $employee->days()->where('is_offday', '!=', 1)->get()->count();
            $should_be_counted = $oneDayHours * $employee->attendances()->whereYear('created_at',date('Y'))
            ->whereMonth('created_at',date('m'))->where('absent', NULL)->get()->count();
            $working_hours += intval($working_minutes / 60);
            $working_minutes = (($working_minutes / 60) - floor($working_minutes / 60)) * 60;
            $total_working_hours = ''.$working_hours.' : '.$working_minutes.'';
            // $should_be_counted = ''.$assignedHours.' : '.$assignedMinutes.'';
            $difference = $working_hours - $should_be_counted;
        }else{
            $this->something_wrong();
            return back();
        }

        return view('admin.employee_attendances.show', compact('employee', 'total_working_hours', 'should_be_counted', 'difference'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function export(Request $request)
    {
        return Excel::download(new EmployeesAttendancesExport($request), 'Attendances.xlsx');
    }
}
