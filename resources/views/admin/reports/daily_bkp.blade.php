  
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-dollar"></i> {{ trans('global.daily_analysis') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2">
                            <div class="card-body text-center text-white bg-primary">
                                <div>
                                    <h3>{{ number_format($payments->sum('amount')) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('global.payments.title') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2 ">
                            <div class="card-body text-center text-white bg-primary">
                                <div>
                                    <h3>{{ number_format($external_payments->sum('amount')) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('cruds.externalPayment.title') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2">
                            <div class="card-body text-center text-white bg-primary">
                                <div>
                                    <h3>{{ number_format($total_income) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('global.total_income') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ///////////////////////////// --}}
                <div class="row">
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2">
                            <div class="card-body text-center text-white bg-danger">
                                <div>
                                    <h3>{{ number_format($refunds->sum('amount')) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('cruds.refund.title') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2 ">
                            <div class="card-body text-center text-white bg-danger">
                                <div>
                                    <h3>{{ number_format($expenses->sum('amount')) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('cruds.expense.title') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-sm-4 col-lg-4">
                        <div class="card mb-2 text-center text-white bg-danger">
                            <div class="card-body">
                                <div>
                                    <h3>{{ number_format($total_outcome) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('global.total_outcome') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- /////////////////////////////// --}}
                <div class="row">
                    <div class="col-sm-4 col-lg-4 offset-8">
                        <div class="card mb-2 text-center text-white bg-success">
                            <div class="card-body">
                                <div>
                                    <h3>{{ number_format($net_income) }} EGP<span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('global.net_income') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- /////////////////////////////// --}}
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-users"></i> {{ trans('global.members_analysis') }}</h3>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body text-center text-white bg-info">
                                <div>
                                    <h3>{{ $renewals_payments_count }} ({{ number_format($renewals_payments).' EGP' }}) <span class="fs-6 fw-normal"></h3>
                                    <div>{{ trans('cruds.membership.fields.renew') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-sm-6 col-lg-6">
                        <div class="card mb-4 ">
                            <div class="card-body text-center text-white bg-info">
                                <div>
                                    <h3>{{ $new_payments_count }} ({{ number_format($new_payments).' EGP' }})  <span class="fs-6 fw-normal"></h3>
                                    <div>New Members</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ///////////////////////// --}}
            </div>
        </div>

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
                            @foreach ($accounts as $account)
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>{{ trans('global.income') }} : <span class="text-primary">{{ $total_income }} EGP</span></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                {{ trans('cruds.externalPayment.title') }}
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('cruds.externalPayment.title') }}</th>
                                            <th>{{ trans('cruds.externalPayment.fields.amount') }}</th>
                                            <th>{{ trans('cruds.externalPayment.fields.created_by') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($external_payments as $externalPayment)
                                            <tr>
                                                <td>{{ $externalPayment->title }}</td>
                                                <td>
                                                    {{ $externalPayment->amount }} -
                                                    {{ $externalPayment->account->name ?? '-' }}
                                                </td>
                                                <td>{{ $externalPayment->created_by->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Payments By Service types
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('cruds.serviceType.title') }}</th>
                                            <th>{{ trans('global.count') }}</th>
                                            <th>{{ trans('global.income') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($service_payments as $key => $payment)
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>{{ $payment->count() }}</td>
                                                <td>{{ $payment->sum('amount') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                   

                    @foreach ($service_payments as $key => $payment)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <strong>{{ $key }} - {{ trans('global.income') }} : <span class="text-primary">{{ $payment->sum('amount') }} EGP ({{ $payment->count().' Payment' }})</span> </strong> 
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover table-bordered zero-configuration table-responsive">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('cruds.payment.title_singular') }}
                                                    {{ trans('cruds.payment.fields.id') }}</th>
                                                <th>{{ trans('cruds.payment.fields.invoice') }}</th>
                                                <th>{{ trans('global.payments.amount') }}</th>
                                                <th>{{ trans('cruds.payment.fields.sales_by') }}</th>
                                                <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payment as $pay)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.payments.show',$pay->id) }}">
                                                            {{ $pay->id }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.invoices.show', $pay->invoice_id) }}"
                                                            target="_blank">
                                                            {{ $pay->invoice->invoicePrefix() . ' ' . $pay->invoice_id }}
                                                        </a>
            
                                                        <a href="{{ route('admin.members.show',$pay->invoice->membership->member_id) }}">
                                                            <b class="d-block">
                                                                {{ $pay->invoice->membership->memberPrefix() . $pay->invoice->membership->member->member_code ?? '' }}
                                                                ({{ $pay->invoice->membership->member->name }} )
                                                            </b>
                    
                                                            <b class="d-block">{{ $pay->invoice->membership->member->phone ?? '' }}</b>
                                                        </a>
            
                                                        <div class="p-2 badge badge-success">
                                                            {{ $pay->invoice->membership->service_pricelist->name }}
                
                                                            {{ $pay->invoice->membership->service_pricelist->session_count != 0 ? $pay->invoice->membership->service_pricelist->session_count . ' Session/s ' : '' }}
                                                        </div>
            
                                                        <span class="p-2 badge badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$pay->invoice->membership->membership_status] }}">
                                                            {{ \App\Models\Membership::MEMBERSHIP_STATUS[$pay->invoice->membership->membership_status] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $pay->amount }} - {{ $pay->account->name ?? '' }}</td>
                                                    <td>{{ $pay->sales_by->name ?? '-' }}</td>
                                                    <td>{{ $pay->created_by->name ?? '-' }}</td>
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
        </div>

        <div class="card">
            <div class="card-header">
                <h3>{{ trans('global.outcome') }} : <span class="text-danger">{{ $total_outcome }} EGP</span></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                {{ trans('cruds.loan.title') }}
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

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                {{ trans('cruds.expense.title') }}
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('cruds.expense.fields.name') }}</th>
                                            <th>{{ trans('cruds.expense.fields.expenses_category') }}</th>
                                            <th>{{ trans('cruds.expense.fields.amount') }}</th>
                                            <th>{{ trans('cruds.expense.fields.created_by') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenses as $expense)
                                            <tr>
                                                <td>{{ $expense->name }}</td>
                                                <td>{{ $expense->expenses_category->name ?? '-' }}</td>
                                                <td>{{ $expense->amount }} - {{ $expense->account->name ?? '-' }}</td>
                                                <td>{{ $expense->created_by->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Refunds by service types
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('cruds.serviceType.title') }}</th>
                                            <th>{{ trans('global.count') }}</th>
                                            <th>{{ trans('global.outcome') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($service_refunds as $key => $refund)
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>{{ $refund->count() }}</td>
                                                <td>{{ $refund->sum('amount') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @foreach ($service_refunds as $key => $refund)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <strong>{{ $key }} - {{ trans('global.outcome') }} : <span class="text-danger">{{ $refund->sum('amount') }} EGP ({{ $refund->count().' Refund' }})</span> </strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('cruds.refund.fields.invoice') }}</th>
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
                                                            target="_blank">{{ $ref->invoice->invoicePrefix() . ' ' . $ref->invoice_id }}
                                                        </a>
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
        </div>