@extends('layouts.admin')
@section('content')
@can('import_employee_attendances')
<div class="row">
    <div class="col-3">
        <a href="{{ route('admin.employee-attendance.index') }}" type="button" data-toggle="modal" data-target="#importAttendanceModal" class="btn mb-2 btn-danger">
            <i class="fa fa-upload"></i> {{ trans('global.import_fp_sheet') }}
        </a>
    </div>
    <div class="col-2">
        @include('admin_includes.filters', [
        'columns' => [
            'name' => ['label' => 'Employee', 'type' => 'text' , 'related_to' => 'employee'],
            'finger_print_id' => ['label' => 'Finger Print ID', 'type' => 'number'],
        ],
            'route' => 'admin.employee-attendance.index'
        ])
    </div>
</div>
    

    
@endcan
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-fingerprint"></i> {{ trans('global.employee_attendances') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.name') }}</th>
                        <th>{{ trans('global.finger_print_id') }}</th>
                        <th>{{ trans('global.total_absence') }}</th>
                        <th>{{ trans('global.total_working_days') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employee_attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance->first()->employee->name }}</td>
                            <td>{{ $attendance->first()->employee->finger_print_id }}</td>
                            <td>{{ $attendance->where('absent', 'True')->count() }}</td>
                            <td>{{ $attendance->where('absent', '!=', 'True')->count() }}</td>
                            <td>
                                <a href="{{ route('admin.employee-attendance.show', $attendance->first()->finger_print_id) }}" class="btn btn-primary">
                                    {{ trans('global.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Attendance Modal -->
<div class="modal fade" id="importAttendanceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.import_fp_sheet') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'action' => 'Admin\EmployeeAttendanceController@store', 'files' => true]) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('file', trans('global.upload') . ' ' . trans('global.file')) !!} <br>
                    {!! Form::file('file', null) !!} <br>
                    <small class="font-weight-bold">
                        ( {{ trans('global.csv_or_xlsx') }} )
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ asset('template.xlsx') }}" download class="btn btn-primary float-left">
                    <i class="fa fa-download"></i> {{ trans('global.download_sample') }}
                </a>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ trans('global.save') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection