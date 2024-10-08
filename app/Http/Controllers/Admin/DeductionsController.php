<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyDeductionRequest;
use App\Http\Requests\StoreDeductionRequest;
use App\Http\Requests\UpdateDeductionRequest;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DeductionsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('deduction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);
        $data['created_at']['from'] = $data['created_at']['from'] ?? date('Y-m-01');
        $data['created_at']['to'] = $data['created_at']['to'] ?? date('Y-m-t');


        $employee = Auth()->user()->employee;

        if ($request->ajax()) {

            if ($employee && $employee->branch_id) 
            {
                $query = Deduction::index($data)
                                    ->with(['employee', 'created_by'])
                                    ->whereHas('employee',fn($q) => $q->whereBranchId($employee->branch_id))
                                    ->select(sprintf('%s.*', (new Deduction())->table));
            }else{
                $query = Deduction::index($data)
                                    ->with(['employee', 'created_by'])
                                    ->select(sprintf('%s.*', (new Deduction())->table));
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'deduction_show';
                $editGate = 'deduction_edit';
                $deleteGate = 'deduction_delete';
                $crudRoutePart = 'deductions';

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

            $table->editColumn('reason', function ($row) {
                return $row->reason ? $row->reason : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount . ' EGP' : '';
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
            $q = $q->where('title','Admin');
        })->pluck('name', 'id');

        $deductions = Deduction::index($data);
        $deductions_sum = number_format($deductions->where('created_at','>=',$data['created_at']['from'])->where('created_at','<=',$data['created_at']['to'] . ' 23:59:59')->sum('amount'),2);
        $deductions_count = $deductions->where('created_at','>=',$data['created_at']['from'])->where('created_at','<=',$data['created_at']['to'] . ' 23:59:59')->count();

        return view('admin.deductions.index',compact('created_bies','deductions','deductions_sum','deductions_count'));
    }

    public function create()
    {
        abort_if(Gate::denies('deduction_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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

        return view('admin.deductions.create', compact('employees', 'created_bies'));
    }

    public function store(StoreDeductionRequest $request)
    {
        $data = $request->all();
        $data['created_by_id'] = auth()->id();

        $data['name'] = $data['reason'];
        // $data['name'] = Employee::find($data['employee_id'])->first()->name;


        $deduction = Deduction::create($data);

        return redirect()->route('admin.deductions.index');
    }

    public function edit(Deduction $deduction)
    {
        abort_if(Gate::denies('deduction_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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

        $deduction->load('employee', 'created_by');

        return view('admin.deductions.edit', compact('employees', 'created_bies', 'deduction'));
    }

    public function update(UpdateDeductionRequest $request, Deduction $deduction)
    {
        $data = $request->all();
        $data['created_by_id'] = auth()->id();
        $deduction->update($data);

        return redirect()->route('admin.deductions.index');
    }

    public function show(Deduction $deduction)
    {
        abort_if(Gate::denies('deduction_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deduction->load('employee', 'created_by');

        return view('admin.deductions.show', compact('deduction'));
    }

    public function destroy(Deduction $deduction)
    {
        abort_if(Gate::denies('deduction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deduction->delete();

        return back();
    }

    public function massDestroy(MassDestroyDeductionRequest $request)
    {
        Deduction::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
