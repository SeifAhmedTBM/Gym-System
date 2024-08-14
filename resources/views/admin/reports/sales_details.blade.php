@extends('layouts.admin')
@section('content')
    {{-- branch sources --}}
    <div class="form-group">
        <div class="card">
            <div class="card-header">
                {{ $branch->name }} Sources
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Source</th>
                            <th>Leads</th>
                            <th>Members</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sources as $source)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $source->name }}</td>
                                <td>{{ $source->leads_count }}</td>
                                <td>{{ $source->members_count }}</td>
                                <td>{{ $source->members_count > 0 ? number_format(($source->members_count / $source->leads_count) * 100,2).' %' : '0 %' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- branch sources --}}

    {{-- due payments --}}
    <div class="form-group">
        <div class="card">
            <div class="card-header">
                {{ $branch->name }} Due Payments
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered zero-configuration text-center">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-dark">#</th>
                            <th class="text-dark">{{ trans('global.name') }}</th>
                            <th class="text-dark">Total Collected</th>
                            <th class="text-dark">Total Remaining</th>
                            <th class="text-dark">{{ trans('global.count') }}</th>
                            <th class="text-dark">{{ trans('global.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $key => $sale)
                            <tr>
                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $sale->name ?? '-' }}</td>
                                <td class="font-weight-bold">{{ number_format($sale->invoices->sum('payments_sum_amount')) }} EGP</td>
                                <td class="font-weight-bold">{{ number_format($sale->invoices->sum('rest')) }} EGP</td>
                                <td class="font-weight-bold">{{ $sale->invoices_count }}</td>
                                <td class="font-weight-bold">
                                    <a href="{{ route('admin.invoice.duePayments',$sale->id) }}" class="btn font-weight-bold btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> {{ trans('cruds.invoice.title') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- due payments --}}
@endsection