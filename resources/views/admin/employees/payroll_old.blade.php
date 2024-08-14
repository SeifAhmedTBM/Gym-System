@extends('layouts.admin')
@section('content')
<form action="{{ route('admin.payroll.get') }}" method="get">
    <div class="row my-3">
        <div class="col-md-3">
            <label for="date">{{ trans('global.date') }}</label>
            <div class="input-group">
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fa fa-money-bill"></i> {{ trans('global.payroll') }}
    </div>
    <div class="card-body">
        <h4>Payroll : 
            <span class="badge badge-success px-2 py-2">
                {{ request('created_at.from') && request('created_at.to') ? request('created_at.from') . ' TO ' . request('created_at.to') : date('Y-m') }}
            </span>
        </h4>
        <div class="table-responsive">
            <table class="table table-bordered table-outline text-center table-hover">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        <th class="text-dark">{{ trans('global.labour_hours') }}</th>
                        <th class="text-dark">{{ trans('global.basic_salary') }}</th>
                        <th class="text-dark">{{ trans('global.working_hours') }}</th>
                        <th class="text-dark">{{ trans('global.gross_salary') }}</th>
                        <th class="text-dark">{{ trans('global.all_deductions') }}</th>
                        <th class="text-dark">{{ trans('global.bonuses') }}</th>
                        <th class="text-dark">{{ trans('global.net_salary') }}</th>
                        {{-- <th class="text-dark">{{ trans('global.actions') }}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        {{-- @php
                            $hoursRate = $employee->salary > 0 && $employee->days->sum('working_hours') > 0 ? $employee->salary / $employee->days->sum('working_hours') : 0;
                        @endphp --}}
                        <tr>
                            <td class="font-weight-bold">
                                <i class="far fa-user"></i> {{ $employee->name }}<br>
                                <i class="fa fa-phone"></i> {{ $employee->phone }}<br>
                                <span class="badge badge-info px-2 py-2">
                                    {{ $employee->user->roles[0]->title }}
                                </span>
                            </td>
                            {{-- <td>
                                {{ $employee->days->sum('working_hours') ?? '---' }} Hour/s
                            </td>
                            <td>
                                {{ number_format($employee->salary) . ' EGP' }}
                            </td>
                            <td>{{ $payrollData[$employee->id][1] }}</td>
                            <td>{{ number_format($hoursRate * $payrollData[$employee->id][0]) . ' EGP' }}</td>
                            <td>{{ $employee->deductions->sum('amount') . ' EGP' }}</td>
                            <td>{{ $employee->bonuses->sum('amount') . ' EGP' }}</td>
                            <td>
                                @if ($hoursRate > 0 && $payrollData[$employee->id][0] > 0)
                                    {{ ($hoursRate * $payrollData[$employee->id][0]) + $employee->bonuses->sum('amount') - $employee->deductions->sum('amount') . ' EGP' }}
                                @else
                                    0 EGP
                                @endif
                            </td> --}}
                            {{-- <td>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-check-circle"></i> {{ trans('global.confirm') }}
                                </button>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection