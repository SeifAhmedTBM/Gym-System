<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyVacationRequest;

class VacationsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('vacation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {

            if ($employee && $employee->branch_id) 
            {
                $query = Vacation::index($data)
                                    ->with(['employee', 'created_by'])
                                    ->whereHas('employee',fn($q) => $q->whereBranchId($employee->branch_id))
                                    ->select(sprintf('%s.*', (new Vacation())->table));
            }else{
                $query = Vacation::index($data)->with(['employee', 'created_by'])->select(sprintf('%s.*', (new Vacation())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'vacation_show';
                $editGate = 'vacation_edit';
                $deleteGate = 'vacation_delete';
                $crudRoutePart = 'vacations';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('employee_job_status', function ($row) {
                return $row->employee ? $row->employee->name : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('diff', function ($row) {
                return $row->diff ? $row->diff : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'employee', 'created_by']);

            return $table->make(true);
        }

        $created_bies = User::whereHas('roles',function($q){
            $q->where('Title','Admin');
        })->pluck('name', 'id');

        $vacations = Vacation::index($data);

        return view('admin.vacations.index',compact('created_bies','vacations'));
    }

    public function create()
    {
        abort_if(Gate::denies('vacation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) 
        {
            $employees = Employee::whereBranchId($employee->branch_id)
                                            ->whereStatus('active')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->prepend(trans('global.pleaseSelect'), '');
        }else{
            $employees = Employee::whereStatus('active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend(trans('global.pleaseSelect'), '');
        }

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.vacations.create', compact('employees', 'created_bies'));
    }

    public function store(StoreVacationRequest $request)
    {
        if ($request['from'] != $request['to']) 
        {
            $vacation = Vacation::create([
                'employee_id'   => $request['employee_id'],
                'name'          => $request['name'],
                'description'   => $request['description'],
                'from'          => $request['from'],
                'to'            => $request['to'],
                'diff'          => Carbon::parse($request['from'])->diffInDays(Carbon::parse($request['to'])),
                'created_by_id' => Auth()->user()->id,
            ]);
            $this->sent_successfully();
            return redirect()->route('admin.employees.show',$vacation->employee_id);
        }else{
            $this->something_wrong();
            return back();
        }
    }

    public function edit(Vacation $vacation)
    {
        abort_if(Gate::denies('vacation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) 
        {
            $employees = Employee::whereBranchId($employee->branch_id)
                                            ->whereStatus('active')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->prepend(trans('global.pleaseSelect'), '');
        }else{
            $employees = Employee::whereStatus('active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend(trans('global.pleaseSelect'), '');
        }

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vacation->load('employee', 'created_by');

        return view('admin.vacations.edit', compact('employees', 'created_bies', 'vacation'));
    }

    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $vacation->update([
            'employee_id' => $request['employee_id'],
            'name' => $request['name'],
            'description' => $request['description'],
            'from' => $request['from'],
            'to' => $request['to'],
            'diff' => Carbon::parse($request['from'])->diffInDays(Carbon::parse($request['to'])),
        ]);

        return redirect()->route('admin.vacations.index');
    }

    public function show(Vacation $vacation)
    {
        abort_if(Gate::denies('vacation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vacation->load('employee', 'created_by');

        return view('admin.vacations.show', compact('vacation'));
    }

    public function destroy(Vacation $vacation)
    {
        abort_if(Gate::denies('vacation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vacation->delete();

        return back();
    }

    public function massDestroy(MassDestroyVacationRequest $request)
    {
        Vacation::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
