@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.freezeRequest.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.freeze-requests.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.id') }}
                        </th>
                        <td>
                            {{ $freezeRequest->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.membership') }}
                        </th>
                        <td>
                            <a href="{{ route('admin.memberships.show',$freezeRequest->membership_id) }}">
                                {{ $freezeRequest->membership->service_pricelist->name ?? '' }} - {{ $freezeRequest->membership->service_pricelist->service->name ?? '' }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('global.start_date') }}
                        </th>
                        <td>
                            {{ $freezeRequest->membership->start_date ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('global.end_date') }}
                        </th>
                        <td>
                            {{ $freezeRequest->membership->end_date ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.freeze') }}
                        </th>
                        <td>
                            {{ $freezeRequest->freeze }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.start_date') }}
                        </th>
                        <td>
                            {{ $freezeRequest->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.end_date') }}
                        </th>
                        <td>
                            {{ $freezeRequest->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\FreezeRequest::STATUS_SELECT[$freezeRequest->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.created_by') }}
                        </th>
                        <td>
                            {{ $freezeRequest->created_by->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.freeze-requests.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection