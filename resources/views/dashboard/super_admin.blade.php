@if (config('domains')[config('app.url')]['profile_attendance_dashboard'] == true)@endif
@if (config('domains')[config('app.url')]['timeline_schedule'] == true)
  <h4><i class="fa fa-calendar"></i> {{ trans('global.schedule_timeline') }}</h4>
@include('partials.schedule')
@endif

    <!-- @include('partials.profile_attendance') -->

<div class="form-group">
    <form action="{{ route('admin.home') }}" method="get">
        <div class="row">
            <div class="col-md-6">
                <label for="date">{{ trans('global.date') }}</label>
                <div class="input-group">
                    <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="form-group">
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.branch.title') }}
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Branch</th>
                        <th class="bg-primary">Income</th>
                        <th class="bg-danger">Outcome</th>
                        <th class="bg-success">Net Income</th>
                        <th class="bg-secondary">Partner</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_income = 0;
                        $total_outcome = 0;
                        $total_net = 0;
                        $partner_total = 0;
                    @endphp
                    @foreach ($branches as $branch)
                        @php
                            $tran_payments = $branch->transactions->where('transactionable_type', 'App\Models\Payment')->sum('amount');
                            $tran_externalPayments = $branch->transactions->where('transactionable_type', 'App\Models\ExternalPayment')->sum('amount');
                            $income = ($tran_payments + $tran_externalPayments);

                            $tran_refunds = $branch->transactions->where('transactionable_type', 'App\Models\Refund')->sum('amount');
                            $tran_loans = $branch->transactions->where('transactionable_type', 'App\Models\Loan')->sum('amount');
                            $tran_expenses = $branch->transactions->where('transactionable_type', 'App\Models\Expense')->sum('amount');
                            $total_comission = 0;
                            $tran_expenses_commission = $branch->transactions->whereIn('transactionable_type', ['App\Models\Payment','App\Models\ExternalPayment']);
                            foreach ($tran_expenses_commission as $key => $tr) {
                                if($tr->account->commission_percentage > 0 ){
                                    $total_comission =  $total_comission + $tr->amount * ($tr->account->commission_percentage/100);
                                }
                            }
                            $outcome = ($tran_refunds + $tran_loans + $tran_expenses + $total_comission);

                            $total_income = $total_income +$income;
                            $total_outcome = $total_outcome +$outcome;
                            $total_net = $total_net +($income - $outcome);

                            $partner_net = ((($income - $outcome) * $branch->partner_percentage)/100);
                            $partner_total += $partner_net;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $branch->name }}</td>
                            <td class="bg-primary">{{ number_format($income) }}</td>
                            <td class="bg-danger">{{ number_format($outcome) }}</td>
                            <td class="bg-success">{{ number_format($income - $outcome) }}</td>
                            <td class="bg-secondary">{{ number_format($partner_net) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="bg-success" colspan="2" style="text-align:center;">Total</td>
                        <td class="bg-success"> {{ number_format($total_income) }} </td>
                        <td class="bg-success"> {{ number_format($total_outcome) }} </td>
                        <td class="bg-success"> {{ number_format($total_net) }} </td>
                        <td class="bg-success"> {{ number_format($partner_total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Last Month Stat Start from {{$startOfLastMonth->format('Y-m-d')}} To : {{$endOfLastMonth->format('Y-m-d')}}
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Branch</th>
                        <th class="bg-primary">Income</th>
                        <th class="bg-danger">Outcome</th>
                        <th class="bg-success">Net Income</th>
                        <th class="bg-secondary">Partner</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_income = 0;
                        $total_outcome = 0;
                        $total_net = 0;
                        $partner_total = 0;
                    @endphp
                    @foreach ($lastMonthBranchesTransactions as $branch)
                        @php
                            $tran_payments = $branch->transactions->where('transactionable_type', 'App\Models\Payment')->sum('amount');
                            $tran_externalPayments = $branch->transactions->where('transactionable_type', 'App\Models\ExternalPayment')->sum('amount');
                            $income = ($tran_payments + $tran_externalPayments);

                            $tran_refunds = $branch->transactions->where('transactionable_type', 'App\Models\Refund')->sum('amount');
                            $tran_loans = $branch->transactions->where('transactionable_type', 'App\Models\Loan')->sum('amount');
                            $tran_expenses = $branch->transactions->where('transactionable_type', 'App\Models\Expense')->sum('amount');
                            $total_comission = 0;
                            $tran_expenses_commission = $branch->transactions->whereIn('transactionable_type', ['App\Models\Payment','App\Models\ExternalPayment']);
                            foreach ($tran_expenses_commission as $key => $tr) {
                                if($tr->account->commission_percentage > 0 ){
                                    $total_comission =  $total_comission + $tr->amount * ($tr->account->commission_percentage/100);
                                }
                            }
                            $outcome = ($tran_refunds + $tran_loans + $tran_expenses + $total_comission);

                            $total_income = $total_income +$income;
                            $total_outcome = $total_outcome +$outcome;
                            $total_net = $total_net +($income - $outcome);

                            $partner_net = ((($income - $outcome) * $branch->partner_percentage)/100);
                            $partner_total += $partner_net;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $branch->name }}</td>
                            <td class="bg-primary">{{ number_format($income) }}</td>
                            <td class="bg-danger">{{ number_format($outcome) }}</td>
                            <td class="bg-success">{{ number_format($income - $outcome) }}</td>
                            <td class="bg-secondary">{{ number_format($partner_net) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="bg-success" colspan="2" style="text-align:center;">Total</td>
                        <td class="bg-success"> {{ number_format($total_income) }} </td>
                        <td class="bg-success"> {{ number_format($total_outcome) }} </td>
                        <td class="bg-success"> {{ number_format($total_net) }} </td>
                        <td class="bg-success"> {{ number_format($partner_total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>