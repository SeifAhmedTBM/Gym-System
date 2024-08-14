@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.memberStatus.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.member-statuses.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.memberStatus.fields.id') }}
                        </th>
                        <td>
                            {{ $memberStatus->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberStatus.fields.name') }}
                        </th>
                        <td>
                            {{ $memberStatus->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberStatus.fields.default_next_followup_days') }}
                        </th>
                        <td>
                            {{ $memberStatus->default_next_followup_days }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberStatus.fields.need_followup') }}
                        </th>
                        <td>
                            {{ App\Models\MemberStatus::NEED_FOLLOWUP_SELECT[$memberStatus->need_followup] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.member-statuses.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection