@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-center">{{ request()->date ? date('F Y',strtotime(request()->date)) : date('F Y',strtotime('Y m')) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body  text-center">
                    <h5 class="fs-4 fw-semibold">{{ $sale->memberships_count }}</h5>
                        {{ trans('cruds.membership.title') }}
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body  text-center">
                    <h5 class="fs-4 fw-semibold">{{  number_format($sale->employee->target_amount) }} EGP</h5>
                    {{ trans('global.target') }}
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body text-center">
                    <h5 class="fs-4 fw-semibold">{{ number_format($sale->payments->sum('amount')) ?? 0 }} EGP ({{ $sale->payments->count() }})</h5>
                    {{ trans('cruds.payment.title') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body  text-center">
                    <h5 class="fs-4 fw-semibold">
                        @isset($sale->payments)
                            @isset($sale->sales_tier->sales_tier)
                            {{ round(($sale->payments->sum('amount') / $sale->employee->target_amount) * 100) }}
                                %
                            @else
                                {{ trans('global.there_is_no_sales_tier') }}
                            @endisset
                        @endisset
                    </h5>
                        {{ trans('global.achieved') }}
                </div>
            </div>
        </div>
        <!-- /.col-->
        
        
        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body  text-center">
                    <h5 class="fs-4 fw-semibold">
                        @isset($sale->payments)
                            @isset($sale->sales_tier->sales_tier)
                            @php
                                $sales_payments = $sale->payments->sum('amount');
                                if ($sales_payments) 
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
                    </h5>
                    {{ trans('global.commission') }} ( A )
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body text-center">
                    <h5 class="fs-4 fw-semibold">
                        @isset($sale->payments)
                            @isset($sale->sales_tier->sales_tier)
                                @php
                                    $sales_payments = $sale->payments->sum('amount');
                                    if ($sales_payments) 
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
                    </h5>
                    {{ trans('global.commission') }} ( % )
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fa fa-file"></i> {{ trans('cruds.service.title') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th class="text-dark">{{ trans('cruds.source.fields.name') }}</th>
                                    <th class="text-dark">{{ trans('global.count') }}</th>
                                    <th class="text-dark">{{ trans('global.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($service_payments as $key => $payments)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key }}</td>
                                        <td>{{ $payments->count() }}</td>
                                        <td>{{ number_format($payments->sum('amount')) }}</td>
                                    </tr>
                                @empty
                                    <td colspan="4" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-right font-weight-bold">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- {{ trans('global.total') }} : {{ $service_payments }} --}}
                            {{-- {{ trans('cruds.membership.title_singular') }} --}}
                        </div>
                        <div class="col-md-6">
                            {{-- {{ trans('global.total').'  '.trans('global.amount') }} : {{ number_format($serviceReport->sum('payments')) .' EGP' }} --}}
                            {{-- {{ trans('global.amount') }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.service.title') }} Chart
                </div>
                <div class="card-body">
                    <div class="col">
                        <canvas id="myChart2" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fa fa-file"></i> {{ trans('cruds.source.title') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th class="text-dark">{{ trans('cruds.source.fields.name') }}</th>
                                    <th class="text-dark">{{ trans('global.count') }}</th>
                                    <th class="text-dark">{{ trans('global.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sources_members as $key => $sources)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key }}</td>
                                        <td>{{ $sources->count() }}</td>
                                        <td>{{ number_format($sources->sum('amount')) }}</td>
                                    </tr>
                                @empty
                                    <td colspan="4" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                   {{ trans('cruds.source.title') }} Chart
                </div>
                <div class="card-body">
                    <div class="col">
                        <canvas id="myChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.payment.title') }}
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-striped zero-configuration">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('cruds.member.fields.name') }}</th>
                                <th>{{ trans('cruds.payment.fields.invoice') }}</th>
                                <th>{{ trans('cruds.service.title_singular') }}</th>
                                <th>{{ trans('cruds.invoice.fields.account') }}</th>
                                <th>{{ trans('cruds.payment.fields.amount') }}</th>
                                
                                <th>{{ trans('cruds.payment.fields.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->payments as $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('admin.members.show',$payment->invoice->membership->member_id) }}" target="_blank" rel="noopener noreferrer">
                                            <b>
                                                {{ \App\Models\Setting::first()->member_prefix.$payment->invoice->membership->member->member_code ?? ''}}
                                            </b>
                                            <br>
                                            <b>
                                                {{ $payment->invoice->membership->member->name  ?? '-' }}
                                            </b>
                                            <br>
                                            <b>
                                                {{ $payment->invoice->membership->member->phone  ?? '-' }}
                                            </b>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.invoices.show',$payment->invoice_id) }}" target="_blank">{{ \App\Models\Setting::first()->invoice_prefix.$payment->invoice_id }}</a>
                                    </td>
                                    <td>
                                        {{ $payment->invoice->membership->service_pricelist->name ?? '-' }}
                                    </td>
                                    <td>{{ $payment->account->name ?? '-' }}</td>
                                    <td>{{ number_format($payment->amount) }}</td>
                                    
                                    <td>
                                        {{ $payment->created_at }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    @php
                        foreach ($sources_members as $key => $sources) {
                            echo "'" . $key . "'" . ',';
                        }
                    @endphp
                ],
                datasets: [{
                    label: '# of Votes',
                    data: [
                        @php
                            foreach ($sources_members as $key => $sources) {
                                echo "'" . $sources->sum('amount'). "'" . ',';
                            }
                        @endphp
                    ],
                    backgroundColor: [
                        @php
                            function random_color_part()
                            {
                                return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
                            }
                            
                            function random_color()
                            {
                                return random_color_part() . random_color_part() . random_color_part();
                            }
                            
                            foreach ($sources_members as $key => $sources) {
                                echo "'#" . random_color() . "',";
                            }
                        @endphp
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script>
        const ctx2 = document.getElementById('myChart2').getContext('2d');
        const myChart2 = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: [
                    @php
                    foreach ($service_payments as $key => $payments) {
                        echo "'" . $key . "'" . ',';
                    }
                    @endphp
                ],
                datasets: [{
                    label: '# of Votes',
                    data: [
                        @php
                        foreach ($service_payments as $key => $payments) {
                            echo "'" . $payments->sum('amount'). "'" . ',';
                        }
                        @endphp
                    ],
                    backgroundColor: [
                        @php
                        function random_color_part2()
                        {
                            return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
                        }
                        
                        function random_color2()
                        {
                            return random_color_part2() . random_color_part2() . random_color_part2();
                        }
                        
                        foreach ($service_payments as $key => $payments) {
                            echo "'#" . random_color2() . "',";
                        }
                        @endphp
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    
@endsection
