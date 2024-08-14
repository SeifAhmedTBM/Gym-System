@extends('layouts.admin')
@section('content')
    {{-- <div class="mb-2">
        @can('export_revenues')
            {!! Form::open([
                'class' => 'd-inline',
                'method' => 'POST',
                'url' => route('admin.reports.revenues-report.export', request()->all()),
            ]) !!}
            <button type="submit" class="btn btn-success">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </button>
            {!! Form::close() !!}
        @endcan
    </div> --}}
    <div class="card shadow-sm">
        <div class="card-header font-weight-bold">
            <i class="fa fa-money-bill"></i> Sessions Revenue (Heat Map)
        </div>
        <div class="card-body">
            {!! Form::open(['method' => 'GET', 'url' => route('admin.reports.sessions-revenue')]) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="month" class="required">From - To</label>
                        <div class="input-group">
                            <input type="month" name="month"
                                value="{{ request('month') != null ? request('month') : date('Y-m') }}" id="month"
                                class="form-control shadow-none">

                            <div class="input-group-append">
                                <button class="btn btn-success" type="submit">
                                    <i class="fa fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="table-responsive">
                <table class="table table-bordered text-center table-striped table-outline table-hover zero-configuration">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-dark">#</th>
                            <th class="text-dark">{{ trans('global.session_name') }}</th>
                            <th class="text-dark">Sessions Count</th>
                            <th class="text-dark">Attendance</th>
                            <th class="text-dark">Invoices</th>
                            <th class="text-dark">Trainer Profit</th>
                            <th class="text-dark">Expenses</th>
                            <td class="text-dark">Profit</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $key => $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value['session'] }}</td>
                                <td>
                                    @foreach ($value['days'] as $item)
                                        {{ $item }} <br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
