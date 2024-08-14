@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.refund.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.refunds.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.refund.fields.id') }}
                        </th>
                        <td>
                            {{ $refund->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.refund.fields.refund_reason') }}
                        </th>
                        <td>
                            {{ $refund->refund_reason->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.refund.fields.invoice') }}
                        </th>
                        <td>
                            {{ $refund->invoice->discount ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.refund.fields.amount') }}
                        </th>
                        <td>
                            {{ $refund->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.refund.fields.created_by') }}
                        </th>
                        <td>
                            {{ $refund->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.refunds.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection