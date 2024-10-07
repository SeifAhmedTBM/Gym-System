<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\MediaUploadingTrait;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\SalesDataImport;
use App\Models\EmployeeSchedule;
use App\Models\ExpensesCategory;
use App\Models\ScheduleTemplate;
use App\Models\AttendanceSetting;
use App\Models\EmployeeAttendance;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyEmployeeRequest;
use App\Models\MembershipSchedule;
use App\Models\Schedule;
use App\Models\TrainerAttendant;
use App\Models\TrainerSessionAttendance;
use Illuminate\Support\Facades\Auth;

class EmployeesController extends Controller
{
    use CsvImportTrait;
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        
        abort_if(Gate::denies('employee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);


        $employees = Employee::where('status' , 'active')->get();
        foreach ($employees as $employee) {
            $employee->finger_print_id = $employee->id;
            $employee->save();
        }

        $employee = Auth()->user()->employee;
        if($request->status){
            $status = $request->status ;
        }
        else{
            $status = 'active';
        }
        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) {
                $query = Employee::index($data)->with(['user', 'branch'])
                    ->where('status' , $status)
                    ->whereBranchId($employee->branch_id)
                    ->select(sprintf('%s.*', (new Employee())->table));
            } else {
                $query = Employee::index($data)->where('status' , $status)->with(['user', 'branch'])->select(sprintf('%s.*', (new Employee())->table));
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'employee_show';
                $editGate = 'employee_edit';
                $deleteGate = 'employee_delete';
                $crudRoutePart = 'employees';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('photo', function ($row) {
                if ($photo = $row->photo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });

            $table->editColumn('id', function ($row) {
                return $row->order ? $row->order : '';
            });
            $table->editColumn('job_status', function ($row) {
                return $row->job_status ? Employee::JOB_STATUS_SELECT[$row->job_status] : '';
            });

            $table->editColumn('status', function ($row) {
//                Trainer
                if($row->user->roles[0]->title == 'Trainer'){
                    return $row->status ? Employee::STATUS_SELECT[$row->status] . '<br> Mobile: '.($row->mobile_visibility ? 'Active' : 'inactive') : '';

                }
                return $row->status ? Employee::STATUS_SELECT[$row->status] : '';
            });

            $table->addColumn('employee_name', function ($row) {
                return $row->name ? '<a class="text-decoration-none" href="' . route('admin.employees.show', $row->id) . '">' . $row->name . ' <br>' . ($row->user ? $row->user->phone : '') . '</a>' : '';
            });


            $table->addColumn('user_email', function ($row) {
                return $row->user ? $row->user->email : '-';
            });

            $table->addColumn('phone_number', function ($row) {
                return $row->phone;
            });

            $table->addColumn('national', function ($row) {
                return $row->national ? $row->national : '-';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->branch ? $row->branch->name : '-';
            });

            $table->addColumn('role', function ($row) {
                return $row->user ? '<span class="badge badge-info">' . $row->user->roles[0]->title . '</span>' : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'role', 'branch_name', 'employee_name','photo','status']);

            return $table->make(true);
        }

        if ($employee && $employee->branch_id != NULL) {
            $employees = Employee::index($data)->whereBranchId($employee->branch_id);
        } else {
            $employees = Employee::index($data);
        }

        $branches = Branch::pluck('name', 'id');

        return view('admin.employees.index', compact('employees', 'branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('employee_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $employee = Auth()->user()->employee;

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $roles = Role::pluck('title', 'id');

        $schedule_templates = ScheduleTemplate::pluck('name', 'id');

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
  
        return view('admin.employees.create', compact('users', 'roles', 'schedule_templates', 'branches','employee'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        if (isset($request['can_login'])) {
            $user = User::create([
                'name'      => $request['name'],
                'email'     => $request['phone'] . '@fitness.com',
                'password'  => Hash::make('123456'),
                'phone'     => $request['phone']
            ]);
            $user->roles()->sync($request->input('role_id'));
        }

        $data = $request->except('from', 'to', 'days');

        $data['attendance_check'] = $request['attendance_check'] ?? 'no';

        $data['user_id'] = isset($user) ? $user->id : NULL;

        $data['finger_print_id'] = $request['access_card'];
        $data['branch_id']  = Auth()->user()->employee->branch_id ?? null;
        $employee = Employee::create($data);

        if (isset($request['attendance_check'])) {
            foreach ($request['days'] as $day) {
                $employee = EmployeeSchedule::create([
                    'employee_id'           => $employee->id,
                    'day'                   => $day,
                    'from'                  => $request['from'][$day] ?? '10:00',
                    'to'                    => $request['to'][$day] ?? '18:00',
                    'is_offday'             => $request['offday'][$day] ?? 0,
                    'flexible'              => $request['flexible'][$day] ?? 0,
                    'working_hours'         => $request['working_hours'][$day] ?? 8,
                ]);
                if ($request->input('photo', false)) {
                    $employee->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
                }

                if ($media = $request->input('ck-media', false)) {
                    Media::whereIn('id', $media)->update(['model_id' => $employee->id]);
                }
            }
        }

        $this->created();

        return redirect()->route('admin.employees.index');
    }

    public function edit(Employee $employee)
    {
        // abort_if(Gate::denies('employee_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $schedule_templates = ScheduleTemplate::pluck('name', 'id');

        $employee->loadCount('days')->load(['user', 'days']);

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.employees.edit', compact('users', 'employee', 'schedule_templates', 'branches'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        // $employees = Employee::all();
        // foreach($employees as $employee){
        //     $employee->finger_print_id = $employee->id;
        //     $employee->save();
        // }
        $data['finger_print_id'] = $request['access_card'];
        if ($request['attendance_check'] == 'yes') {
            if (isset($request['days_ids'])) {
                foreach ($request['days_ids'] as $key => $day_id) {
                    EmployeeSchedule::find($day_id)->update([
                        'employee_id'          => $employee->id,
                        'day'                  => $request['days'][$key],
                        'from'                 => $request['from'][$request['days'][$key]] ?? NULL,
                        'to'                   => $request['to'][$request['days'][$key]] ?? NULL,
                        'is_offday'            => $request['is_offday'][$request['days'][$key]] ?? 0,
                    ]);
                }
            } elseif (isset($request['days'])) {
                foreach ($request['days'] as $day) {
                    EmployeeSchedule::create([
                        'employee_id'          => $employee->id,
                        'day'                  => $day,
                        'from'                 => $request['from'][$day] ?? NULL,
                        'to'                   => $request['to'][$day] ?? NULL,
                        'is_offday'            => $request['offday'][$day] ?? 0
                    ]);
                }
            }
        }

        $employee->user->update([
            'phone'         => $request['phone']
        ]);

        $employee->update($request->except('from', 'to', 'days', 'user_id'));
        if ($request->input('photo', false)) {
            if (!$employee->photo || $request->input('photo') !== $employee->photo->file_name) {
                if ($employee->photo) {
                    $employee->photo->delete();
                }
                $employee->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($employee->photo) {
            $employee->photo->delete();
        }
        $this->sent_successfully();
        // return redirect()->back();
        return redirect()->route('admin.employees.index');
    }

    public function show(Employee $employee)
    {
        abort_if(Gate::denies('employee_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee->loadCount('days')->loadSum('vacations', 'diff')->load(['user', 'days', 'bonuses', 'deductions', 'loans', 'vacations', 'documents']);

        return view('admin.employees.show', compact('employee'));
    }

    public function destroy(Employee $employee)
    {
        abort_if(Gate::denies('employee_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee->delete();

        return back();
    }

    public function massDestroy(MassDestroyEmployeeRequest $request)
    {
        Employee::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function payroll()
    {
        $payrolls = Payroll::all();

        return view('admin.employees.payroll', compact('payrolls'));
    }

    public function payrollStatus($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);

        if ($payroll->status == 'unconfirmed') {
            $expense = Expense::create([
                'expenses_category_id'      => ExpensesCategory::whereName('Salary')->firstOrCreate(['name' => 'Salary'])->id,
                'amount'                    => $payroll->net_salary,
                'date'                      => date('Y-m-d'),
                'created_by_id'             => Auth()->id(),
                'account_id'                => Account::first()->id,
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
        }

        $payroll->update([
            'status'        => $payroll->status == 'unconfirmed' ? 'confirmed' : 'unconfirmed'
        ]);

        $this->sent_successfully();
        return back();
    }

    public function showSinglePayroll(Payroll $payroll)
    {
        $payroll = $payroll->load('employee');

        $settings = Setting::firstOrFail();

        return view('admin.employees.show-payroll', compact('payroll', 'settings'));
    }


    public function printPayroll(Request $request)
    {
        $payroll = Payroll::with('employee')->find($request['payroll_id']);
        $data = array();
        $data['collected'] = 0;
        if ($payroll->employee->user && $payroll->employee->user->memberships) {
            foreach ($payroll->employee->user->memberships as $membership) {
                $data['collected'] += $membership->invoice->payments->sum('amount');
            }
        }
        $data['payroll'] = $payroll->toArray();
        $data['commission'] = 0;
        $data['logo'] = asset('images/' . Setting::first()->menu_logo);
        if ($payroll->employee->user && $payroll->employee->user->sales_tier) {
            $data['commission'] += $data['collected']  * (($payroll->employee->user->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $data['collected'])->first()->commission ?? 0) / 100);
        }
        dd($data);
        $pdf = PDF::loadView('admin.employees.print-payroll', $data);
        return $pdf->stream('payroll-pdf');

        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadView('admin.employees.print-payroll', $data);
        // return $pdf->stream();

    }

//    public function employee_attendances(Request $request)
//    {
//        $employee = Auth()->user()->employee;
//        $from = $request->input('from')??null;
//        $to = $request->input('to')??null;
//
//        if ($employee && $employee->branch_id != NULL) {
//            $branch_id = $employee->branch_id;
//        } else {
//            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
//        }
//
//        $attendances = EmployeeAttendance::with('employee')
//                    ->whereHas(
//                        'employee',fn($q) => $q->whereHas(
//                                'user',fn($x) => $x
//                                        ->when($request['role_id'],fn($x) => $x->whereRelation('roles','id',$request['role_id']))
//                            )
//                            ->when($branch_id,fn($x) => $x->whereBranchId($branch_id))
//                            ->when($request['employee_id'],fn($x) => $x->whereId($request['employee_id'])
//                        )
//                    )
//            ->whereBetween('created_at', [$from, $to])
//            ->latest()
//            ->get();
//
//        $employees = Employee::whereHas('attendances')->orderBy('name')->pluck('name','id');
//
//        $roles = Role::orderBy('title')->pluck('title','id');
//
//        $branches = Branch::orderBy('name')->pluck('name','id');
//
//        return view('admin.employees.attendance', compact('attendances','employees','roles','branches','employee','branch_id'));
//    }
    public function employee_attendances(Request $request)
    {
        $employee = Auth()->user()->employee;
        $from = $request->input('from') ?? null;
        $to = $request->input('to') ?? null;


        $branch_id = ($employee && $employee->branch_id != null)
            ? $employee->branch_id
            : ($request->input('branch_id') ?? '');

        $attendancesQuery = EmployeeAttendance::with('employee')
            ->whereHas(
                'employee', fn($q) => $q->whereHas(
                'user', fn($x) => $x
                ->when($request->input('role_id'), fn($x) => $x->whereRelation('roles', 'id', $request->input('role_id')))
            )
                ->when($branch_id, fn($x) => $x->whereBranchId($branch_id))
                ->when($request->input('employee_id'), fn($x) => $x->whereId($request->input('employee_id')))
            );

        if ($from && $to) {
            $attendancesQuery->whereBetween('created_at', [$from, $to]);
        }
        elseif ($from) {
            // Only the from date is provided
            $attendancesQuery->where('created_at', '>=', $from);
        } elseif ($to) {
            // Only the to date is provided
            $attendancesQuery->where('created_at', '<=', $to);
        }else{
            $attendancesQuery->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'));
        }

        $attendances = $attendancesQuery->latest()->get();

        $employees = Employee::whereHas('attendances')->orderBy('name')->pluck('name', 'id');
        $roles = Role::orderBy('title')->pluck('title', 'id');
        $branches = Branch::orderBy('name')->pluck('name', 'id');

        return view('admin.employees.attendance', compact('attendances', 'employees', 'roles', 'branches', 'employee', 'branch_id'));
    }

    public function take_employee_attendance(Request $request)
    {
        $employee = Employee::with('user')
            ->whereAccessCard($request['access_card'])
            ->first();
        
        if (!is_null($employee)) {
            $attendance = EmployeeAttendance::whereFingerPrintId($employee['finger_print_id'])->latest()->first();

            if (is_null($attendance)) {
                $attend = EmployeeAttendance::create([
                    'finger_print_id'       => $employee['finger_print_id'],
                    'date'                  => date('Y-m-d'),
                    'absent'                => 'False',
                    'clock_in'              => now()->format('H:i:s'),
                    'clock_out'             => NULL,
                    'work_time'             => NULL,
                ]);

                session()->flash('attend_successfully', $employee->name.' Sign in Successfully !');
            } else {
                if (is_null($attendance->clock_out)) {
                    $attendance->update([
                        'clock_out'         => now()->format('H:i:s'),
                        'work_time'         => gmdate('H:i:s', Carbon::parse($attendance->clock_in)->diffInSeconds(Carbon::parse(now()->format('H:i:s'))))
                    ]);

                    session()->flash('attend_successfully', $employee->name.' Sign out Successfully !');
                } else {
                    $attendance = EmployeeAttendance::create([
                        'finger_print_id'   => $employee['finger_print_id'],
                        'date'              => date('Y-m-d'),
                        'absent'            => 'False',
                        'clock_in'          => now()->format('H:i:s'),
                        'clock_out'         => NULL,
                        'work_time'         => NULL,
                    ]);

                    session()->flash('attend_successfully', $employee->name.' Sign in Successfully !');
                }
            }
            $this->sent_successfully();
        } else {
            $this->wrong_employee_card();
        }


        return back();
    }

    public function employee_sign_in_out(Request $request,Employee $employee)
    {
        // dd(auth()->user()->employee->id);
        $employee = Employee::with('user')
            ->findOrFail(Auth()->user()->employee->id);
        
        if (!is_null($employee)) {
            $attendance = EmployeeAttendance::whereEmployeeId($employee->id)->latest()->first();

            if (is_null($attendance)) {
                $attend = EmployeeAttendance::create([
                    'finger_print_id'       => $employee['finger_print_id'],
                    'employee_id'           => $employee['id'],
                    'date'                  => date('Y-m-d'),
                    'absent'                => 'False',
                    'clock_in'              => now()->format('H:i:s'),
                    'clock_out'             => NULL,
                    'work_time'             => NULL,
                ]);

                session()->flash('attend_successfully', $employee->name.' Sign in Successfully !');
            } else {
                if (is_null($attendance->clock_out)) {
                    $attendance->update([
                        'clock_out'         => now()->format('H:i:s'),
                        'work_time'         => gmdate('H:i:s', Carbon::parse($attendance->clock_in)->diffInSeconds(Carbon::parse(now()->format('H:i:s'))))
                    ]);

                    session()->flash('attend_successfully', $employee->name.' Sign out Successfully !');
                } else {
                    $attendance = EmployeeAttendance::create([
                        'finger_print_id'   => $employee['finger_print_id'],
                        'employee_id'       => $employee['id'],
                        'date'              => date('Y-m-d'),
                        'absent'            => 'False',
                        'clock_in'          => now()->format('H:i:s'),
                        'clock_out'         => NULL,
                        'work_time'         => NULL,
                    ]);

                    session()->flash('attend_successfully', $employee->name.' Sign in Successfully !');
                }
            }
            $this->sent_successfully();
        } else {
            $this->wrong_employee_card();
        }


        return back();
    }

    public function transferSalesData()
    {
        $sales = User::whereRelation('roles', 'title', 'Sales')->pluck('name', 'id');
       
        return view('admin.transferSalesData.index', compact('sales'));
    }

    public function storeTransferSalesData(Request $request)
    {
        $from   = User::findOrFail($request['from'])->id;

        // Get the to request
        $leads = Lead::with(['memberships.invoice.payments', 'sales_reminders'])
            ->whereSalesById($request['from'])
            ->get();

        $random_to_index  = count($request['to']) - 1;
        foreach ($leads as $index => $lead) 
        {
            if (isset($request['retroactive']) && $lead->has('memberships')) 
            {
                foreach ($lead->sales_reminders as $key => $reminder) {
                    $reminder->update([
                        'user_id'               => $request['to'][$random_to_index]
                    ]);
                }

                foreach ($lead->memberships as $key => $membership) 
                {
                    $membership->update([
                        'sales_by_id'           =>  $request['to'][$random_to_index]
                    ]);

                    if($membership->invoice)
                    {
                        $membership->invoice->update([
                            'sales_by_id'           =>  $request['to'][$random_to_index]
                        ]);
                    }

                    if ($membership->payments) 
                    {
                        foreach ($membership->payments as $key => $payment) 
                        {
                            $payment->update([
                                'sales_by_id'       =>  $request['to'][$random_to_index]
                            ]);
                        }
                    }
                }

                $lead->update([
                    'sales_by_id'               =>  $request['to'][$random_to_index]
                ]);
            }
            if (count($request['to']) == 1) {
                $random_to_index = 0;
            } else if ($random_to_index != 0) {
                $random_to_index -= 1;
            } else if ($random_to_index == 0) {
                $random_to_index  = count($request['to']) - 1;
            }
        }

        $this->sent_successfully();
        return back();
    }

    public function importSalesData(Request $request)
    {
        try {
            if ($file = $request->file('upload')) {
                $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move('imports', $fileName);
                Excel::import(new SalesDataImport, 'imports/' . $fileName);
            }
            $this->created();
            return back();
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function add_bonus($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.bonus.add_bonus', compact('employee'));
    }

    public function add_deduction($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.deductions.add_deduction', compact('employee'));
    }

    public function add_loan($id)
    {
        $employee = Employee::with('branch.accounts')->find($id);
        $selected_branch = $employee->branch;
        return view('admin.loans.add_loan', compact('selected_branch','employee'));
    }

    public function add_vacation($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.vacations.add_vacation', compact('employee'));
    }

    public function add_document($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.documents.add_document', compact('employee'));
    }

    public function change_status($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->status == 'active') 
        {
            $employee->update([
                'status'        => 'inactive'
            ]);
        } else {
            $employee->update([
                'status'        => 'active'
            ]);
        }

        $this->sent_successfully();
        return back();
    }

    public function change_mobile_status($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->mobile_visibility = ! $employee->mobile_visibility;
        $employee->save();
        $this->sent_successfully();
        return back();
    }
    public function fixedComission($id)
    {
        $payroll = Payroll::find($id);
        $user = $payroll->employee->user;
        // $date = date('Y-m', strtotime($date));
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

        $sessions = TrainerSessionAttendance::with('trainer', 'schedule')
            ->where('trainer_id', $user->id)
            ->where('created_at', '>=', date('Y-m-1'))
            ->where('created_at', '<=', date('Y-m-t'))
            ->whereHas('schedule', function ($q) {
                $q->where('comission_type', 'fixed');
            })->get();
        // dd($sessions);
        foreach ($sessions as $val) {
            $total_comissions += $val->schedule->comission_amount;
        }
        // dd($real_attendances[0]);
        return view('admin.employees.fixedComission', compact('user', 'payroll', 'total_comissions', 'sessions'));
    }


    public function percentageComission($id)
    {

        $payroll = Payroll::find($id);
        $user = $payroll->employee->user;
        $date = date('Y-m', strtotime($payroll->created_at));
        $total_comissions = 0;

        $trainer_schedule_ids = Schedule::where('trainer_id', $user->id)->where('comission_type', 'percentage')->pluck('id')->toArray();
        $membership_schedules = MembershipSchedule::whereIn('schedule_id', $trainer_schedule_ids)
            ->where('created_at', '>=', date('Y-m-1', strtotime($date)))
            ->where('created_at', '<=', date('Y-m-t', strtotime($date)))
            ->get();
        foreach ($membership_schedules as $membership_sch) {
            $total_comissions += ($membership_sch->membership->invoice->net_amount * $membership_sch->schedule->comission_amount) / 100;
        }

        return view('admin.employees.percentageComission', compact('membership_schedules', 'user', 'payroll', 'total_comissions'));
    }
}
