@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-9">
        @can('export_trainers')
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.trainers-report.export', request()->all())]) !!}
            <button type="submit" class="btn btn-success">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </button>
        {!! Form::close() !!}
    @endcan
    </div>
    <div class="col-lg-3 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center">{{ trans('global.total') }}</h2>
                {{-- {{dd($trainers)}} --}}
                <h2 class="text-center">{{ number_format($trainers->sum('total_invoices')) }}</h2>
            </div>
        </div>
    </div>

</div>
<form action="{{ route('admin.reports.trainers-report') }}" method="get">
    <div class="row form-group">
        <div class="col-md-8">
            <label for="date">{{ trans('global.filter') }}</label>
            <div class="input-group">
                <select name="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                    <option value="{{ NULL }}" selected>All Branches</option>
                    @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="trainer_id" class="form-control">
                    <option value="{{ NULL }}" selected>Trainer</option>
                    @foreach (\App\Models\User::whereRelation('roles','title','Trainer')->pluck('name','id') as $trainer_id => $trainer_name)
                        <option value="{{ $trainer_id }}" {{ request('trainer_id') == $trainer_id ? 'selected' : '' }}>{{ $trainer_name }}</option>
                    @endforeach
                </select>
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>


<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fas fa-dumbbell"></i> {{ trans('global.trainers_report') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        <th class="text-dark">{{ trans('cruds.branch.title_singular') }}</th>
                        <th class="text-dark">{{ trans('global.total_invoices') }}</th>
                        <th class="text-dark">{{ trans('global.this_month_collected') }}</th>
                        <th class="text-dark">{{ trans('global.previous_month_collected') }}</th>
                        <th class="text-dark">Attendances this Month</th>
                        <th class="text-dark">Attendances previous Month</th>
                        <th class="text-dark">{{ trans('global.this_month_commission') }}</th>
                        <th class="text-dark">{{ trans('global.previous_month_commissions') }}</th>
                        <th class="text-dark">{{ trans('global.total_commissions') }}</th>
                        <th class="text-dark">{{ trans('global.view') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trainers as $trainer)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td class="font-weight-bold ">{{ $trainer['name'] }}</td>
                            <td class="font-weight-bold ">{{ $trainer['branch_name'] }}</td>
                            <td class="font-weight-bold ">
                                {{ number_format($trainer['total_invoices']) }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['total_payments_this_month'] }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['total_payments_previous_month'] }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['attendances_this_month'] }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['attendances_previous_month'] }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['commissions_this_month'] }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['commissions_previous_month'] }}
                            </td>
                            <td class="font-weight-bold ">
                                {{ $trainer['commissions_previous_month'] }}
                            </td>
                            <td>
                                <a href="{{ route('admin.reports.show-trainer-report', [
                                        $trainer['id'],
                                        'date='.(isset(request()->date) ? request()->date : date('Y-m'))
                                    ]) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> {{ trans('global.show') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{-- {{ $trainers->links() }} --}}
    </div>
</div>
@endsection