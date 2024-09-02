@extends('layouts.admin')
@section('content')
<style>
    /* *{
        font-size: 10px!important;
        font-weight: bold;
    } */
</style>
    <div class="card">
        <div class="card-body">
            <div class=" float-right">
                <form action="{{ route('admin.reports.print.dailyReport') }}" method="get">
                    <input type="hidden" name="date" value="{{ request()->get('date') ?? date('Y-m-d') }}">
                    <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-print"></i> {{ trans('global.print') }}</button>
                </form>
            </div>

            <form action="{{ route('admin.reports.daily.report') }}" method="get">
                <div class="row">
                    <div class="col-md-6">
                        <label for="date">{{ trans('global.date') }}</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="date" value="{{ request('date') ?? date('Y-m-d') }}">
                            <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                                <option value="{{ NULL }}" selected >All Branches</option>
                                @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                                    <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend">
                                <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div id="DivIdToPrint">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-center">{{ trans('global.daily_report') }} {{{  $_GET['date'] ?? date('Y-m-d') }}}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            {{-- <div class="card-header">
                <h3><i class="fa fa-dollar"></i> {{ trans('global.daily_analysis') }}</h3>
            </div> --}}
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2">
                            <div class="card-body text-center text-white bg-primary">
                                <div>
                                    <h3 id="total_income_card"></h3>
                                    <div >{{ trans('global.total_income') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

        
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2 text-center text-white bg-danger">
                            <div class="card-body">
                                <div>
                                    <h3 id="total_outcome_card"></h3>
                                    <div>{{ trans('global.total_outcome') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2 text-center text-white bg-success">
                            <div class="card-body">
                                <div>
                                    <h3 id="net_income_card"></h3>
                                    <div>{{ trans('global.net_income') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-users"></i> {{ trans('global.members_analysis') }}</h3>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body text-center bg-light">
                                <div>
                                    <h3>{{ trans('cruds.membership.fields.renew') }} <span class="badge badge-success badge-pill">{{ $renewals_payments_count }}</span></h3>
                                    <h3>{{ number_format($renewals_payments).' EGP' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-sm-6 col-lg-6">
                        <div class="card mb-4 ">
                            <div class="card-body text-center bg-light">
                                <div>
                                    <h3>New Members <span class="badge badge-success badge-pill">{{ $new_payments_count }}</span></h3> 
                                    <h3>{{ number_format($new_payments).' EGP' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    {{-- <div class="card-header">
                        <h5>{{ trans('cruds.account.title') }} {{ trans('global.list') }}</h5>
                    </div> --}}

                    <div class="card-body">
                        <div class="row form-group">
                            @foreach ($accounts as $account)
                                @php
                                    $tran_payments = $account->transactions->where('transactionable_type', 'App\Models\Payment')->sum('amount');
            
                                    $tran_externalPayments = $account->transactions->where('transactionable_type', 'App\Models\ExternalPayment')->sum('amount');
            
                                    $tran_refunds = $account->transactions->where('transactionable_type', 'App\Models\Refund')->sum('amount');

                                    $tran_loans = $account->transactions->where('transactionable_type', 'App\Models\Loan')->sum('amount');
            
                                    $tran_expenses = $account->transactions->where('transactionable_type', 'App\Models\Expense')->sum('amount');
            
                                    $total = ($tran_payments + $tran_externalPayments) - ($tran_refunds + $tran_expenses + $tran_loans); 
                                @endphp
            
                                <div class="col-md-2">
                                    <div class="card mb-2">
                                        <div class="card-body text-center  bg-primary">
                                            <div>
                                                <h3>{{ number_format($total) }} EGP<span class="fs-6 fw-normal"></h3>
                                                <div style="font-size: 14px !important;">{{ $account->name }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                         <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class=" table table-bordered table-striped table-hover datatable datatable-statement">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{ trans('cruds.transactions.fields.id') }}
                                                </th>
                                                <th>
                                                    {{ trans('cruds.transactions.fields.account') }}
                                                </th>
                                                @foreach ($service_payments as $key => $payment)
                                                <th class="bg-primary">
                                                    {{ $key }} Payments
                                                </th>
                                                @endforeach
                                                <th class="bg-primary">
                                                    {{ trans('cruds.externalPayment.title') }}
                                                </th>
                                                <th class="bg-primary">
                                                    Total Income
                                                </th>
                                                @foreach ($service_refunds as $key => $refund)
                                                   <th class="bg-danger"> {{$key}} Refunds</th>
                                                @endforeach
                                                <th class="bg-danger">
                                                    Commission Fees
                                                </th>
                                                <th class="bg-danger">
                                                    {{ trans('cruds.expense.title') }}
                                                </th>
                                                <th class="bg-danger">
                                                    {{ trans('cruds.loan.title') }}
                                                </th>
                                                <th class="bg-danger">
                                                    Total Outcome
                                                </th>
                                                <th class="bg-success">
                                                    {{ trans('cruds.account.fields.balance') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total_payments = 0;
                                                $total_revenues = 0;
                                                $total_refunds = 0;
                                                $total_expenses = 0;
                                                $total_loans = 0;
                                                $total_balance = 0;
                                                $total_commission_fees = 0;
                                            @endphp
                                            @forelse ($accounts as $acc)
                                                <tr>
                                                    @php
                                                        $tran_payments = $acc->transactions->where('transactionable_type', 'App\Models\Payment')->sum('amount');
                                                        
                                                        $tran_externalPayments = $acc->transactions->where('transactionable_type', 'App\Models\ExternalPayment')->sum('amount');
                                                        
                                                        $income = ($tran_payments + $tran_externalPayments);

                                                        $tran_refunds = $acc->transactions->where('transactionable_type', 'App\Models\Refund')->sum('amount');

                                                        $tran_loans = $acc->transactions->where('transactionable_type', 'App\Models\Loan')->sum('amount');

                                                        $tran_expenses = $acc->transactions->where('transactionable_type', 'App\Models\Expense')->sum('amount');

                                                        $commission_fees = ($income * $acc->commission_percentage) / 100;

                                                        $outcome = ($tran_refunds + $tran_loans + $tran_expenses + $commission_fees);

                                                        $total_commission_fees += $commission_fees;
                                                        $total_expenses += $tran_expenses;
                                                        $total_payments += $tran_payments;
                                                        $total_revenues += $tran_externalPayments;
                                                        $total_refunds += $tran_refunds;
                                                        $total_loans += $tran_loans;
                                                        $total_income = ($total_payments + $total_revenues);
                                                        $total_outcome = ($total_refunds + $total_loans + $total_expenses + $total_commission_fees);
        
                                                        $total = $income - $outcome;
                                                        $total_balance += $total;

                                                    @endphp
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $acc->name ?? '' }} ({{ $acc->commission_percentage .'%'}})</td>
                                                    @foreach ($service_payments as $key => $payment)
                                                        <td>{{ number_format($payment->where('account_id',$acc->id)->sum('amount')) }}</td>
                                                    @endforeach
                                                    <td>
                                                        {{ $tran_externalPayments }}
                                                    </td>
                                                    <td class="bg-primary">
                                                        {{ number_format($tran_payments + $tran_externalPayments) }}
                                                    </td>
                                                    @foreach ($service_refunds as $key => $refund)
                                                        <td>{{ number_format($refund->where('account_id',$acc->id)->sum('amount')) }}</td>
                                                    @endforeach
                                                    <td>
                                                        {{ number_format($commission_fees,1) }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($tran_expenses) }}
                                                    </td>

                                                    <td>
                                                        {{ number_format($tran_loans) }}
                                                    </td>
                                                    <td class="bg-danger">
                                                        {{ number_format($tran_refunds + $tran_loans + $tran_expenses + $commission_fees) }}
                                                    </td>
                                                    <td class="bg-success">
                                                        {{ number_format($total) ?? 0 }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <td colspan="7" class="text-center">{{ trans('global.no_data_available') }}</td>
                                            @endforelse
                                            <tr>
                                                <td colspan="2"></td>
                                                @foreach ($service_payments as $key => $payment)
                                                    <td>{{ number_format($payment->sum('amount')) }} EGP</td>
                                                @endforeach
                                                <td>{{ number_format($total_revenues) }} EGP</td>
                                                <td class="bg-primary" id="total_income">{{ number_format($total_income) }} EGP</td>
                                                @foreach ($service_refunds as $key => $refund)
                                                    <td>{{ number_format($refund->sum('amount')) }} EGP</td>
                                                @endforeach
                                                <td>{{ number_format($total_commission_fees,1) }} EGP</td>
                                                <td>{{ number_format($total_expenses) }} EGP</td>
                                                <td>{{ number_format($total_loans) }} EGP</td>
                                                <td class="bg-danger" id="total_outcome">{{ number_format($total_outcome) }} EGP</td>
                                                <td class="bg-success" id="net_income">{{ number_format($total_balance) }} EGP</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div> 
        
        <div class="row">
            @if ($expenses->count() > 0)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ trans('cruds.expense.title') }} : <span class="text-white">{{ number_format($expenses->sum('amount')) }} EGP ({{ $expenses->count() }})</span> </strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>{{ trans('cruds.expense.fields.expenses_category') }}</th>
                                        <th>{{ trans('cruds.expense.fields.name') }}</th>
                                        <th>{{ trans('cruds.expense.fields.amount') }}</th>
                                        <th>{{ trans('cruds.expense.fields.created_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expenses_category->name ?? '-' }}</td>
                                            <td>{{ $expense->name }}</td>
                                            <td>{{ $expense->amount }} - {{ $expense->account->name ?? '-' }}</td>
                                            <td>{{ $expense->created_by->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif   

            @if ($loans->count() > 0)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ trans('cruds.loan.title') }} : <span class="text-white">{{ number_format($loans->sum('amount')) }} EGP ({{ $loans->count() }})</span> </strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>{{ trans('cruds.loan.fields.name') }}</th>
                                        <th>{{ trans('cruds.loan.fields.employee') }}</th>
                                        <th>{{ trans('cruds.loan.fields.amount') }}</th>
                                        <th>{{ trans('cruds.loan.fields.created_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loans as $loan)
                                        <tr>
                                            <td>{{ $loan->name ?? '-' }}</td>
                                            <td>{{ $loan->employee->name ?? '-' }}</td>
                                            <td>{{ $loan->amount }}</td>
                                            <td>{{ $loan->created_by->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if ($external_payments->count() > 0)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ trans('cruds.externalPayment.title') }} : <span class="text-white">{{ number_format($external_payments->sum('amount')) }} EGP ({{ $external_payments->count() }})</span> </strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>{{ trans('cruds.externalPayment.fields.id') }}</th>
                                        <th>{{ trans('cruds.externalPayment.fields.customer_name') }}</th>
                                        <th>{{ trans('cruds.externalPayment.title') }}</th>
                                        <th>{{ trans('cruds.externalPayment.fields.amount') }}</th>
                                        <th>Notes</th>
                                        <th>{{ trans('cruds.externalPayment.fields.created_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($external_payments as $index=>$externalPayment)
                                        <tr>
                                            <td>{{ $index }}</td>
                                            <td>{{ $externalPayment->lead ? $externalPayment->lead->name : '-' }}</td>
                                            <td>{{ $externalPayment->title }}</td>
                                            <td>
                                                {{ $externalPayment->amount }} -
                                                {{ $externalPayment->account->name ?? '-' }}
                                            </td>
                                            <td>{{ $externalPayment->notes ?? '-' }}</td>
                                            <td>{{ $externalPayment->created_by->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($service_payments as $key => $payment)
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ $key }} - {{ trans('global.income') }} : <span class="text-white">{{ number_format($payment->sum('amount')) }} EGP ({{ $payment->count().' Payment' }})</span> </strong> 
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('cruds.payment.fields.invoice') }}</th>
                                        <th>Service</th>
                                        <th>Service Type</th>
                                        <th>{{ trans('global.payments.amount') }}</th>
                                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment as $pay)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $pay->invoice_id) }}">
                                                    {{ $pay->invoice->membership->member->branch->invoice_prefix.$pay->invoice_id }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.members.show',$pay->invoice->membership->member_id) }}">
                                                    <b class="d-block">
                                                        {{ $pay->invoice->membership->member->branch->member_prefix.$pay->invoice->membership->member->member_code ?? '' }}
                                                        ({{ $pay->invoice->membership->member->name }} )
                                                    </b>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="p-2 badge badge-success">
                                                    {{ $pay->invoice->membership->service_pricelist->name }}
                                                    {{ $pay->invoice->membership->service_pricelist->session_count != 0 ? $pay->invoice->membership->service_pricelist->session_count . ' Session/s ' : '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="p-2 badge badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$pay->invoice->membership->membership_status] }}">
                                                    {{ \App\Models\Membership::MEMBERSHIP_STATUS[$pay->invoice->membership->membership_status] }}
                                                </span>
                                            </td>
                                            <td>{{ $pay->amount }} - {{ $pay->account->name ?? '' }}</td>
                                            {{-- <td>{{ $pay->sales_by->name ?? '-' }}</td> --}}
                                            <td>{{ $pay->created_by->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach ($service_refunds as $key => $refund)
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ $key }} - {{ trans('global.outcome') }} : <span class="text-danger">{{ number_format($refund->sum('amount')) }} EGP ({{ $refund->count().' Refund' }})</span> </strong>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('cruds.refund.fields.invoice') }}</th>
                                        <th>Service</th>
                                        <th>{{ trans('cruds.refund.fields.refund_reason') }}</th>
                                        <th>{{ trans('cruds.externalPayment.fields.amount') }}</th>
                                        <th>{{ trans('cruds.externalPayment.fields.created_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($refund as $ref)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $ref->invoice_id) }}"
                                                    target="_blank">{{ $ref->invoice->branch->invoice_prefix.$ref->invoice_id }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.members.show',$ref->invoice->membership->member_id) }}">
                                                    <b class="d-block">
                                                        {{ $ref->invoice->branch->member_prefix.$ref->invoice->membership->member->member_code ?? '' }}
                                                        ({{ $ref->invoice->membership->member->name }} )
                                                    </b>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="p-2 badge badge-success">
                                                    {{ $ref->invoice->membership->service_pricelist->name }}
                                                    {{ $ref->invoice->membership->service_pricelist->session_count != 0 ? $ref->invoice->membership->service_pricelist->session_count . ' Session/s ' : '' }}
                                                </div>
                                            </td>
                                            <td>{{ $ref->refund_reason->name ?? '-' }}</td>
                                            <td>{{ $ref->amount }} - {{ $ref->account->name ?? '-' }}</td>
                                            <td>{{ $ref->created_by->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function printFunction()
        {
            var divToPrint=document.getElementById('DivIdToPrint');

            var newWin=window.open('','Print-Window');

            newWin.document.open();

            newWin.document.write('<html><link href="{{ asset("css/coreui.min.css")}}" rel="stylesheet" type="text/css" /><style> th{font-size:21px;} td{font-size:21px;} h6{font-size:21px;} h5{font-size:21px;} </style><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

            newWin.document.close();

            setTimeout(function(){newWin.close();},100);
        }

        $(document).ready(function(){
            $('#total_income_card').text($('#total_income').text());
            $('#total_outcome_card').text($('#total_outcome').text());
            $('#net_income_card').text($('#net_income').text());
        })
    </script>
@endsection