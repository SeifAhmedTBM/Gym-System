@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.reason.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.reasons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.reason.fields.id') }}
                        </th>
                        <td>
                            {{ $reason->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reason.fields.title') }}
                        </th>
                        <td>
                            {{ $reason->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reason.fields.description') }}
                        </th>
                        <td>
                            {{ $reason->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reason.fields.image') }}
                        </th>
                        <td>
                            @if($reason->image)
                                <a href="{{ $reason->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $reason->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.reasons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection