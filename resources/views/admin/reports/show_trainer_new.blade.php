@extends('layouts.admin')
@section('content')
    <a href="{{ route('admin.reports.trainers-report') }}" class="btn btn-danger mb-2">
        <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
    </a>

    <form action="{{ URL::current() }}" method="get">
        <div class="row form-group">
            <div class="col-md-8">
                <label for="date">{{ trans('global.filter') }}</label>
                <div class="input-group">
                    <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <h5 class="text-center">{{ request()->date ? date('F Y',strtotime(request()->date)) : date('F Y',strtotime('Y m')) }}</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body  text-center">
                            <h5 class="fs-4 fw-semibold">{{ $trainer->trainer_memberships_count }}</h5>
                                Memberships
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body  text-center">
                            <h5 class="fs-4 fw-semibold">{{  number_format($trainer->employee->target_amount ?? 0) }} EGP</h5>
                            {{ trans('global.target') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fs-4 fw-semibold">{{ number_format($trainer->trainer_memberships->sum('payments_sum_amount')) ?? 0 }} EGP ({{ $trainer->trainer_memberships->sum('payments_count') }})</h5>
                            Payments this month
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fs-4 fw-semibold">
                                {{ number_format($trainer->previous_trainer_memberships->sum('payments_sum_amount')) ?? 0 }} EGP 
                                ({{ $trainer->previous_trainer_memberships->sum('payments_count') }})
                            </h5>
                            Payments last month
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fs-4 fw-semibold">
                                {{ number_format($trainer->trainer_memberships->sum('attendances_count')) ?? 0 }} 
                            </h5>
                            Attendances this month
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fs-4 fw-semibold">
                                {{ number_format($trainer->previous_trainer_memberships->sum('attendances_count')) ?? 0 }} 
                            </h5>
                            Attendances last month
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- service payments --}}
    <div class="form-group row">
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
                                @forelse ($trainer_service_payments as $key => $payments)
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
                        </div>
                        <div class="col-md-6">
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
    {{-- service payments --}}

    {{-- payments= --}}
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
                            @foreach ($trainer_payments as $payment)
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
    {{-- payments --}}
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx2 = document.getElementById('myChart2').getContext('2d');
        const myChart2 = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: [
                    @php
                    foreach ($trainer_service_payments as $key => $payments) {
                        echo "'" . $key . "'" . ',';
                    }
                    @endphp
                ],
                datasets: [{
                    label: '# of Votes',
                    data: [
                        @php
                        foreach ($trainer_service_payments as $key => $payments) {
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
                        
                        foreach ($trainer_service_payments as $key => $payments) {
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