@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-fingerprint"></i> {{ trans('global.employee_attendances') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <table class="table table-bordered">
                    <thead class="border-0">
                        <tr class="border-0">
                            <th class="border-0">{{ trans('global.employee_data') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.name') }}</td>
                            <td class="font-weight-bold">{{ $employee->name }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.phone') }}</td>
                            <td class="font-weight-bold">{{ $employee->phone }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('cruds.employee.fields.job_status') }}</td>
                            <td class="font-weight-bold">{{ App\Models\Employee::JOB_STATUS_SELECT[$employee->job_status] }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.start_date') }}</td>
                            <td class="font-weight-bold">{{ $employee->start_date }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.status') }}</td>
                            <td class="font-weight-bold">{{ $employee->status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead class="border-0">
                        <tr class="border-0">
                            <th class="border-0">{{ trans('global.attendance_data') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.finger_print_id') }}</td>
                            <td class="font-weight-bold">{{ $employee->finger_print_id }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.total_working_days') }}</td>
                            <td class="font-weight-bold">{{ $employee->attendances->where('absent', '!=', 'True')->count() }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.total_absence') }}</td>
                            <td class="font-weight-bold">{{ $employee->attendances->where('absent', 'True')->count() }}</td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.working_hours') }}</td>
                            <td class="font-weight-bold">
                                {{ $should_be_counted }}
                            </td>
                        </tr>
                        <tr class="border-0 px-2 bg-light rounded">
                            <td>{{ trans('global.actual_working_hours') }}</td>
                            <td class="font-weight-bold">
                                {{ $total_working_hours }}
                                <small>
                                    ( {{ trans('global.hours_should_be_counted', ['hours' => $difference]) }} )
                                </small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.attendances') }} 
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.date') }}</th>
                        <th>{{ trans('global.is_absent') }}</th>
                        <th>{{ trans('global.clock_in') }}</th>
                        <th>{{ trans('global.clock_out') }}</th>
                        <th>{{ trans('global.work_time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employee->attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance->date }}</td>
                            <td>
                                @if ($attendance->absent == 'True')
                                    <span class="badge badge-success">{{ trans('global.yes') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ trans('global.no') }}</span>
                                @endif
                            </td>
                            <td>{{ date('g:i A', strtotime($attendance->clock_in)) }}</td>
                            <td>{{ date('g:i A', strtotime($attendance->clock_out)) }}</td>
                            <td>{{ $attendance->work_time }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection