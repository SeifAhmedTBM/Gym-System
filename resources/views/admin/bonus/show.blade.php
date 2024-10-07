@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.bonu.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bonus.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.bonu.fields.id') }}
                        </th>
                        <td>
                            {{ $bonu->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bonu.fields.employee') }}
                        </th>
                        <td>
                            {{ $bonu->employee->job_status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bonu.fields.name') }}
                        </th>
                        <td>
                            {{ $bonu->employee->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bonu.fields.reason') }}
                        </th>
                        <td>
                            {{ $bonu->reason }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bonu.fields.amount') }}
                        </th>
                        <td>
                            {{ $bonu->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bonu.fields.created_by') }}
                        </th>
                        <td>
                            {{ $bonu->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.bonus.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection