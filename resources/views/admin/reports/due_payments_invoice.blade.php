@php
    $startOfMonth = now()->startOfMonth()->toDateString();
    $endOfMonth = now()->endOfMonth()->toDateString();
@endphp
@extends('layouts.admin')
@section('content')
    <div class="modal fade" id="settlement_invoice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Settlement
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'POST', 'id' => 'settlement_invoice_form']) !!}
                <div class="modal-body">
                    <h4 class="text-warning font-weight-bold text-center">
                        {{ trans('global.settlement_invoice') }}
                    </h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        {{ trans('global.close') }}
                    </button>
                    <button type="submit" class="btn btn-success">{{ trans('global.yes') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="form-group row mt-3">
        <div class="col-md-9">
            <form method="GET">
                <div class="form-group d-flex align-items-end">
                    <div class="mr-2">
                        <label for="from_date">{{ trans('global.timeFrom') }}</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') ?? $startOfMonth }}">
                    </div>
                    <div class="mr-2">
                        <label for="end_date">{{ trans('global.timeTo') }}</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') ?? $endOfMonth }}">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ trans('global.filter') }}</button>
                </div>
            </form>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">{{ trans('global.total') }}</h3>
                    <h3 class="text-center">
                        @if ($sale)
                            {{ number_format($sale->invoices->sum('rest')) }}
                        @else
                            {{ number_format(0) }}
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-file"></i> {{ trans('cruds.invoice.title') }} </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice Number</th>
                        <th>{{ trans('cruds.member.fields.name') }}</th>
                        <th>Service</th>
                        <th>{{ trans('cruds.invoice.fields.net_amount') }}</th>
                        <th>{{ trans('cruds.invoice.fields.paid_amount') }}</th>
                        <th>{{ trans('global.rest') }}</th>
                        <th>{{ trans('cruds.payment.fields.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($sale)
                        @forelse ($sale->invoices as $key => $invoice)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->id ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.members.show', $invoice->membership->member_id) }}" target="_blank">
                                        {{ $invoice->membership->member->member_code ?? '-' }}
                                        <br>
                                        {{ $invoice->membership->member->name ?? '-' }}
                                        <br>
                                        {{ $invoice->membership->member->phone ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $invoice->membership->service_pricelist->name ?? '-' }} </td>
                                <td>{{ number_format($invoice->net_amount) ?? '-' }} EGP</td>
                                <td>{{ number_format($invoice->payments_sum_amount) ?? '-' }} EGP</td>
                                <td>{{ number_format($invoice->rest) ?? '-' }} EGP</td>
                                <td>{{ $invoice->created_at ?? '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.invoice.payments', $invoice->id) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i>
                                            {{ trans('cruds.payment.title') }}</a>

                                        <a href="{{ route('admin.invoice.payment', $invoice->id) }}" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i>
                                            {{ trans('cruds.payment.title_singular') }}
                                        </a>
                                        @if (config('domains')[config('app.url')]['settlement_invoices'] == true)
                                            <a href="javascript:void(0)" onclick="setSettlementInvoice(this)"
                                               data-toggle="modal" data-target="#settlement_invoice"
                                               data-url="{{ route('admin.settlement.invoice', $invoice->id) }}"
                                               class="btn btn-info"><i class="fas fa-check-circle"></i> &nbsp;
                                                {{ trans('global.settlement') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ trans('global.no_data_available') }}</td>
                            </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="9" class="text-center">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- {{ $sales->links() }} --}}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function setSettlementInvoice(button) {
            let formURL2 = $(button).data('url');
            $("#settlement_invoice_form").attr('action', formURL2);
        }
    </script>
@endsection
