<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyEmployeeSettingRequest;
use App\Http\Requests\StoreEmployeeSettingRequest;
use App\Http\Requests\UpdateEmployeeSettingRequest;
use App\Models\EmployeeSetting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class EmployeeSettingsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('employee_setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = EmployeeSetting::with(['created_by'])->select(sprintf('%s.*', (new EmployeeSetting())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'employee_setting_show';
                $editGate = 'employee_setting_edit';
                $deleteGate = 'employee_setting_delete';
                $crudRoutePart = 'employee-settings';

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

            $table->editColumn('start_time', function ($row) {
                return $row->start_time ? $row->start_time : '';
            });
            
            $table->editColumn('end_time', function ($row) {
                return $row->end_time ? $row->end_time : '';
            });
            
            $table->editColumn('default_month_days', function ($row) {
                return $row->default_month_days ? $row->default_month_days : '';
            });
            
            $table->editColumn('default_vacation_days', function ($row) {
                return $row->default_vacation_days ? $row->default_vacation_days : '';
            });
            
            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'created_by']);

            return $table->make(true);
        }

        return view('admin.employeeSettings.index');
    }

    public function create()
    {
        abort_if(Gate::denies('employee_setting_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.employeeSettings.create', compact('created_bies'));
    }

    public function store(StoreEmployeeSettingRequest $request)
    {
        $employeeSetting = EmployeeSetting::create($request->all());

        return redirect()->route('admin.employee-settings.index');
    }

    public function edit(EmployeeSetting $employeeSetting)
    {
        abort_if(Gate::denies('employee_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $employeeSetting->load('created_by');

        return view('admin.employeeSettings.edit', compact('created_bies', 'employeeSetting'));
    }

    public function update(UpdateEmployeeSettingRequest $request, EmployeeSetting $employeeSetting)
    {
        $employeeSetting->update($request->all());

        return redirect()->route('admin.employee-settings.index');
    }

    public function show(EmployeeSetting $employeeSetting)
    {
        abort_if(Gate::denies('employee_setting_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employeeSetting->load('created_by');

        return view('admin.employeeSettings.show', compact('employeeSetting'));
    }

    public function destroy(EmployeeSetting $employeeSetting)
    {
        abort_if(Gate::denies('employee_setting_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employeeSetting->delete();

        return back();
    }

    public function massDestroy(MassDestroyEmployeeSettingRequest $request)
    {
        EmployeeSetting::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
