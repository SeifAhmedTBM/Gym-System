@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.withdrawal.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.withdrawals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawal.fields.id') }}
                        </th>
                        <td>
                            {{ $withdrawal->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawal.fields.amount') }}
                        </th>
                        <td>
                            {{ $withdrawal->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawal.fields.notes') }}
                        </th>
                        <td>
                            {{ $withdrawal->notes }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawal.fields.account') }}
                        </th>
                        <td>
                            {{ $withdrawal->account->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawal.fields.created_by') }}
                        </th>
                        <td>
                            {{ $withdrawal->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.withdrawals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection