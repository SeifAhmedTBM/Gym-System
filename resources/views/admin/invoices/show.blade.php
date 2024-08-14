@extends('layouts.admin')
@section('content')
    @can('invoice.index')
        <div class="form-group">
            <a class="btn btn-danger" href="{{ route('admin.invoices.index') }}">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back_to_list') }}
            </a>
        </div>
    @endcan


    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.invoice.title') }}
            @if (auth()->user()->roles[0]->title == 'Super Visor')
                <form action="{{ route('admin.printInvoiceSupervisor', [$invoice->id,$alt_id]) }}" class="d-inline float-right" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-xs">
                        <i class="fa fa-print"></i> {{ trans('global.print') }}
                    </button>
                </form>
            @else
                <form action="{{ route('admin.invoice.print', $invoice->id) }}" class="d-inline float-right" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-xs">
                        <i class="fa fa-print"></i> {{ trans('global.print') }}
                    </button>
                </form>
            @endif
            
        </div>

        <div class="card-body">
            <img src="{{ asset('images/'. $setting->menu_logo) }}" alt="Logo" width="100" class="mb-4">
            <div class="form-group">
                <div class="row" style="line-height: 32px !important;">
                    <div class="col-md-6">
                        {!! $invoice_tmp['left_section'] !!}
                    </div>
                    <div class="col-md-6 text-right">
                        {!! $invoice_tmp['right_section'] !!}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="row my-2">
                            <div class="col-md-6">
                                <h6>{{ trans('cruds.membership.title_singular') }}</h6>
                            </div>

                            <div class="col-md-6">
                                <span class="d-block">{{ $invoice->membership->service_pricelist->name ?? '' }} </span>
                                <span class="badge p-2 badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$invoice->membership->membership_status] }}">
                                    {{ \App\Models\Membership::MEMBERSHIP_STATUS[$invoice->membership->membership_status] }}
                                </span>
                            </div>
                        </div>
                        @if ($invoice->membership->service_pricelist->serviceOptionsPricelist)
                            @foreach( $invoice->membership->service_pricelist->serviceOptionsPricelist as $option)
                                @if($option->count>0)
                                <div class="row my-2">
                                    <div class="col-md-6">
                                        <h6>{{ $option->service_option->name ?? '-'}}</h6>
                                    </div>

                                    <div class="col-md-6">
                                    <span>{{ $option->service_option ? $option->count ?? '0' : '-' }}</span>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="row my-2">
                            <div class="col-md-6">
                                <h6>{{ trans('cruds.invoice.fields.service_fee') }}</h6>
                            </div>

                            <div class="col-md-6">
                               <span>{{ $invoice->service_fee ?? '' }}</span>
                            </div>
                        </div>

                        <div class="row my-2">
                            <div class="col-md-6">
                                <h6>{{ trans('cruds.invoice.fields.discount') }}</h6>
                            </div>

                            <div class="col-md-6">
                                <span>{{ $invoice->discount ?? '' }}</span>
                            </div>
                        </div>

                        <div class="row my-2">
                            <div class="col-md-6">
                                <h6>{{ trans('cruds.invoice.fields.net_amount') }}</h6>
                            </div>

                            <div class="col-md-6">
                                <span>{{ $invoice->net_amount ?? '' }}</span>
                            </div>
                        </div>

                        <div class="row my-2">
                            <div class="col-md-6">
                                <h6>{{ trans('cruds.invoice.fields.paid_amount') }}</h6>
                            </div>

                            <div class="col-md-6">
                                <span>{{ $invoice->payments_sum_amount ?? '' }}</span>
                            </div>
                        </div>

                        <div class="row my-2">
                            <div class="col-md-6">
                                <h6>{{ trans('global.rest') }}</h6>
                            </div>

                            <div class="col-md-6">
                                <span>{{ $invoice->rest ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.payment.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.account.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.payment.fields.amount') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.invoice.fields.sales_by') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.bonu.fields.created_by') }}
                                     </th>
                                    <th>
                                       {{ trans('global.created_at') }}
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @foreach ($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->account->name ?? '' }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->sales_by->name ?? '' }}</td>
                                        <td>{{ $payment->created_by->name ?? '' }}</td>
                                        <td>{{ $payment->created_at ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 {{ app()->isLocale('ar') ? 'text-right' : 'text-left' }}">
                        {!! $invoice_tmp['footer'] !!}
                    </div>
                </div>

            </div>
        </div>
    </div>



@endsection
