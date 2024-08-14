@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.sessionList.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.session-lists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.sessionList.fields.id') }}
                        </th>
                        <td>
                            {{ $sessionList->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sessionList.fields.name') }}
                        </th>
                        <td>
                            {{ $sessionList->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sessionList.fields.service') }}
                        </th>
                        <td>
                            {{ $sessionList->service->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sessionList.fields.image') }}
                        </th>
                        <td>
                            @if($sessionList->image)
                                <a href="{{ $sessionList->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $sessionList->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sessionList.fields.max_capacity') }}
                        </th>
                        <td>
                            {{ $sessionList->max_capacity }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.session-lists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection