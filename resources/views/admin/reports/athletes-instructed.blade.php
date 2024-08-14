@extends('layouts.admin')
@section('content')
<div class="row mb-2">
    <div class="col-md-6 text-left">
        <a href="{{ \URL::previous() }}" class="btn btn-danger">
            <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
        </a>
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.athletes-instructed-report.export', request()->all())]) !!}
        <button type="submit" class="btn btn-success">
            <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
        </button>
        {!! Form::close() !!}
    </div>
    <div class="col-md-6 text-right">
        <h4>
            <span class="d-inline-block font-weight-bold">
                {{ trans('global.total') }} : {{ $athletes->count() }}
            </span>
            <span class="d-inline-block ml-4 text-success font-weight-bold">
                ( {{ trans('global.attendance_count') . ' : ' . $counter }} )
            </span>
        </h4>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header">
        <i class="fas fa-fingerprint"></i> {{ trans('global.athletes_instructed') }}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table text-center table-bordered table-hover table-striped zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark font-weight-bold">#</th>
                        <th class="text-dark font-weight-bold">{{ trans('cruds.member.title_singular') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.attendance_count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($athletes as $athlete)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td>{{ $athlete->first()->member->name }}</td>
                            <td>{{ $athlete->count() }}</td>
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