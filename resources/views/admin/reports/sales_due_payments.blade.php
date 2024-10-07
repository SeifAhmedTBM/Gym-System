@php
    $startOfMonth = now()->startOfMonth()->toDateString();
    $endOfMonth = now()->endOfMonth()->toDateString();
@endphp
@extends('layouts.admin')
@section('content')

    <form method="GET" action="{{ route('admin.reports.sales_due_payments') }}">
        <div class="row align-items-end mb-5">
            <div class="col-md-3">
                <div>
                    <label for="branch_id">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-control">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div>
                    <label for="sales_id">Sales</label>
                    <select name="sales_id" id="sales_id" class="form-control">
                        <option value="">All Sales</option>
                        @foreach($sales_representatives as $sales)
                            <option value="{{ $sales->id }}" {{ request('sales_id') == $sales->id ? 'selected' : '' }}>
                                {{ $sales->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div>
                    <label for="start_date">Start Date</label>

                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date')??$startOfMonth}}">
                </div>
            </div>
{{--            {{ request('end_date')??$endOfMonth }}--}}
            <div class="col-md-2">
                <div>
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{request('end_date')??$endOfMonth }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <div class="modal fade" id="settlement_invoice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Settlement</h5>
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
                    <h3 class="text-center">{{ number_format($due_payments->sum('rest')) }} EGP</h3>
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
                                <a href="{{ route('admin.members.show', $due_payment->membership->member_id) }}" target="_blank">
                                    {{ $due_payment->membership->member->member_code ?? '-' }}
                                    <br>
                                    {{ $due_payment->membership->member->name ?? '-' }}
                                    <br>
                                    {{ $due_payment->membership->member->phone ?? '-' }}
                                </a>
                            </td>
                            <td>{{ $due_payment->membership->service_pricelist->name ?? '-' }}</td>
                            <td>{{ number_format($due_payment->net_amount) ?? '-' }} EGP</td>
                            <td>{{ number_format($due_payment->payments_sum_amount) ?? '-' }} EGP</td>
                            <td>{{ number_format($due_payment->rest) ?? '-' }} EGP</td>
                            <td>{{ $due_payment->created_at ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.invoice.payments', $due_payment->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i> {{ trans('cruds.payment.title') }}
                                    </a>
                                    <a href="{{ route('admin.invoice.paymentDuePayments', $due_payment->id) }}" class="btn btn-success btn-sm">
                                        <i class="fa fa-plus-circle"></i> {{ trans('cruds.payment.title_singular') }}
                                    </a>
                                    @if (config('domains')[config('app.url')]['settlement_invoices'] == true)
                                        <a href="javascript:void(0)" onclick="setSettlementInvoice(this)"
                                           data-toggle="modal" data-target="#settlement_invoice"
                                           data-url="{{ route('admin.settlement.invoice', $due_payment->id) }}"
                                           class="btn btn-primary">
                                            <i class="fas fa-check-circle"></i> &nbsp; {{ trans('global.settlement') }}
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{-- {{ $due_payments->links() }} --}}
        </div>
    </div>

@endsection


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var branchSelect = document.getElementById('branch_id');
            var salesSelect = document.getElementById('sales_id');
            getSales();
            function getSales(){
                var branch_id = branchSelect.value;

                var xhr = new XMLHttpRequest();
                xhr.open('GET', '{{ route("admin.reports.get.sales.by.branch") }}' + '?branch_id=' + branch_id, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        let selected_Sales_id = salesSelect.value
                        console.log(selected_Sales_id)

                        salesSelect.innerHTML = '<option value="">All Sales</option>';

                        data.forEach(function(sales) {
                            var option = document.createElement('option');
                            if(selected_Sales_id == sales.id){
                                option.selected = true
                            }
                            option.value = sales.id;
                            option.textContent = sales.name;
                            salesSelect.appendChild(option);
                        });
                    }
                };
                xhr.send();
            }
            branchSelect.addEventListener('change', function() {
                getSales();

            });
        });

        function setSettlementInvoice(button) {
            var url = button.getAttribute('data-url');
            var form = document.getElementById('settlement_invoice_form');
            form.setAttribute('action', url);
        }
    </script>

