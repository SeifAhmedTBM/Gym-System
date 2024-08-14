@extends('layouts.admin')
@section('content')
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fa fa-list"></i> {{ trans('global.schedule_templates') }}
    </div>
    <div class="card-body">
        @can('add_schedule_template')
        <a href="{{ route('admin.schedule-templates.create') }}" class="btn btn-primary btn-sm float-right mb-2">
            <i class="fa fa-plus-circle"></i> {{ trans('global.add_schedule_template') }}
        </a>
        @endcan
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.name') }}</th>
                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedule_templates as $schedule_template)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $schedule_template->name }}</td>
                            <td>{{ $schedule_template->user->name }}</td>
                            <td>{{ $schedule_template->created_at->toFormattedDateString() }}</td>
                            <td>
                                @can('edit_schedule_template')
                                    <a href="{{ route('admin.schedule-templates.edit', [$schedule_template->id,'date' => date('Y-m',strtotime($schedule_template->days()->first()->day))]) }}" class="btn btn-xs btn-success">
                                        <i class="fa fa-edit"></i> {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('delete_schedule_template')
                                    @component('components.delete.button')
                                        @slot('id', $schedule_template->id)
                                    @endcomponent
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $schedule_templates->links() }}
    </div>
</div>
@component('components.delete.modal') @endcomponent
@endsection

@section('scripts')
    @component('components.delete.script') 
        @slot('route', 'admin.schedule-templates.destroy')
    @endcomponent
@endsection