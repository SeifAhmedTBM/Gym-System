@extends('layouts.admin')
@section('content')
<div class="mb-2">
    @can('export_revenues')
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.revenues-report.export', request()->all())]) !!}
            <button type="submit" class="btn btn-success">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </button>
        {!! Form::close() !!}
    @endcan
</div>
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fa fa-money-bill"></i> {{ trans('global.revenue_report') }}
    </div>
    <div class="card-body">
        {!! Form::open(['method' => 'GET', 'url' => route('admin.reports.revenue')]) !!}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="month" class="required">{{ trans('global.month') }}</label>
                    <div class="input-group">
                        <input type="month" name="month" value="{{ request('month') != NULL ? request('month') : date('Y-m') }}" id="month" class="form-control shadow-none">
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
                        <th class="text-dark">{{ trans('global.max_capacity') }}</th>
                        <th class="text-dark">{{ trans('global.session_count') }}</th>
                        <th class="text-dark">{{ trans('global.attended') }}</th>
                        <th class="text-dark">{{ trans('global.revenue') }}</th>
                        <th class="text-dark">{{ trans('global.avg_uti_rate') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (collect($report)->sortByDesc('utilization_rate') as $rep)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge px-3 py-2 font-weight-bold" style="font-size:12px;background: {{ $rep['session']['color'] }};">
                                    <span style="">{{ $rep['session']['name'] }}</span>
                                </span>
                            </td>
                            <td>
                                {{ $rep['session']['max_capacity'] }}
                            </td>
                            <td>
                                <a target="_blank" href="{{ route('admin.reports.sessions-instructed', [
                                    'session'   => $rep['session_id'],
                                    'date'      => request('month') != NULL ? request('month') : date('Y-m')
                                ]) }}">
                                    {{ $rep['sessions_count'] }}
                                </a>
                            </td>
                            <td>
                                <a target="_blank" href="{{ route('admin.reports.athletes-instructed', [
                                    'session'   => $rep['session_id'],
                                    'date'      => request('month') != NULL ? request('month') : date('Y-m')
                                ]) }}">
                                    {{ $rep['attendants'] }}
                                </a>
                            </td>
                            <td class="font-weight-bold text-danger">
                                <a href="{{ route('admin.reports.revenue-details', [
                                    'session'   => $rep['session_id'],
                                    'date'      => request('month') != NULL ? request('month') : date('Y-m')
                                ]) }}">
                                    {{ round($rep['revenue']) . ' EGP' }}
                                </a>
                            </td>
                            <td>
                                {{ $rep['utilization_rate'] }} %
                            </td>
                        </tr>
                    @empty
                        
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection