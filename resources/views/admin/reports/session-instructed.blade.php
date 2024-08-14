@extends('layouts.admin')
@section('content')
<div class="row mb-2">
    <div class="col-md-6">
        <a href="{{ \URL::previous() }}" class="btn btn-danger">
            <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
        </a>
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.sessions-instructed-report.export', request()->all())]) !!}
        <button type="submit" class="btn btn-success">
            <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
        </button>
        {!! Form::close() !!}
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header">
        <i class="fas fa-fingerprint"></i> {{ trans('global.sessions_instructed') }}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table text-center table-bordered table-hover table-striped zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark font-weight-bold">#</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.trainer') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.session_name') }}</th>
                        <th class="text-dark font-weight-bold">Commission Type</th>
                        <th class="text-dark font-weight-bold">Amount</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.time') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td>{{ $session->trainer->name }}</td>
                            <td>{{ $session->schedule->session->name }}</td>
                            <td>{{ $session->schedule->schedule_main->commission_type }}</td>
                            <td>
                                @if ($session->schedule->schedule_main->commission_type == 'fixed')
                                    {{ $session->schedule->schedule_main->commission_amount }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ date('g:i A', strtotime($session->schedule->timeslot->from)) }}</td>
                            <td>{{ $session->day }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection