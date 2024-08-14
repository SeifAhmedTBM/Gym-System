@extends('layouts.admin')
@section('content')
    <a href="{{ route('admin.reports.trainers-report') }}" class="btn btn-danger mb-2">
        <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
    </a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5><i class="fas fa-dumbbell"></i> {{ trans('global.trainers_report') }} | {{ $trainer->name }}</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info shadow-sm font-weight-bold">
                <i class="fa fa-exclamation-circle"></i>
                {{ trans('global.this_month_records', ['month' => $commission['sales_tier_month']]) }} .
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered text-left rounded shadow-sm">
                                    <tbody>
                                        <tr>
                                            <td width="400" class="border-0 font-weight-bold">{{ trans('global.name') }}
                                            </td>
                                            <td class="border-0 font-weight-bold text-danger">{{ $trainer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 font-weight-bold">{{ trans('global.target_amount') }}</td>
                                            <td class="border-0 font-weight-bold text-danger">
                                                {{ $trainer->employee != NULL ? number_format($trainer->employee->target_amount) : 0 }} EGP</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 font-weight-bold">
                                                {{ trans('global.this_month_collected') }}</td>
                                            <td class="border-0 font-weight-bold text-success">
                                                {{ number_format($total) }} EGP</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 font-weight-bold">
                                                {{ trans('global.previous_month_collected') }}</td>
                                            <td class="border-0 font-weight-bold text-success">
                                                {{ number_format($pre_total) }} EGP</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table border-0 text-left rounded shadow-sm">
                                    <tbody class="border-0">
                                        <tr>
                                            <td class="border-0 font-weight-bold">
                                                {{ trans('global.this_month_sales_tier') }}</td>
                                            <td class="border-0 font-weight-bold text-success">
                                                {{ $commission['sales_tier'] . ' ( ' . number_format($commission['commission_value']) . '% )' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 font-weight-bold">
                                                {{ trans('global.this_month_commission') }}</td>
                                            <td class="border-0 font-weight-bold text-success">
                                                {{ number_format(floatVal($commission['commission'])) }} EGP</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 font-weight-bold">
                                                {{ trans('global.previous_month_commissions') }}</td>
                                            <td class="border-0 font-weight-bold text-success">
                                                {{ number_format(floatVal($previous_months_commissions)) . ' EGP' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 font-weight-bold">{{ trans('global.total_commissions') }}
                                            </td>
                                            <td class="border-0 font-weight-bold text-success">
                                                {{ gettype($commission['commission']) == 'integer' || gettype($commission['commission']) == 'double'? round($commission['commission']) + $previous_months_commissions: '0' }}
                                                EGP</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered text-center table-hover table-outline">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.lead.fields.member_code') }}</th>
                                <th class="text-dark">{{ trans('cruds.member.title_singular') }}</th>
                                <th class="text-dark">{{ trans('global.phone') }}</th>
                                <th class="text-dark">{{ trans('cruds.membership.title_singular') }}</th>
                                <th class="text-dark">{{ trans('cruds.invoice.title_singular') }}</th>
                                <th class="text-dark">{{ trans('global.session_cost') }}</th>
                                <th class="text-dark">{{ trans('global.attendance_count') }}</th>
                                <th class="text-dark">{{ trans('global.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td>#{{ $loop->iteration }}</td>
                                    <td>#{{ $report['member']['member_code'] }}</td>
                                    <td>{{ $report['member']['name'] }}</td>
                                    <td>{{ $report['member']['member_phone'] }}</td>
                                    <td>{{ $report['member']['service'] }}</td>
                                    <td class="font-weight-bold">
                                        <a style="letter-spacing: 2px" target="_blank"
                                            class="text-danger text-decoration-none"
                                            href="{{ route('admin.invoices.show', $report['member']['invoice_id']) }}">
                                            #{{ $report['member']['invoice_number'] }}
                                        </a>
                                        ({{ $report['member']['membership_cost'] }} EGP)
                                    </td>
                                    <td>{{ number_format($report['member']['session_cost']) }} EGP</td>
                                    <td>{{ number_format($report['member']['attendance_count']) }}</td>
                                    <td>{{ number_format($report['member']['sessions_total_cost']) }} EGP</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">{{ trans('global.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="text-center">
                                <td colspan="7"></td>
                                <td>{{ $total_attendance }}</td>
                                <td class="bg-secondary font-weight-bold">{{ number_format(floatVal($total)) }} EGP</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white font-weight-bold">
            <i class="fa fa-list"></i> {{ trans('global.last_month_data') }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered text-center table-hover table-outline">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.lead.fields.member_code') }}</th>
                                <th class="text-dark">{{ trans('cruds.member.title_singular') }}</th>
                                <th class="text-dark">{{ trans('global.phone') }}</th>
                                <th class="text-dark">{{ trans('cruds.membership.title_singular') }}</th>
                                <th class="text-dark">{{ trans('cruds.salesTier.title_singular') }}</th>
                                <th class="text-dark">{{ trans('cruds.invoice.title_singular') }}</th>
                                <th class="text-dark">{{ trans('global.session_cost') }}</th>
                                <th class="text-dark">{{ trans('global.attendance_count') }}</th>
                                <th class="text-dark">{{ trans('global.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($previous_reports as $pre_report)
                                <tr>
                                    <td>#{{ $loop->iteration }}</td>
                                    <td>#{{ $pre_report['member']['member_code'] }}</td>
                                    <td>{{ $pre_report['member']['name'] }}</td>
                                    <td>{{ $pre_report['member']['member_phone'] }}</td>
                                    <td>{{ $pre_report['member']['service'] }}</td>
                                    @if (\App\Models\SalesTier::where('month', $pre_report['member']['created_at'])->count() > 0)
                                        <td>{{ \App\Models\SalesTier::where('month', $pre_report['member']['created_at'])->first()->name }}
                                        </td>
                                    @else
                                        <td> - No Trainer Tier - </td>
                                    @endif
                                    <td class="font-weight-bold">
                                        <a style="letter-spacing: 2px" target="_blank"
                                            class="text-danger text-decoration-none"
                                            href="{{ route('admin.invoices.show', $pre_report['member']['invoice_id']) }}">
                                            #{{ $pre_report['member']['invoice_number'] }}
                                        </a>
                                        ({{ number_format($pre_report['member']['membership_cost']) }} EGP)
                                    </td>
                                    <td>{{ number_format($pre_report['member']['session_cost']) }} EGP</td>
                                    <td>{{ $pre_report['member']['attendance_count'] }}</td>
                                    <td>{{ number_format($pre_report['member']['sessions_total_cost']) }} EGP</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">{{ trans('global.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="text-center">
                                <td colspan="8"></td>
                                <td>{{ $pre_total_attendance }}</td>
                                <td class="bg-secondary font-weight-bold">{{ number_format(floatVal($pre_total)) }} EGP
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
