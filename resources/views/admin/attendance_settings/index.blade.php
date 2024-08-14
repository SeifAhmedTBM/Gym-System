@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
       <h5> <i class="fa fa-minus-circle"></i> {{ trans('global.delay_rules') }}</h5>
    </div>
    <div class="card-body">
        @can('create_attendance_settings')
            <a href="{{ route('admin.attendance-settings.create') }}" class="btn btn-primary float-right mb-2">
                <i class="fa fa-plus-circle"></i> {{ trans('global.add') }}
            </a>
        @endcan
        <div class="table-responsive">
            <table class="table table-bordered text-center table-hover table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.from') }}</th>
                        <th>{{ trans('global.to') }}</th>
                        <th>{{ trans('global.deduction') }} ( <small class="font-weight-bold">{{ trans('global.by_day') }}</small> )</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendance_settings as $attendance_setting)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance_setting->from }} {{ trans('global.minutes') }}</td>
                            <td>{{ $attendance_setting->to }} {{ trans('global.minutes') }}</td>
                            <td>{{ $attendance_setting->deduction }}</td>
                            <td>
                                @can('edit_attendance_settings')
                                    <a href="{{ route('admin.attendance-settings.edit', $attendance_setting->id) }}" class="btn btn-success btn-xs">
                                        <i class="fa fa-edit"></i> {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('delete_attendance_settings')
                                    @component('components.delete.button')
                                        @slot('id' , $attendance_setting->id)
                                    @endcomponent
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@component('components.delete.modal') @endcomponent
@endsection

@section('scripts')
    @component('components.delete.script') @slot('route', 'admin.attendance-settings.destroy') @endcomponent
@endsection