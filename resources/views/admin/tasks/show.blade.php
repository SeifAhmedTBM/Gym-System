@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.show') }} Task</h5>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.tasks.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.source.fields.id') }}
                            </th>
                            <td>
                                {{ $task->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Title
                            </th>
                            <td>
                                {{ $task->title ?? 'N/D' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Description
                            </th>
                            <td>
                                {!! $task->description ?? 'N/D' !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                To User
                            </th>
                            <td>
                                {{ $task->to_user->name ?? 'N/D' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                To Role
                            </th>
                            <td>
                                {{ $task->to_role->title ?? 'N/D' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Status
                            </th>
                            <td>
                                {{ $task->status ?? 'N/D' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Task Date
                            </th>
                            <td>
                                {{ $task->task_date ?? 'N/D' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Done
                            </th>
                            <td>
                                {{ $task->done_at ?? 'N/D' }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                Created At
                            </th>
                            <td>
                                {{ $task->created_at ?? 'N/D' }}
                            </td>
                        </tr>

                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.tasks.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
