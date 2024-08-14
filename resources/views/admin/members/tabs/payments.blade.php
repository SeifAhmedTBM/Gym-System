<div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
    <div class="row">
        <div class="col-md-12">
            @forelse ($invoices as $invoice)
                <div class="accordion" id="invoicesPayment">
                    <div class="card">
                        <div class="card-header bg-info" id="headingOne">
                            <h2 class="mb-0">
                                <button class="btn btn-block text-left text-white" type="button" data-toggle="collapse"
                                    data-target="#{{ $invoice->id }}" aria-expanded="true" aria-controls="headingOne">
                                    <h5> Invoice Number :
                                        {{ $invoice->invoicePrefix() . $invoice->id }}</h5>
                                </button>
                            </h2>
                        </div>

                        <div id="{{ $invoice->id }}" class="collapse {{ $invoice->first()->id ? 'show' : '' }}"
                            aria-labelledby="headingOne" data-parent="#invoicesPayment">
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ trans('cruds.payment.fields.payment_method') }}
                                            </th>
                                            <th>{{ trans('global.payments.amount') }}</th>
                                            <th>{{ trans('cruds.member.fields.sales_by') }}</th>
                                            <th>{{ trans('created_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->payments as $payment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $payment->account->name ?? '-' }}</td>
                                                <td>{{ $payment->amount }}</td>
                                                <td>{{ $payment->sales_by->name ?? '-' }}</td>
                                                <td>{{ $payment->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <h4 class="text-center">No Data Available</h4>
            @endforelse

        </div>
    </div>
</div>
