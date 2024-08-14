
<div class="row">
    <div class="col-sm-6 col-lg-6">
        <div class="card">
            <div class="card-body bg-success text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ number_format($dailyPayments->sum('amount')) .' EGP' }}</h5>
                    <h5><i class="fa-fw far fa-credit-card"></i> {{ trans('cruds.payment.title') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-6">
        <div class="card">
            <div class="card-body bg-success text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ number_format($payments->sum('amount')) .' EGP' }} </h5>
                    <h5><i class="fa-fw far fa-credit-card"></i> {{ trans('global.monthly_payments') }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered zero-configuration">
                    <thead>
                        <th>#</th>
                        <th>{{ trans('cruds.member.fields.name') }}</th>
                        <th>{{ trans('cruds.invoice.fields.membership') }}</th>
                        <th>{{ trans('cruds.invoice.fields.service_fee') }}</th>
                        <th>{{ trans('cruds.invoice.fields.discount') }}</th>
                        <th>{{ trans('cruds.invoice.fields.net_amount') }}</th>
                        <th>{{ trans('cruds.invoice.fields.paid_amount') }}</th>
                        <th>{{ trans('cruds.invoice.fields.sales_by') }}</th>
                        <th>{{ trans('cruds.invoice.fields.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->membership->member->name ?? '-' }}</td>
                                <td>{{ $invoice->membership->service_pricelist->name ?? '-' }}</td>
                                <td>{{ $invoice->service_fee ?? '-' }}</td>
                                <td>{{ $invoice->discount ?? '-' }}</td>
                                <td>{{ $invoice->net_amount ?? '-' }}</td>
                                <td>{{ $invoice->payments_sum_amount ?? '-' }}</td>
                                <td>{{ $invoice->sales_by->name ?? '-' }}</td>
                                <td>{{ $invoice->created_at->format('Y-m-d') ?? '-' }}</td>
                                <td><a href="{{ route('admin.invoice.showSupervisor',[$invoice->id,$loop->iteration]) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> {{ trans('global.show') }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

