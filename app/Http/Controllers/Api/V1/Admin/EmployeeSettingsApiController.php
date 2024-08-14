<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeSettingRequest;
use App\Http\Requests\UpdateEmployeeSettingRequest;
use App\Http\Resources\Admin\EmployeeSettingResource;
use App\Models\EmployeeSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeSettingsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('employee_setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EmployeeSettingResource(EmployeeSetting::with(['created_by'])->get());
    }

    public function store(StoreEmployeeSettingRequest $request)
    {
        $employeeSetting = EmployeeSetting::create($request->all());

        return (new EmployeeSettingResource($employeeSetting))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(EmployeeSetting $employeeSetting)
    {
        abort_if(Gate::denies('employee_setting_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EmployeeSettingResource($employeeSetting->load(['created_by']));
    }

    public function update(UpdateEmployeeSettingRequest $request, EmployeeSetting $employeeSetting)
    {
        $employeeSetting->update($request->all());

        return (new EmployeeSettingResource($employeeSetting))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(EmployeeSetting $employeeSetting)
    {
        abort_if(Gate::denies('employee_setting_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employeeSetting->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
