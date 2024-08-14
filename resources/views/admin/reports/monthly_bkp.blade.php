@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-2 offset-md-8">
            <form action="{{ route('admin.reports.print.monthlyReport') }}" method="get">
                <input type="hidden" name="date" value="{{ request()->get('date') ?? date('Y-m') }}">
                <button type="submit" class="btn btn-sm btn-info btn-block"><i class="fa fa-print"></i> {{ trans('global.print') }}</button>
            </form>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.reports.monthlyReport.export',request()->all()) }}" class="btn btn-primary btn-sm btn-block">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-3">
                    <form action="{{ route('admin.reports.monthly.report') }}" method="get">
                        <label for="date">{{ trans('global.date') }}</label>
                        <div class="input-group">
                            <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                            <div class="input-group-prepend">
                                <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="asd" id="DivIdToPrint">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-center">{{ trans('global.monthly_report') }} {{ request('date') ?? date('F Y') }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center text-white bg-primary">
                        <div>
                            <h3>{{ number_format($total_income) }} EGP<span class="fs-6 fw-normal"></h3>
                            <div>{{ trans('global.total_income') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-4 ">
                    <div class="card-body text-center text-white bg-danger">
                        <div>
                            <h3>{{ number_format($total_outcome) }} EGP<span class="fs-6 fw-normal"></h3>
                            <div>{{ trans('global.total_outcome') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-4 col-lg-4">
                <div class="card mb-4 text-center text-white bg-success">
                    <div class="card-body">
                        <div>
                            <h3>{{ number_format($net_income) }} EGP<span class="fs-6 fw-normal"></h3>
                            <div>{{ trans('global.net_income') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>

        <div class="row">
            <div class="col-sm-6 col-lg-6">
                <div class="card mb-4">
                    <div class="card-body text-center text-white bg-info">
                        <div>
                            {{-- <h3>{{ $renewals }} <span class="fs-6 fw-normal"></h3> --}}
                            <h3>{{ $renewals_payments_count }} ({{ number_format($renewals_payments).' EGP' }}) <span class="fs-6 fw-normal"></h3>
                            <div>{{ trans('cruds.membership.fields.renew') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-6">
                <div class="card mb-4 ">
                    <div class="card-body text-center text-white bg-info">
                        <div>
                            {{-- <h3>{{ $new_members }} <span class="fs-6 fw-normal"></h3> --}}
                            <h3>{{ $new_payments_count }} ({{ number_format($new_payments).' EGP' }})  <span class="fs-6 fw-normal"></h3>
                            <div>New Members</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('cruds.account.title') }} {{ trans('global.list') }}</h5>
                    </div>

                    <div class="card-body">
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
                                        <th>
                                            {{ trans('global.payments.title') }}
                                        </th>
                                        <th>
                                            {{ trans('cruds.externalPayment.title') }}
                                        </th>
                                        <th>
                                            {{ trans('cruds.refund.title') }}
                                        </th>
                                        <th>
                                            {{ trans('cruds.expense.title') }}
                                        </th>
                                        <th>
                                            {{ trans('cruds.account.fields.balance') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($accounts as $account)
                                        <tr>
                                            @php
                                                $tran_payments = $account->transactions->where('transactionable_type', 'App\Models\Payment')->sum('amount');

                                                $tran_externalPayments = $account->transactions->where('transactionable_type', 'App\Models\ExternalPayment')->sum('amount');

                                                $tran_refunds = $account->transactions->where('transactionable_type', 'App\Models\Refund')->sum('amount');

                                                $tran_expenses = $account->transactions->where('transactionable_type', 'App\Models\Expense')->sum('amount');

                                                $total = ($tran_payments + $tran_externalPayments) - ($tran_refunds + $tran_expenses); 
                                            @endphp
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->name ?? '' }}</td>
                                            <td>{{ $tran_payments }}
                                            </td>
                                            <td>{{ $tran_externalPayments }}
                                            </td>
                                            <td>{{ $tran_refunds }}
                                            </td>
                                            <td>{{ $tran_expenses }}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('cruds.payment.title') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('cruds.payment.title_singular') }}
                                        {{ trans('cruds.payment.fields.id') }}</th>
                                    <th>{{ trans('cruds.payment.fields.invoice') }}</th>
                                    <th>{{ trans('global.payments.amount') }}</th>
                                    <th>{{ trans('cruds.payment.fields.sales_by') }}</th>
                                    <th>{{ trans('cruds.account.title') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.payments.show',$payment->id) }}">
                                                {{ $payment->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}"
                                                target="_blank">
                                                {{ $payment->invoice->invoicePrefix() . ' ' . $payment->invoice_id }}
                                            </a>

                                            <a href="{{ route('admin.members.show',$payment->invoice->membership->member_id) }}">
                                                <b class="d-block">
                                                    {{ $payment->invoice->membership->memberPrefix() . $payment->invoice->membership->member->member_code ?? '' }}
                                                    ({{ $payment->invoice->membership->member->name }} )
                                                </b>
        
                                                <b class="d-block">{{ $payment->invoice->membership->member->phone ?? '' }}</b>
                                            </a>

                                            <div class="p-2 badge badge-success">
                                                {{ $payment->invoice->membership->service_pricelist->name }}

                                                {{ $payment->invoice->membership->service_pricelist->session_count != 0 ? $payment->invoice->membership->service_pricelist->session_count . ' Session/s ' : '' }}
                                            </div>

                                            <span class="p-2 badge badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$payment->invoice->membership->membership_status] }}">
                                                {{ \App\Models\Membership::MEMBERSHIP_STATUS[$payment->invoice->membership->membership_status] }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->sales_by->name ?? '-' }}</td>
                                        <td>{{ $payment->account->name ?? '' }}</td>
                                    </tr>
                                @empty
                                    <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('cruds.expense.title') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover zero-configuration">
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
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('cruds.externalPayment.title') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover zero-configuration">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('cruds.externalPayment.title') }}</th>
                                    <th>{{ trans('cruds.externalPayment.fields.amount') }}</th>
                                    <th>{{ trans('cruds.account.title') }}</th>
                                    <th>{{ trans('cruds.externalPayment.fields.created_by') }}</th>
                                    <th>{{ trans('cruds.externalPayment.fields.created_at') }}</th>
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
                                        <td>{{ $externalPayment->created_at ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <td colspan="5" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('cruds.refund.title') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover zero-configuration">
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
                    </div>
                </div>
            </div>
        </div> --}}
      
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
    </script>
@endsection