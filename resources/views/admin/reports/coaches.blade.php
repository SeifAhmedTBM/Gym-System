@extends('layouts.admin')
@section('content')
<div class="mb-2">
    {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.coaches-report.export', request()->all())]) !!}
    <button type="submit" class="btn btn-success">
        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
    </button>
    {!! Form::close() !!}
</div>
<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fa fa-users"></i> {{ trans('global.coaches') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                {!! Form::open(['method' => 'GET' , 'url' => route('admin.reports.coaches')]) !!}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('month', trans('global.month')) !!}
                            <div class="input-group">
                                {!! Form::month('month', request('month') ?? date('Y-m'), ['class' => 'form-control']) !!}
                                <div class="input-group-append">
                                    {!! Form::button('<i class="fa fa-filter"></i>', ['class' => 'btn btn-success', 'type' => 'submit']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th class="text-dark">{{ trans('global.coach') }}</th>
                        <th class="text-dark">Fixed</th>
                        <th class="text-dark">{{ trans('global.sessions_instructed') }}</th>
                        <th class="text-dark">{{ trans('global.athletes_instructed') }}</th>
                        <th class="text-dark">{{ trans('global.revenue') }}</th>
                        <th class="text-dark">{{ trans('global.ranking') }} ( {{ trans('global.revenue') }} )</th>
                        {{-- <th class="text-dark">{{ trans('global.avg_uti_rate') }}</th>
                        <th class="text-dark">{{ trans('global.ranking') }}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports->sortByDesc('revenue') as $report)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $report['trainer_name'] }}</td>
                            <td class="font-weight-bold">{{ $report['fixed'] }}</td>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.reports.sessions-instructed', ['date' => request('month') != NULL ? request('month') : date('Y-m'), 'trainer' => $report['trainer_id']]) }}">
                                    {{ $report['sessions_instructed'] }}
                                </a>
                            </td>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.reports.athletes-instructed', ['date' => request('month') != NULL ? request('month') : date('Y-m'), 'trainer' => $report['trainer_id']]) }}">
                                    {{ $report['athletes_instructed'] }}
                                </a>
                            </td>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.reports.revenue-details', ['date' => request('month') != NULL ? request('month') : date('Y-m'), 'trainer' => $report['trainer_id']]) }}">
                                    {{ $report['revenue'] . ' EGP' }}
                                </a>
                            </td>
                            <td class="font-weight-bold">
                                @if ($report['revenue'] == 0)
                                <span class="badge badge-danger px-3 py-2">
                                    {{ trans('global.unranked') }}
                                </span>
                                @else
                                    {{ $loop->iteration }}
                                @endif    
                            </td>
                            {{-- <td class="font-weight-bold"></td>
                            <td class="font-weight-bold"></td> --}}
                        </tr>
                    @empty
                       <tr>
                           <td colspan="8">
                               {{ trans('global.no_data_available') }}
                           </td>
                       </tr> 
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection