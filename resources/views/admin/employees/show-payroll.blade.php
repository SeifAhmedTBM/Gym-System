@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        <i class="fa fa-user"></i> {{ trans('global.payroll') }} | <b>{{ $payroll->employee->name }}</b>
        <form action="{{ route('admin.payroll.print', $payroll->id) }}" class="d-inline float-right" method="POST">
            @csrf
            <input type="hidden" name="payroll_id" value="{{ $payroll->id }}">
            {{-- <button type="submit" class="btn btn-primary btn-xs">
                <i class="fa fa-print"></i> {{ trans('global.print') }}
            </button> --}}
        </form>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <img src="{{ asset('images/'. $settings->menu_logo) }}" alt="APP LOGO" width="200">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-6">
                <table class="table">
                    <thead class="bg-info border-0">
                        <tr>
                            <th class="text-center" colspan="2">{{ trans('global.gym_info') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.name') }}</td>
                            <td class="text-right">{{ $settings->name }}</td>
                        </tr>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.phone') }}</td>
                            <td class="text-right">{{ $settings->phone_numbers }}</td>
                        </tr>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.landline') }}</td>
                            <td class="text-right">{{ $settings->landline }}</td>
                        </tr>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.address') }}</td>
                            <td class="text-right">{{ $settings->address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 ml-auto">
                <table class="table">
                    <thead class="bg-info border-0">
                        <tr>
                            <th class="text-center" colspan="2">{{ trans('global.employee_info') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.name') }}</td>
                            <td class="text-right">{{ $payroll->employee->name ?? '-' }}</td>
                        </tr>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.phone') }}</td>
                            <td class="text-right">{{ $payroll->employee->phone ?? '-' }}</td>
                        </tr>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.finger_print_id') }}</td>
                            <td class="text-right">{{ $payroll->employee->finger_print_id != NULL ? $payroll->employee->finger_print_id : '---' }}</td>
                        </tr>
                        <tr class="bg-light text-dark">
                            <td class="font-weight-bold">{{ trans('global.payroll_month') }}</td>
                            <td class="text-right">{{ date('M', strtotime($payroll->created_at)) . ' ' . date('Y', strtotime($payroll->created_at)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <table class="table table-bordered table-hover table-striped mt-4">
            <thead class="thead-light">
                <tr>
                    <th class="text-dark">{{ trans('global.title') }}</th>
                    <th class="text-dark">{{ trans('global.data') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.basic_salary') }}</td>
                    <td>{{ number_format($payroll->basic_salary) }} EGP</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.deductions') }}</td>
                    <td>{{ number_format($payroll->deduction) }}  EGP</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('cruds.bonu.title') }}</td>
                    <td>{{ number_format($payroll->bonuses) }}  EGP</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.loan_deduction') }}</td>
                    <td>{{ number_format($payroll->loans) }}  EGP</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.bonuses') }}</td>
                    <td>{{ number_format($payroll->bonus) }}  EGP</td>
                </tr>
                <tr class="bg-secondary">
                    <td class="font-weight-bold text-white">{{ trans('global.net_salary') }}</td>
                    <td class="font-weight-bold text-white">
                        {{ number_format($payroll->net_salary) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection