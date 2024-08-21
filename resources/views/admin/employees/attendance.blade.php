@php
    $startOfMonth = now()->startOfMonth()->toDateString();
    $endOfMonth = now()->endOfMonth()->toDateString();
@endphp
@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <form action="{{ URL::current() }}" method="get">
            <div class="row">
                <div class="col-md-12">
                    <label for="date">{{ trans('global.date') }}</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="from" value="{{ request('from')??$startOfMonth }}">
                        <input type="date" class="form-control" name="to" value="{{ request('to')??$endOfMonth }}">
                        <select name="employee_id" class="form-control">
                            <option value="{{ NULL }}" selected>All Employees</option>
                            @foreach ($employees as $employee_id => $employee_name)
                                <option value="{{ $employee_id }}" {{ $employee_id == request('employee_id') ? 'selected' : '' }}>{{ $employee_name }}</option>
                            @endforeach
                        </select>
                        <select name="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                            <option value="{{ NULL }}" selected>All Branches</option>
                            @foreach ($branches as $branch_id => $branch_name)
                                <option value="{{ $branch_id }}" {{ $branch_id == request('branch_id') ? 'selected' : '' }}>{{ $branch_name }}</option>
                            @endforeach
                        </select>
                        <select name="role_id" class="form-control">
                            <option value="{{ NULL }}" selected>All Roles</option>
                            @foreach ($roles as $role_id => $role_name)
                                <option value="{{ $role_id }}" {{ $role_id == request('role_id') ? 'selected' : '' }}>{{ $role_name }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-prepend">
                            <button class="btn btn-primary" type="submit" ><i class="fa fa-search"></i></button>
                        </div>
                        @can('export_employee_attendances')
                            <a href="{{ route('admin.employees-attendances.export',request()->all()) }}" class="btn btn-info">
                                <i class="fa fa-download"></i>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="form-group">
        @if(Session::has('attend_successfully'))
            <div class="alert alert-success font-weight-bold text-center">
                <i class="fa fa-check-circle"></i> {{ session('attend_successfully') }} .
            </div>
        @endif
    </div>
    <div class="form-group row">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-4">
                    <form action="{{ route('admin.take_employee_attendance') }}" method="post">
                        @csrf
                        <h4>{{ trans('global.employee_attendances_info') }}</h4>
                        <input type="text" class="form-control form-control-lg bg-white text-dark" name="access_card" id="access_card" autofocus>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('cruds.user.fields.name') }}</th>
                                        <th>{{ trans('cruds.role.title_singular') }}</th>
                                        <th>{{ trans('cruds.branch.title_singular') }}</th>
                                        <th>{{ trans('global.clock_in') }}</th>
                                        <th>{{ trans('global.clock_out') }}</th>
                                        <th>{{ trans('global.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $attendance)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            @if($attendance->employee->user)
                                            <td>
                                                <a href="{{ route('admin.employees.show',$attendance->employee->id) }}" target="_blank">
                                                    {{ $attendance->employee->user->name ?? '-' }}
                                                </a>
                                            </td>
                                            @else
                                                <td>
                                                    <a href="{{ route('admin.employees.show',$attendance->employee->id) }}" target="_blank">
                                                        {{ $attendance->employee->name ?? '-' }}
                                                    </a>
                                                </td>
                                            @endif
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $attendance->employee->user->roles[0]->title ?? 'Employee'}}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $attendance->employee->branch->name ?? '-'}}
                                            </td>
                                            <td>{{ date("g:i A",strtotime($attendance->clock_in))  }}</td>
                                            @if(!is_null($attendance->clock_out))
                                                <td> {{ date("g:i:s A",strtotime($attendance->clock_out)) }} </td>
                                            @else
                                                <td> <span class="badge badge-success"><i class="fa fa-clock"></i> Still Working</span> </td>
                                            @endif
                                            <td>{{ date('Y-m-d',strtotime($attendance->created_at)) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection