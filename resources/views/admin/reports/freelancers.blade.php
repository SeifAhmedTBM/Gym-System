@extends('layouts.admin')
@section('content')
    <form action="{{ route('admin.reports.freelancers-report') }}" method="get">
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
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-users"></i> {{ trans('global.freelancers_report') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center table-bordered table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ trans('global.name') }}</th>
                            <th>{{ trans('global.memberships') }}</th>
                            <th>{{ trans('global.target') }}</th>
                            <th>{{ trans('cruds.payment.title') }}</th>
                            <th>{{ trans('global.achieved') }}</th>
                            <th>{{ trans('global.commission') }} ( A )</th>
                            <th>{{ trans('global.commission') }} ( % )</th>
                            {{-- <th>{{ trans('global.due') }}</th>
                            <th>{{ trans('global.collected') }}</th>
                            <th>{{ trans('global.net') }}</th>
                            <th>{{ trans('global.commission') }}</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($freelancers as $key => $freelancer)
                            <tr>
                                <td>{{ $freelancers->name }}</td>
                                <td>{{ $freelancers->memberships_count }}</td>
                                <td>{{ number_format($freelancer->employee->target_amount ?? 0) }} EGP</td>
                                <td>
                                    {{ number_format($freelancer->payments->sum('amount')) ?? 0 }} EGP ({{ $freelancer->payments->count() }})
                                </td>
                                <td>
                                    @isset($freelancer->payments)
                                        @isset($freelancer->sales_tier->sales_tier)
                                        {{ round(($freelancer->payments->sum('amount') / $freelancer->employee->target_amount) *100) }}
                                            %
                                        @else
                                            {{ trans('global.there_is_no_sales_tier') }}
                                        @endisset
                                    @endisset
                                </td>
                                <td>
                                    @isset($freelancer->payments)
                                        @isset($freelancer->sales_tier->sales_tier)
                                            @php
                                                $freelancer_payments = $sale->payments->sum('amount');
                                                if ($freelancer_payments) 
                                                {
                                                    $achieved = ($freelancer_payments / $freelancer->employee->target_amount) *100;
                                                    $freelancer_sales_tier_commission = ($freelancer->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved)->orderBy('range_from','desc')->first()->commission ?? 0);
                                                }
                                            @endphp
                                            {{ $freelancer_payments ? $freelancer_sales_tier_commission : 0 }} EGP
                                        @else
                                            {{ trans('global.there_is_no_sales_tier') }}
                                        @endisset
                                    @endisset
                                </td>
                                <td>
                                    @isset($freelancer->payments)
                                        @isset($freelancer->sales_tier->sales_tier)
                                            @php
                                                $freelancer_payments = $sale->payments->sum('amount');
                                                if ($freelancer_payments) 
                                                {
                                                    $achieved = ($freelancer_payments / $freelancer->employee->target_amount) *100;
                                                    $freelancer_sales_tier_commission = ($freelancer->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved)->orderBy('range_from','desc')->first()->commission ?? 0);
                                                }
                                                
                                            @endphp
                                            {{ $freelancer_payments ? $freelancer_sales_tier_commission  : 0 }} %
                                        @else
                                            {{ trans('global.there_is_no_sales_tier') }}
                                        @endisset
                                    @endisset
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ trans('global.no_data_available') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- {{ $freelancers->links() }} --}}
        </div>
    </div>
@endsection