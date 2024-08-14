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
    <div class="form-group row">
        <div class="col-md-3 offset-9">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">{{ trans('global.total') }}</h3>
                    <h3 class="text-center">{{ number_format($due_payments->sum('rest')) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-file"></i> Due Payments </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                    <thead>
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
                        @forelse ($due_payments as $key => $due_payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $due_payment->id ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.members.show', $due_payment->membership->member_id) }}"
                                        target="_blank">
                                        {{ $due_payment->membership->member->member_code ?? '-' }}
                                        <br>
                                        {{ $due_payment->membership->member->name ?? '-' }}
                                        <br>
                                        {{ $due_payment->membership->member->phone ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $due_payment->membership->service_pricelist->name ?? '-' }} </td>
                                <td>{{ number_format($due_payment->net_amount) ?? '-' }} EGP</td>
                                <td>{{ number_format($due_payment->payments_sum_amount) ?? '-' }} EGP</td>
                                <td>{{ number_format($due_payment->rest) ?? '-' }} EGP</td>
                                <td>{{ $due_payment->created_at ?? '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.invoice.payments', $due_payment->id) }}"
                                            class="btn btn-info btn-sm"><i class="fa fa-eye"></i>
                                            {{ trans('cruds.payment.title') }}</a>

                                        <a href="{{ route('admin.invoice.payment', $due_payment->id) }}"
                                            class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i>
                                            {{ trans('cruds.payment.title_singular') }} </a>
                                        @if (config('domains')[config('app.url')]['settlement_invoices'] == true)
                                            <a href="javascript:void(0)" onclick="setSettlementInvoice(this)"
                                                data-toggle="modal" data-target="#settlement_invoice"
                                                data-url="{{ route('admin.settlement.invoice', $due_payment->id) }}"
                                                class="btn btn-primary"><i class="fas fa-check-circle"></i> &nbsp;
                                                {{ trans('global.settlement') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="8" class="text-center">{{ trans('global.no_data_available') }}</td>
                        @endforelse
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
