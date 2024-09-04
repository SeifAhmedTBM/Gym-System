@extends('layouts.admin')
@section('content')
    {{-- filter  --}}
    <div class="form-group">
        <div class="card">
            <div class="card-body">
                <form action="{{ URL::current() }}" method="get">
                    <div class="form-group">
                        <label for="date">{{ trans('global.date') }}</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="from"
                                value="{{ request('from') ?? date('Y-m-01') }}">
                            <input type="date" class="form-control" name="to"
                                value="{{ request('to') ?? date('Y-m-t') }}">
                            <select name="branch_id" id="branch_id" class="form-control"
                                {{ $employee && $employee->branch_id != null ? 'readonly' : '' }}>
                                <option value="{{ null }}" selected >All Branches</option>
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

    {{-- cards --}}
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
    {{-- card --}}

    {{-- service payments --}}
    @foreach ($service_payments as $key => $payment)
        <div class="form-group">
            <div class="card">
                <div class="card-header">
                    <strong>{{ $key }} - {{ trans('global.income') }} : <span
                            class="text-white">{{ number_format($payment->sum('amount')) }} EGP
                            ({{ $payment->count() . ' Payment' }})
                        </span> </strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover table-bordered zero-configuration">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member</th>
                                <th>Service</th>
                                <th>Service Type</th>
                                <th>{{ trans('global.payments.amount') }}</th>
                                <th>Sales By</th>
                                <th>Created at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment as $pay)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $pay->invoice_id) }}">
                                            {{ $pay->invoice->membership->member->branch->invoice_prefix . $pay->invoice_id }}
                                            <br>
                                            {{ $pay->invoice->created_at->format('Y-m-d') }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.members.show', $pay->invoice->membership->member_id) }}">
                                            <b class="d-block">
                                                {{ $pay->invoice->membership->member->branch->member_prefix . $pay->invoice->membership->member->member_code ?? '' }}
                                                ({{ $pay->invoice->membership->member->name }})
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
                                        <span
                                            class="p-2 badge badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$pay->invoice->membership->membership_status] }}">
                                            {{ \App\Models\Membership::MEMBERSHIP_STATUS[$pay->invoice->membership->membership_status] }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($pay->amount) }} - {{ $pay->account->name ?? '' }}</td>
                                    {{-- <td>{{ $pay->sales_by->name ?? '-' }}</td> --}}
                                    <td>{{ $pay->invoice->sales_by->name ?? '-' }}</td>
                                    <td>{{ $pay->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    {{-- service payments --}}

    {{-- service refunds --}}
    @foreach ($service_refunds as $key => $refund)
        <div class="form-group">
            <div class="card">
                <div class="card-header bg-danger">
                    <strong>
                        {{ $key }} - {{ trans('global.outcome') }} : <span>{{ number_format($refund->sum('amount')) }} EGP ({{ $refund->count() . ' Refund' }})</span> 
                    </strong>
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
                                            target="_blank">{{ $ref->invoice->branch->invoice_prefix . $ref->invoice_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.members.show', $ref->invoice->membership->member_id) }}">
                                            <b class="d-block">
                                                {{ $ref->invoice->branch->member_prefix . $ref->invoice->membership->member->member_code ?? '' }}
                                                ({{ $ref->invoice->membership->member->name }})
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
                                    <td>{{ number_format($ref->amount) }} - {{ $ref->account->name ?? '-' }}</td>
                                    <td>{{ $ref->created_by->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    {{-- service refunds --}}
@endsection
