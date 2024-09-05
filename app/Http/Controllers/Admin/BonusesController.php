<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyBonuRequest;
use App\Http\Requests\StoreBonuRequest;
use App\Http\Requests\UpdateBonuRequest;
use App\Models\Bonu;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BonusesController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('bonu_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {

            if ($employee && $employee->branch_id != NULL) 
            {
                $query = Bonu::index($data)
                                ->with(['employee', 'created_by'])
                                ->whereHas('employee',fn($q) => $q->whereBranchId($employee->branch_id))
                                ->select(sprintf('%s.*', (new Bonu())->table));

            }else{
                $query = Bonu::index($data)
                                ->with(['employee', 'created_by'])
                                ->select(sprintf('%s.*', (new Bonu())->table));
            }
            if (
                (!isset($request->created_at) ||
                    ($request->created_at['from'] === null && $request->created_at['to'] === null))
            ) {
                $query = $query->where('created_at', '>=', date('Y-m-1'))
                    ->where('created_at', '<=', date('Y-m-t'));
            }


            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'bonu_show';
                $editGate = 'bonu_edit';
                $deleteGate = 'bonu_delete';
                $crudRoutePart = 'bonus';

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

        $bonuses = Bonu::index($data);
        if (
            (!isset($request->created_at) ||
                ($request->created_at['from'] === null && $request->created_at['to'] === null))
        ) {
            $bonuses = $bonuses->where('created_at', '>=', date('Y-m-1'))->where('created_at', '<=', date('Y-m-t'));;
        }
        return view('admin.bonus.index',compact('created_bies','bonuses'));
    }

    public function create()
    {
        abort_if(Gate::denies('bonu_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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

        return view('admin.bonus.create', compact('employees', 'created_bies'));
    }

    public function store(StoreBonuRequest $request)
    {
        $data = $request->all();
        $data['name'] = $data['reason'];
        $data['created_by_id'] = auth()->id();
        $bonu = Bonu::create($data);

        return redirect()->route('admin.bonus.index');
    }

    public function edit(Bonu $bonu)
    {
        abort_if(Gate::denies('bonu_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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

        $bonu->load('employee', 'created_by');

        return view('admin.bonus.edit', compact('employees', 'created_bies', 'bonu'));
    }

    public function update(UpdateBonuRequest $request, Bonu $bonu)
    {
        $data = $request->all();
        $data['created_by_id'] = auth()->id();
        $bonu->update($data);

        return redirect()->route('admin.bonus.index');
    }

    public function show(Bonu $bonu)
    {
        abort_if(Gate::denies('bonu_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bonu->load('employee', 'created_by');

        return view('admin.bonus.show', compact('bonu'));
    }

    public function destroy(Bonu $bonu)
    {
        abort_if(Gate::denies('bonu_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bonu->delete();

        return back();
    }

    public function massDestroy(MassDestroyBonuRequest $request)
    {
        Bonu::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
