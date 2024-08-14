@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.employeeSetting.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.employee-settings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.id') }}
                        </th>
                        <td>
                            {{ $employeeSetting->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.start_date') }}
                        </th>
                        <td>
                            {{ $employeeSetting->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.end_date') }}
                        </th>
                        <td>
                            {{ $employeeSetting->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.start_time') }}
                        </th>
                        <td>
                            {{ $employeeSetting->start_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.end_time') }}
                        </th>
                        <td>
                            {{ $employeeSetting->end_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.default_month_days') }}
                        </th>
                        <td>
                            {{ $employeeSetting->default_month_days }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.default_vacation_days') }}
                        </th>
                        <td>
                            {{ $employeeSetting->default_vacation_days }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeSetting.fields.created_by') }}
                        </th>
                        <td>
                            {{ $employeeSetting->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.employee-settings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection