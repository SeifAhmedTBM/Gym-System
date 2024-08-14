@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.reminder.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.reminders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.id') }}
                        </th>
                        <td>
                            {{ $reminder->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.comment') }}
                        </th>
                        <td>
                            {{ $reminder->comment }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.due_date') }}
                        </th>
                        <td>
                            {{ $reminder->due_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Reminder::STATUS_SELECT[$reminder->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.lead') }}
                        </th>
                        <td>
                            {{ $reminder->lead->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.user') }}
                        </th>
                        <td>
                            {{ $reminder->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.reminder.fields.member_status') }}
                        </th>
                        <td>
                            {{ $reminder->member_status->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.reminders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection