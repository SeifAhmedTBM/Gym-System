@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <form action="{{ route('admin.reports.tax-accountant') }}" method="get">
            {{-- <div class="row form-group">
                <div class="col-md-12">
                    <label for="date">{{ trans('global.date') }}</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="from" value="{{ request('from') ?? date('Y-m-01') }}">
                        <input type="date" class="form-control" name="to" value="{{ request('to') ?? date('Y-m-t') }}">
                        <select name="branch_id[]" id="branch_id" class="form-control select2" multiple style="width:30%!important">
                            @foreach ($branches as $id => $name)
                                <option value="{{ $id }}" {{ (request('branch_id') ? in_array($id,request('branch_id')) : '') ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="account_id[]" id="account_id" class="form-control select2" multiple style="width:auto!important">
                            @foreach ($accounts as $id => $name)
                                <option value="{{ $id }}" {{ (request('account_id') ? in_array($id,request('account_id')) : '') ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-prepend">
                            <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                            <a href="{{ route('admin.reports.tax-accountant.export',request()->all()) }}" class="btn btn-info">
                                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="form-group row">
                <div class="col-md-10">
                    @include('admin_includes.filters', [
                        'columns' => [
                            'branch_id'  => ['label' => 'Branch', 'type' => 'select', 'data' => $branches],
                            'account_id' => ['label' => 'Account', 'type' => 'select', 'data' => $accounts,'related_to' => 'account'],
                            'created_at' => ['label' => trans('global.created_at'), 'type' => 'date', 'from_and_to' => true]
                        ],
                        'route' => 'admin.reports.tax-accountant'
                    ])
                    <a href="{{ route('admin.reports.tax-accountant.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                    </a>
                </div>
                <div class="col-md-2">
                    <h3 class="text-center">{{ number_format($payments->sum('amount')) }}</h3>
                    <h3 class="text-center">{{ trans('cruds.payment.title_singular') }}</h3>
                </div>
            </div>
        </form>
    </div>
    <div class="form-group">
        <div class="card">
            <div class="card-header">
                Tax Accountant
            </div>
            <div class="card-body">
                <label>Month</label>
                
                <table class="table table-striped table-hover table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>National</th>
                            <th>Pricelist</th>
                            <th>Amount</th>
                            <th>Account</th>
                            <th>Branch</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{-- {{ $payment->invoice->membership->member->code ?? '-' }} <br> --}}
                                    {{ $payment->invoice->membership->member->name ?? '-' }} <br>
                                    {{ $payment->invoice->membership->member->phone ?? '-' }}
                                </td>
                                <td>{{ $payment->invoice->membership->member->national ?? '-' }}</td>
                                <td>{{ $payment->invoice->membership->service_pricelist->name ?? '-' }}</td>
                                <td>{{ number_format($payment->amount) ?? '-' }}</td>
                                <td>{{ $payment->account->name ?? '-' }}</td>
                                <td>{{ $payment->invoice->membership->member->branch->name ?? '-' }}</td>
                                <td>{{ $payment->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection