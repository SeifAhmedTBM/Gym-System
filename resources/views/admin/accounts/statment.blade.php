@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-sm-6 col-lg-6">
        <div class="card ">
            <div class="card-body">
                <h4>{{ trans('cruds.account.fields.opening_balance') }}</h4>
                <h4 class="fs-4 fw-semibold">{{ $account->opening_balance }} EGP</h4>
            </div>
        </div>
    </div>
    <!-- /.col-->

    <div class="col-sm-6 col-lg-6">
        <div class="card mb-4 ">
            <div class="card-body ">
                <div>
                    <h4>{{ trans('cruds.account.fields.balance') }}</h4>
                    <h4 class="fs-4 fw-semibold">{{ $account->balance }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col-->
</div>
<div class="card">
    <div class="card-header">
        <h5>
            {{ trans('cruds.account.fields.statement') }} {{ trans('global.list') }} | <span class="badge badge-info">{{ $account->name }}</span>
        </h5> 
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
                            {{ trans('cruds.transactions.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.notes') }}
                        </th>
                        <th>
                            {{ trans('cruds.transactions.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.transactions.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.transactions.fields.created_at') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $key => $transaction)
                        <tr  class="{{ \App\Models\Transaction::color[$transaction->transactionable_type] }}">
                            <td>
                                {{-- {{ $transaction->transactionable_id ?? '' }} --}}
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ \App\Models\Transaction::type[$transaction->transactionable_type] }}
                            </td>
                            <td>
                                @switch(\App\Models\Transaction::type[$transaction->transactionable_type])
                                    @case('Expenses')
                                        <a href="{{ route('admin.expenses.show',$transaction->transactionable_id) }}">
                                            {{ $transaction->transactionable_type::find($transaction->transactionable_id)->name }}
                                        </a>
                                        @break
                                    @case('Refunds')
                                        <a href="{{ route('admin.refunds.show',$transaction->transactionable_id) }}">
                                            {{ App\Models\Setting::first()->invoice_prefix.' '.$transaction->transactionable_type::find($transaction->transactionable_id)->invoice_id }}</a>
                                        @break
                                    @case('External Payments')
                                        <a href="{{ route('admin.external-payments.show',$transaction->transactionable_id) }}">
                                            {{ $transaction->transactionable_type::find($transaction->transactionable_id)->title }}
                                        </a>    
                                        @break
                                    @case('Payments')
                                        <a href="{{ route('admin.invoices.show',$transaction->transactionable_type::find($transaction->transactionable_id)->invoice_id) }}">
                                            {{ App\Models\Setting::first()->invoice_prefix.' '.$transaction->transactionable_type::find($transaction->transactionable_id)->invoice_id }}
                                        </a>
                                        @break
                                    @case('Loan')
                                        <a href="{{ route('admin.loans.show',$transaction->transactionable_id) }}">
                                            {{ $transaction->transactionable_type::find($transaction->transactionable_id)->employee->name .' ( '. $transaction->transactionable_type::find($transaction->transactionable_id)->amount.' ) '.$transaction->transactionable_type::find($transaction->transactionable_id)->created_at  }}
                                        </a>
                                        @break    
                                    @case('Withdrawal')
                                        <a href="{{ route('admin.withdrawals.show',$transaction->transactionable_id) }}">
                                            {{ $transaction->transactionable_type::find($transaction->transactionable_id)->notes }}
                                        </a>
                                        @break

                                    @case('Transfer')
                                        {{ trans('global.from') }}
                                        {{ $transaction->transactionable_type::find($transaction->transactionable_id)->fromAccount->name ?? '-' }} 
                                        {{ trans('global.to') }}
                                        {{ $transaction->transactionable_type::find($transaction->transactionable_id)->toAccount->name ?? '-' }}
                                        @break
                                    @default
                                        
                                @endswitch
                            </td>
                            <td>
                                {{ $transaction->amount ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->createdBy->name ?? ''}}
                            </td>
                            <td>
                                {{ $transaction->created_at->toFormattedDateString() ?? ''}}
                            </td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->render() }}
        </div>
    </div>
</div>
@endsection