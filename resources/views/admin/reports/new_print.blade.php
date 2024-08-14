<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css" media="screen">
        
        html {
            line-height: 1.15;
            direction: rtl;
            margin: 0;
        }
        body {
            font-family: DejaVu Sans, sans-serif !important;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: inherit;
        }

        h4,
        .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4,
        .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
        }

        .table.table-items td {
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        th,
        tr,
        td,
        p,
        div {
            line-height: 1.1;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }

        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }

        .border-0 {
            border: none !important;
        }

        .cool-gray {
            color: #6B7280;
        }
        
        .page
        {
        -webkit-transform: rotate(-90deg); 
        -moz-transform:rotate(-90deg);
        filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
        }

        
    </style>
</head>

<body style="{{ app()->isLocale('ar') ? 'text-align:right !important' : 'text-align:left !important' }}">
    @if (\App\Models\Setting::first()->menu_logo)
        <img src="{{ 'images/' . \App\Models\Setting::first()->menu_logo }}" style="display: block" alt="logo" height="35">
    @endif

    <h2 class="text-uppercase" style="text-align: center!important">
        {{ trans('global.daily_report') .' '.request()->date ?? date('Y-m-d')}}
    </h2>

    <h5 class="text-uppercase text-left">
        {{ trans('global.total_income') . ' & ' . trans('global.total_outcome') }}
    </h5>
    <table class=" table table-bordered table-striped table-hover datatable datatable-statement" style="text-align:right !important; padding:5px;" border="1">
        <thead>
            <tr class="text-center">
                <th>{{ trans('global.total_income') }}</th>
                <th>{{ trans('global.total_outcome') }}</th>
                <th>{{ trans('global.net_income') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $total_income }}
                </td>
                <td>
                    {{ $total_outcome }}
                </td>
                <td>
                    {{ $net_income }}
                </td>
            </tr>
        </tbody>
    </table>
   

    <h5 class="text-uppercase text-left">
        {{ trans('cruds.account.title') }}
    </h5>

    <table class=" table table-bordered table-striped table-hover datatable datatable-statement" border="1" style="text-align: center; padding:5px">
        <thead>
            <tr>
                <th>
                    {{ trans('cruds.transactions.fields.id') }}
                </th>
                <th>
                    {{ trans('cruds.transactions.fields.account') }}
                </th>
                @foreach ($service_payments as $key => $payment)
                <th style="background-color:#e7f1e6">
                    {{ $key }} Payments
                </th>
                @endforeach
                <th style="background-color:#e7f1e6">
                    {{ trans('cruds.externalPayment.title') }}
                </th>
                @foreach ($service_refunds as $key => $refund)
                   <th style="background-color:#f1e6e6"> {{$key}} Refunds</th>
                @endforeach
                <th style="background-color:#f1e6e6">
                    {{ trans('cruds.expense.title') }}
                </th>
                <th style="background-color:#f1e6e6">
                    {{ trans('cruds.loan.title') }}
                </th>
                <th>
                    {{ trans('cruds.account.fields.balance') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($accounts as $acc)
                    @php
                        $total_payments = 0;
                        $total_revenues = 0;
                        $total_refunds = 0;
                        $total_expenses = 0;
                        $total_loans = 0;
                        $total_balance = 0;
                    @endphp
                <tr>
                    @php
                        $tran_payments = $acc->transactions->where('transactionable_type', 'App\Models\Payment')->sum('amount');
                        $total_payments += $tran_payments;
                        
                        $tran_externalPayments = $acc->transactions->where('transactionable_type', 'App\Models\ExternalPayment')->sum('amount');
                        $total_revenues += $tran_externalPayments;

                        $tran_refunds = $acc->transactions->where('transactionable_type', 'App\Models\Refund')->sum('amount');
                        $total_refunds += $tran_refunds;
                    
                        $tran_loans = $acc->transactions->where('transactionable_type', 'App\Models\Loan')->sum('amount');
                        $total_loans += $tran_loans;

                        $tran_expenses = $acc->transactions->where('transactionable_type', 'App\Models\Expense')->sum('amount');
                        $total_expenses += $tran_expenses;

                        $total = ($tran_payments + $tran_externalPayments) - ($tran_refunds + $tran_expenses + $tran_loans);
                        $total_balance += $total;

                    @endphp
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $acc->name ?? '' }}</td>
                    @foreach ($service_payments as $key => $payment)
                        <td>{{ number_format($payment->where('account_id',$acc->id)->sum('amount')) }}</td>
                    @endforeach
                    <td>{{ $tran_externalPayments }}
                    </td>
                    @foreach ($service_refunds as $key => $refund)
                        <td>{{ number_format($refund->where('account_id',$acc->id)->sum('amount')) }}</td>
                    @endforeach
                    <td>{{ $tran_expenses }}
                    </td>

                    <td>{{ $tran_loans }}
                    </td>
                    
                    <td class="table-success">
                        {{ $total ?? 0 }}
                    </td>
                </tr>
            @empty
                <td colspan="7" class="text-center">{{ trans('global.no_data_available') }}</td>
            @endforelse
        </tbody>
    </table>

    <h5 class="text-uppercase text-left">
        {{ trans('cruds.payment.title') }}
    </h5>
    <table class="table table-striped table-bordered table-hover " border="1" style="text-align: center; padding:5px">
        <thead>
            <tr>
                <th > {{ trans('cruds.payment.fields.invoice') }}</th>
                <th>{{ trans('global.payments.amount') }}</th>
                <th>{{ trans('cruds.payment.fields.sales_by') }}</th>
                <th>{{ trans('cruds.account.title') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    @if(config('domains')[config('app.url')]['short_reports'] == true)
                    <td>
                        {{ $payment->invoice->invoicePrefix() . ' ' . $payment->invoice_id }} | {{$payment->invoice->membership->member->member_code}}
                    </td>
                    @else
                    <td>
                        {{ $payment->invoice->invoicePrefix() . ' ' . $payment->invoice_id }}
                        <br>
                        <b class="d-block">
                            ({{ $payment->invoice->membership->member->name }} )
                        </b>
                        <br>
                        <b class="d-block">{{ $payment->invoice->membership->member->phone ?? '' }}</b>
                        <br>
                        <span class="badge badge-success">
                            {{ $payment->invoice->membership->service_pricelist->name }}
                        </span>
                    </td>
                    @endif
                   
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->sales_by->name ?? '-' }}</td>
                    <td>{{ $payment->account->name ?? '' }}</td>
                </tr>
            @empty
                <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
            @endforelse
        </tbody>
    </table>

    @if ($expenses->count() > 0)
        <h5 class="text-uppercase text-left">
            {{ trans('cruds.expense.title') }}
        </h5>

        <table class="table table-striped table-bordered table-hover " border="1" style="text-align: center; padding:5px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('cruds.expense.fields.name') }}</th>
                    <th>{{ trans('cruds.expense.fields.expenses_category') }}</th>
                    <th>{{ trans('cruds.expense.fields.amount') }}</th>
                    <th>{{ trans('cruds.account.title') }}</th>
                    <th>{{ trans('cruds.expense.fields.created_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $expense->name }}</td>
                        <td>{{ $expense->expenses_category->name ?? '-' }}</td>
                        <td>{{ $expense->amount }}</td>
                        <td>{{ $expense->account->name ?? '-' }}</td>
                        <td>{{ $expense->created_by->name ?? '-' }}</td>
                    </tr>
                @empty
                    <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                @endforelse
            </tbody>
        </table>
    @endif

    @if ($external_payments->count() > 0)
        <h5 class="text-uppercase text-left">
            {{ trans('cruds.externalPayment.title') }}
        </h5>

        <table class="table table-striped table-bordered table-hover " border="1" style="text-align: center; padding:5px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('cruds.externalPayment.title') }}</th>
                    <th>{{ trans('cruds.externalPayment.fields.amount') }}</th>
                    <th>{{ trans('cruds.account.title') }}</th>
                    <th>{{ trans('cruds.externalPayment.fields.created_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($external_payments as $externalPayment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $externalPayment->title }}</td>
                        <td>{{ $externalPayment->amount }}</td>
                        <td>{{ $externalPayment->account->name ?? '-' }}</td>
                        <td>{{ $externalPayment->created_by->name ?? '-' }}</td>
                    </tr>
                @empty
                    <td colspan="5" class="text-center">{{ trans('global.no_data_available') }}</td>
                @endforelse
            </tbody>
        </table>
    @endif

    @if ($refunds->count() > 0)
        <h5 class="text-uppercase text-left">
            {{ trans('cruds.refund.title') }}
        </h5>

        <table class="table table-striped table-bordered table-hover " style="text-align:center" border="1" style="text-align: center; padding:5px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('cruds.refund.fields.invoice') }}</th>
                    <th>{{ trans('cruds.externalPayment.fields.amount') }}</th>
                    <th>{{ trans('cruds.refund.fields.refund_reason') }}</th>
                    <th>{{ trans('cruds.account.title') }}</th>
                    <th>{{ trans('cruds.externalPayment.fields.created_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($refunds as $refund)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('admin.invoices.show', $refund->invoice_id) }}"
                                target="_blank">{{ $refund->invoice->invoicePrefix() . ' ' . $refund->invoice_id }}</a>
                        </td>
                        <td>{{ $refund->amount }}</td>
                        <td>{{ $refund->refund_reason->name ?? '-' }}</td>
                        <td>{{ $refund->account->name ?? '-' }}</td>
                        <td>{{ $refund->created_by->name ?? '-' }}</td>
                    </tr>
                @empty
                    <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                @endforelse
            </tbody>
        </table>
    @endif
</body>

</html>
