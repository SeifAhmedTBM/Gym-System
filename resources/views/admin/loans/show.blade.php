@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.loan.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.loans.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.loan.fields.id') }}
                        </th>
                        <td>
                            {{ $loan->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loan.fields.employee') }}
                        </th>
                        <td>
                            {{ $loan->employee->job_status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loan.fields.name') }}
                        </th>
                        <td>
                            {{ $loan->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loan.fields.description') }}
                        </th>
                        <td>
                            {{ $loan->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loan.fields.amount') }}
                        </th>
                        <td>
                            {{ $loan->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loan.fields.created_by') }}
                        </th>
                        <td>
                            {{ $loan->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.loans.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection