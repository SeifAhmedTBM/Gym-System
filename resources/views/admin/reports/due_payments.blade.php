@php
    $startOfMonth = now()->startOfMonth()->toDateString();
    $endOfMonth = now()->endOfMonth()->toDateString();
@endphp
@extends('layouts.admin')
@section('content')

    <div class="row form-group align-items-end">
        <div class="col-md-8">
            <form  method="get">
                <div class="form-row">
                    <div class="col">
                        <label for="end_date">Branch</label>

                        <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                            <option value="{{ NULL }}" selected>All Branch</option>
                            @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                                <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label for="from_date">{{ trans('global.timeFrom') }}</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date')??$startOfMonth }}">
                    </div>

                    <div class="col">
                        <label for="end_date">{{ trans('global.timeTo') }}</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date')??$endOfMonth }}">
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4 text-right">
            <h3 class="font-weight-bold">
                {{ trans('global.due_payments') }} : <span class="text-danger">{{ number_format($counter) }} EGP</span>
            </h3>
        </div>
    </div>

    <div class="card">
        <div class="card-header font-weight-bold">
            <i class="fa fa-file"></i> {{ trans('global.due_payments_report') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center table-outline table-striped table-hover zero-configuration">
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
                    @forelse ($sales as $key => $sale)
                        {{--                            @php(dd($sales[10]))--}}
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
                    @empty
                        <td colspan="5" class="text-center">{{ trans('global.no_data_available') }}</td>
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