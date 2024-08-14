@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.vacation.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vacations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.id') }}
                        </th>
                        <td>
                            {{ $vacation->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.employee') }}
                        </th>
                        <td>
                            {{ $vacation->employee->job_status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.name') }}
                        </th>
                        <td>
                            {{ $vacation->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.description') }}
                        </th>
                        <td>
                            {{ $vacation->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.from') }}
                        </th>
                        <td>
                            {{ $vacation->from }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.to') }}
                        </th>
                        <td>
                            {{ $vacation->to }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.diff') }}
                        </th>
                        <td>
                            {{ $vacation->diff }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vacation.fields.created_by') }}
                        </th>
                        <td>
                            {{ $vacation->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vacations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection