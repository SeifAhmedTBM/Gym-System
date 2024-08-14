@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.externalPayment.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.external-payments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.externalPayment.fields.id') }}
                        </th>
                        <td>
                            {{ $externalPayment->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.externalPayment.fields.title') }}
                        </th>
                        <td>
                            {{ $externalPayment->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.lead.title_singular') }}
                        </th>
                        <td>
                            {{ $externalPayment->lead->name ?? '---' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.externalPayment.fields.amount') }}
                        </th>
                        <td>
                            {{ $externalPayment->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.externalPayment.fields.notes') }}
                        </th>
                        <td>
                            {{ $externalPayment->notes }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.externalPayment.fields.account') }}
                        </th>
                        <td>
                            {{ $externalPayment->account->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.externalPayment.fields.created_by') }}
                        </th>
                        <td>
                            {{ $externalPayment->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.external-payments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection