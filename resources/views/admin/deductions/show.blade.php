@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.deduction.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.deductions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.deduction.fields.id') }}
                        </th>
                        <td>
                            {{ $deduction->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deduction.fields.employee') }}
                        </th>
                        <td>
                            {{ $deduction->employee->job_status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deduction.fields.name') }}
                        </th>
                        <td>
                            {{ $deduction->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deduction.fields.reason') }}
                        </th>
                        <td>
                            {{ $deduction->reason }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deduction.fields.amount') }}
                        </th>
                        <td>
                            {{ $deduction->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deduction.fields.created_by') }}
                        </th>
                        <td>
                            {{ $deduction->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.deductions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection