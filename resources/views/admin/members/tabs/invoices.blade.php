<div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover zero-configuration">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('cruds.invoice.title') }}</th>
                        <th>{{ trans('cruds.membership.title') }}</th>
                        <th>{{ trans('cruds.invoice.fields.discount') }}</th>
                        <th>{{ trans('cruds.invoice.title_singular') }}</th>
                        <th>{{ trans('cruds.status.title_singular') }}</th>
                        <th>{{ trans('cruds.invoice.fields.sales_by') }}</th>
                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                        <th>{{ trans('cruds.invoice.fields.created_at') }}</th>
                        <th>{{ trans('cruds.action.title') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}">
                                    {{ $invoice->invoicePrefix() }}{{ $invoice->id }}
                                </a>
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ $invoice->membership->service_pricelist->name ?? '-' }}
                                </span>
                                <span class="d-block">
                                    {{ trans('cruds.invoice.fields.service_fee') }} :
                                    {{ number_format($invoice->service_fee) ?? '-' }}
                                </span>
                                <span
                                    class="badge badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$invoice->membership->membership_status] }} p-2">
                                    {{ \App\Models\Membership::MEMBERSHIP_STATUS[$invoice->membership->membership_status] }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ $invoice->discount }}
                                </span>
                                <span class="d-block">
                                    {{ $invoice->discount_notes }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block text-success font-weight-bold">
                                    {{ trans('global.net') }} : {{ number_format($invoice->net_amount) }}
                                </span>
                                <span class="d-block text-primary font-weight-bold">
                                    {{ trans('invoices::invoice.paid') }} :
                                    {{ number_format($invoice->payments_sum_amount) }}
                                </span>
                                <span class="d-block text-danger font-weight-bold">
                                    {{ trans('global.rest') }} :
                                    {{ number_format($invoice->rest) }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge p-2 badge-{{ \App\Models\Invoice::STATUS_COLOR[$invoice->status] }}">
                                    {{ \App\Models\Invoice::STATUS_SELECT[$invoice->status] }}
                                </span>
                            </td>
                            <td>
                                {{ $invoice->sales_by->name ?? '-' }}
                            </td>
                            <td>
                                {{ $invoice->created_by->name ?? '-' }}
                            </td>
                            <td>{{ $invoice->created_at->toFormattedDateString() }}</td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button"
                                        id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('global.action') }}
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                        @can('payment_create')
                                            @if ($invoice->status == 'partial')
                                                <a href="{{ route('admin.invoice.payment', $invoice->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fa fa-plus-circle"></i>&nbsp;
                                                    {{ trans('cruds.payment.title_singular') }}
                                                </a>
                                            @endif
                                        @endcan
                                        @if ($invoice->status == 'partial')
                                            <a href="javascript:void(0)" onclick="setSettlementInvoice(this)"
                                                data-toggle="modal" data-target="#settlement_invoice"
                                                data-url="{{ route('admin.settlement.invoice', $invoice->id) }}"
                                                class="dropdown-item"><i class="fas fa-check-circle"></i> &nbsp;
                                                {{ trans('global.settlement') }}</a>
                                        @endif

                                        @can('invoice_show')
                                            <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                                class="dropdown-item">
                                                <i class="fa fa-eye"></i>&nbsp;
                                                {{ trans('global.view') }}
                                            </a>
                                        @endcan

                                        @can('invoice_edit')
                                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                                class="dropdown-item">
                                                <i class="fa fa-pencil"></i>&nbsp;
                                                Edit
                                            </a>
                                        @endcan

                                        @can('payment_show')
                                            <a href="{{ route('admin.payments.index') }}?invoice_id={{ $invoice->id }}"
                                                class="dropdown-item">
                                                <i class="fa fa-money"></i>&nbsp;
                                                Show Payments
                                            </a>
                                        @endcan

                                        @can('invoice_delete')
                                            <form action="{{ route('admin.invoices.destroy', $invoice->id) }}"
                                                method="POST"onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fa fa-times"></i> &nbsp;
                                                    {{ trans('global.delete') }}
                                                </button>
                                            </form>
                                        @endcan

                                        <form action="{{ route('admin.invoice.download', $invoice->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa fa-download"></i> &nbsp;
                                                {{ trans('global.downloadFile') }}
                                            </button>
                                        </form>


                                        <form action="{{ route('admin.invoice.send', $invoice->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fab fa-whatsapp"></i> &nbsp;
                                                {{ trans('global.whatsapp') }}
                                            </button>
                                        </form>

                                        @can('refund_create')
                                            @if ($invoice->status !== 'refund')
                                                <a href="{{ route('admin.invoice.refund', $invoice->id) }}"
                                                    class="dropdown-item"><i class="fas fa-exchange-alt"></i>&nbsp;
                                                    &nbsp; {{ trans('cruds.refund.title') }}</a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="11" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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

<script>
    function setSettlementInvoice(button) {
        let formURL2 = $(button).data('url');
        $("#settlement_invoice_form").attr('action', formURL2);
    }
</script>
