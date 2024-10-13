@extends('layouts.admin')
@section('content')

    <form action="{{ route('admin.reports.sales-report') }}" method="get">
        <div class="row form-group">
            <div class="col-md-8">
                <label for="date">{{ trans('global.filter') }}</label>
                <div class="input-group">
                    <select name="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>All Branches</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="sales_by_id" class="form-control" {{ $employee && Auth()->user()->roles[0]->title == 'Sales' ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>Sales By</option>
                        @if($employee && $employee->branch_id != NULL)
                        @foreach (\App\Models\User::whereRelation('roles','title','Sales')->whereRelation('employee','branch_id',$employee->branch_id)->whereRelation('employee','status','active')->pluck('name','id') as $sales_by_id => $sales_by_name)
                            <option value="{{ $sales_by_id }}" {{ request('sales_by_id') == $sales_by_id ? 'selected' : '' }}>{{ $sales_by_name }}</option>
                        @endforeach
                        @else
                            @foreach (\App\Models\User::whereRelation('roles','title','Sales')->whereRelation('employee','status','active')->pluck('name','id') as $sales_by_id => $sales_by_name)
                                <option value="{{ $sales_by_id }}" {{ request('sales_by_id') == $sales_by_id ? 'selected' : '' }}>{{ $sales_by_name }}</option>
                            @endforeach
                        @endif
                    </select>

                    <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-2 col-sm-12 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{ trans('global.total') }}</h2>
                        <h2 class="text-center">{{ number_format($sales->sum('payments_sum_amount')) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-file"></i> {{ trans('global.sales_report') }}</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ trans('global.name') }}</th>
                            <th>{{ trans('cruds.branch.title_singular') }}</th>
                            <th>{{ trans('global.memberships') }}</th>
                            <th>{{ trans('global.target') }}</th>
                            <th>{{ trans('cruds.payment.title') }}</th>
                            <th>{{ trans('global.achieved') }}</th>
                            <th>{{ trans('global.commission') }} ( A )</th>
                            <th>{{ trans('global.commission') }} ( % )</th>
                            <th>{{ trans('global.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $key => $sale)
                            @if($sale->employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sale->name }}</td>
                                <td>{{ $sale->employee->branch->name ?? '-' }}</td>
                                <td>{{ $sale->memberships_count }}</td>
                                <td>{{ number_format($sale->employee->target_amount ?? 0) }} EGP</td>
                                <td>
                                    {{ number_format($sale->payments->sum('amount')) ?? 0 }} EGP ({{ $sale->payments->count() }})
                                </td>
                                <td>
                                    @if(isset($sale->payments) && $sale->employee->target_amount > 0)
                                        @isset($sale->sales_tier->sales_tier)
                                        {{ round(($sale->payments->sum('amount') / $sale->employee->target_amount) * 100) }}
                                            %
                                        @else
                                            {{ trans('global.there_is_no_sales_tier') }}
                                        @endisset
                                    @endif
                                </td>
                                <td>

                                    @isset($sale->payments)
                                        @isset($sale->sales_tier->sales_tier)
                                        @php
                                            $sales_payments = $sale->payments->sum('amount');
                                            if ($sales_payments && $sale->employee->target_amount > 0)
                                            {
                                                $achieved = ($sales_payments / $sale->employee->target_amount) *100;

                                                $sales_sales_tier_amount = ($sales_payments * $sale->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved)->orderBy('range_from','desc')->first()->commission) / 100;
                                            }
                                        @endphp
                                        {{ $sales_payments ? $sales_sales_tier_amount : 0 }} EGP
                                        @else
                                            {{ trans('global.there_is_no_sales_tier') }}
                                        @endisset
                                    @endisset
                                </td>
                                <td>
                                    @isset($sale->payments)
                                        @isset($sale->sales_tier->sales_tier)
                                            @php
                                                $sales_payments = $sale->payments->sum('amount');
                                                if ($sales_payments && $sale->employee->target_amount > 0)
                                                {
                                                    $achieved = ($sales_payments / $sale->employee->target_amount) *100;
                                                    $sales_sales_tier_commission = ($sale->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved)->orderBy('range_from','desc')->first()->commission ?? 0);
                                                }

                                            @endphp
                                            {{ $sales_payments ? $sales_sales_tier_commission  : 0 }} %
                                        @else
                                            {{ trans('global.there_is_no_sales_tier') }}
                                        @endisset
                                    @endisset
                                </td>
                                <td>
                                    <a href="{{ route('admin.reports.sales-report.view',[$sale->id,'date='.request()->date]) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> {{ trans('global.view') }}</a>
                                </td>
                            </tr>
                            @endif
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
            {{-- {{ $sales->links() }} --}}
        </div>
    </div>
@endsection
