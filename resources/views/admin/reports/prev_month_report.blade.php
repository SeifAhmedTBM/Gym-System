@extends('layouts.admin')
@section('content')
<style>
    /* *{
        font-size: 10px!important;
        font-weight: bold;
    } */
</style>
    <div class="card">
        <div class="card-header">
            Monthly Overall Report
        </div>
        <?php
            use Carbon\Carbon;
        ?>
        <div class="card-body">
            <h4 style="padding:10px 0px ">Current Month Report From : {{ Carbon::now()->startOfMonth()->format('Y-m-d') }} To : {{ Carbon::now()->format('Y-m-d') }} </h4>
            <table class="table table-striped table-hover table-bordered zero-configuration">
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
            <h4 style="padding:10px 0px ">Previous Month Report From : {{ $startOfLastMonth }} To : {{ $endOfLastMonth }} </h4>
           <table class="table table-striped table-hover table-bordered zero-configuration">
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

    <div class="card">
        <div class="card-header">
            Sales Monthly Performance
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ URL::current() }}" method="get">
                            <div class="form-group">
                                <label for="date">{{ trans('global.branches') }}</label>
                                <div class="input-group">
                                    <!-- <input type="date" class="form-control" name="from"
                                        value="{{ $startOfLastMonth }}" disabled>
                                    <input type="date" class="form-control" name="to"
                                        value="{{ $endOfLastMonth }}" disabled> -->
                                    <select name="branch_id" id="branch_id" class="form-control"
                                        {{ $employee && $employee->branch_id != null ? 'readonly' : '' }}>
                                        <option value="{{ null }}" selected>All Branches</option>
                                        @foreach (\App\Models\Branch::pluck('name', 'id') as $id => $name)
                                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-prepend">
                                        <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Current cards --}}
            <h4 style="padding:10px 0px ">Current Month Sales Report From : {{ Carbon::now()->startOfMonth()->format('Y-m-d') }} To : {{ Carbon::now()->format('Y-m-d') }} </h4>
            <div class="form-group row">
                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2">
                        <div class="card-body text-center text-white bg-primary">
                            <div>
                                <h3>{{ number_format($current_month_invoices) }}</h3>
                                <div>Invoices</div>
                                <strong>( total net amount )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-success">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($current_month_payments_sum_amount) }}</h3>
                                <div>Payments</div>
                                <strong>( total payments for invoices this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-warning">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($current_month_pending) }}</h3>
                                <div>Pending</div>
                                <strong>( Pending amounts this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-success">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($current_month_payments) }}</h3>
                                <div>All Payments</div>
                                <strong>( total payments collected this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-danger">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($current_month_refunds) }}</h3>
                                <div>Refunds</div>
                                <strong>( total refunds this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end current card --}}

            <h4 style="padding:10px 0px ">Previous Month Sales Report From : {{ $startOfLastMonth }} To : {{ $endOfLastMonth }} </h4>
            {{-- Previous cards --}}
            <div class="form-group row">
                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2">
                        <div class="card-body text-center text-white bg-primary">
                            <div>
                                <h3>{{ number_format($invoices) }}</h3>
                                <div>Invoices</div>
                                <strong>( total net amount )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-success">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($payments_sum_amount) }}</h3>
                                <div>Payments</div>
                                <strong>( total payments for invoices this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-warning">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($pending) }}</h3>
                                <div>Pending</div>
                                <strong>( Pending amounts this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-success">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($payments) }}</h3>
                                <div>All Payments</div>
                                <strong>( total payments collected this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4">
                    <div class="card mb-2 text-center text-white bg-danger">
                        <div class="card-body">
                            <div>
                                <h3>{{ number_format($refunds) }}</h3>
                                <div>Refunds</div>
                                <strong>( total refunds this month )</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Previous card --}}
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Trainer Monthly Report
        </div>
        <div class="card-body">
          <div class="form-group">
            <div class="card">
                <div class="card-body">
                    <form action="{{ URL::current() }}" method="get">
                        <div class="form-group">
                            <label for="date">{{ trans('global.branches') }}</label>
                            <div class="input-group">
                                <!-- <input type="date" class="form-control" name="from"
                                    value="{{ $startOfLastMonth }}" disabled>
                                <input type="date" class="form-control" name="to"
                                    value="{{ $endOfLastMonth }}" disabled> -->
                                <select name="branch_id" id="branch_id" class="form-control"
                                    {{ $employee && $employee->branch_id != null ? 'readonly' : '' }}>
                                    <option value="{{ null }}" selected>All Branches</option>
                                    @foreach (\App\Models\Branch::pluck('name', 'id') as $id => $name)
                                        <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-prepend">
                                    <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
          </div>
          {{-- filter --}}

          
          {{-- Current Month cards --}}
          <h4 style="padding:10px 0px ">Current Month Trainer Report From : {{ Carbon::now()->startOfMonth()->format('Y-m-d') }} To : {{ Carbon::now()->format('Y-m-d') }} </h4>
          <div class="form-group row">
            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2">
                    <div class="card-body text-center text-white bg-primary">
                        <div>
                            <h3>{{ number_format($current_month_trainer_invoices) }}</h3>
                            <div>Invoices</div>
                            <strong>( total net amount )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-success">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($current_month_trainer_payments_sum_amount) }}</h3>
                            <div>Payments</div>
                            <strong>( total payments for invoices this month )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-warning">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($current_month_trainer_pending) }}</h3>
                            <div>Pending</div>
                            <strong>( Pending amounts this month )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-success">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($current_month_trainer_payments) }}</h3>
                            <div>All Payments</div>
                            <strong>( total payments collected this month )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-danger">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($current_month_trainer_refunds) }}</h3>
                            <div>Refunds</div>
                            <strong>( total refunds this month )</strong>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          {{-- End Of Current Month card --}}

          {{-- Previous cards --}}
          <h4 style="padding:10px 0px ">Previous Month Trainer Report From : {{ $startOfLastMonth }} To : {{ $endOfLastMonth }} </h4>
          <div class="form-group row">
            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2">
                    <div class="card-body text-center text-white bg-primary">
                        <div>
                            <h3>{{ number_format($trainer_invoices) }}</h3>
                            <div>Invoices</div>
                            <strong>( total net amount )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-success">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($trainer_payments_sum_amount) }}</h3>
                            <div>Payments</div>
                            <strong>( total payments for invoices this month )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-warning">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($trainer_pending) }}</h3>
                            <div>Pending</div>
                            <strong>( Pending amounts this month )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-success">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($trainer_payments) }}</h3>
                            <div>All Payments</div>
                            <strong>( total payments collected this month )</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-2 text-center text-white bg-danger">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($trainer_refunds) }}</h3>
                            <div>Refunds</div>
                            <strong>( total refunds this month )</strong>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          {{-- End Of Previous card --}}
        </div>
    </div>

  
    

@endsection
