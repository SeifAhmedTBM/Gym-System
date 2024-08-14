@extends('layouts.admin')
@section('content')
    <div class="row py-2">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body bg-primary text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $invoice->net_amount }}</h5>
                    <h5>{{ trans('cruds.invoice.fields.net_amount') }}</h5>
                </div>
            </div>
        </div>
        <!-- /.col-->

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body bg-success text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $invoice->payments_sum_amount }}</h5>
                    <h5>{{ trans('cruds.invoice.fields.paid_amount') }}</h5>
                </div>
            </div>
        </div>
        <!-- /.col-->

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body bg-danger text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $invoice->rest }}</h5>
                    <h5>{{ trans('global.rest') }}</h5>
                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-file"></i> {{ trans('cruds.payment.title') }} | {{ \App\Models\Setting::first()->invoice_prefix.$invoice->id }}</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ trans('cruds.member.fields.name') }}</th>
                            <th>{{ trans('cruds.invoice.fields.account') }}</th>
                            <th>{{ trans('cruds.payment.fields.amount') }}</th>
                            <th>{{ trans('cruds.payment.fields.notes') }}</th>
                            <th>{{ trans('cruds.payment.fields.sales_by') }}</th>
                            <th>{{ trans('cruds.payment.fields.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->payments as $key => $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->membership->member->name ?? '-' }}</td>
                                <td>{{ $payment->account->name ?? '-' }}</td>
                                <td>{{ $payment->amount ?? '-' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                                <td>{{ $payment->sales_by->name ?? '-' }}</td>
                                <td>{{ $payment->created_at ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                            </tr>
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
