@extends('layouts.admin')
@section('content')

    <div class="row form-group">
        <div class="col-md-4">
            <form action="{{ route('admin.reports.due-payments-report') }}" method="get">
                <div class="input-group">
                    <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>All Branch</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-8 text-right">
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
{{--                            @php(dd($sale))--}}
                            <tr>
                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $sale->name ?? '-' }}</td>
                                <td class="font-weight-bold">{{ number_format($sale->invoices_monthly->sum('payments_sum_amount')) }} EGP</td>
                                <td class="font-weight-bold">{{ number_format($sale->invoices_monthly->where('status','partial')->sum('rest')) }} EGP</td>
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
