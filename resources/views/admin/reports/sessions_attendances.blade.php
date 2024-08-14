@extends('layouts.admin')
@section('content')
@if (!request()->has('sortBy'))
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white font-weight-bold">
        <i class="fa fa-file"></i> {{ trans('global.sessions_attendances_report') }} .
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reports.sessionsAttendancesReport') }}" method="get">
            <div class="row">
                <div class="col-md-3">
                    <label for="date">{{ trans('global.date') }}</label>
                    <input type="date" class="form-control" value="{{ request()->date ?? date('Y-m-d') }}" name="date">
                </div>
                <div class="col-md-2">
                    <label for="filter" class="d-block">{{ trans('global.filter') }}</label>
                    <button class="btn btn-info"><i class="fa fa-filter"></i> {{ trans('global.filter') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
<div class="row mb-2">
    <div class="col-md-12 text-right">
        <a href="{{ route('admin.reports.session-attendances.export', request()->all()) }}" class="btn text-decoration-none text-white btn-danger">
            <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
        </a>
    </div>
</div>
@forelse ($data as $data)
    <div class="card shadow-sm border-0">
        <div class="card-header border-0">
            <div class="row">
                <div class="col-md-4">
                    <span class="badge btn-block font-weight-bold badge-primary mx-2 px-3 py-2" style="background-color : {{ $data['color'] }}">
                        <h6 class="font-weight-bold">( {{ $data['session'] }} )</h6>
                        <h6 class="font-weight-bold">{{ $data['timeslot'] }}</h6>
                    </span>
                </div>
                <div class="col-md-4">
                    {{-- <span class="badge badge-primary mx-2 px-3 font-weight-bold py-2 float-right"> --}}
                        <span class="badge btn-block font-weight-bold badge-primary mx-2 px-3 py-2">
                            <h6 class="font-weight-bold">{{ trans('global.attendance_count') }} </h6>
                            <h6 class="font-weight-bold">{{ count($data['attendants']) }}</h6>
                        </span>
                    {{-- </span> --}}
                </div>
                <div class="col-md-4">
                    {{-- <span class="badge bg-dribbble text-white font-weight-bold px-3 py-2 float-right"> --}}
                    <span class="badge btn-block font-weight-bold bg-dribbble mx-2 px-3 py-2">
                        <h6 class="font-weight-bold text-white">{{ trans('global.trainer') }} </h6>
                        <h6 class="font-weight-bold text-white">{{ $data['schedule_trainer'] }}</h6>
                    </span>
                    {{-- </span> --}}
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark font-weight-bold">{{ trans('cruds.member.title_singular') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.trainer') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['attendants'] as $attendance)

                        @if ($attendance->member != NULL)
                        <tr>
                            <td class="font-weight-bold">
                                {{ $attendance->member->name }} , 
                                {{ App\Models\Setting::first() ? App\Models\Setting::first()->member_prefix . $attendance->member->member_code : $attendance->member->member_code }} , 
                                {{ $attendance->member->phone }} , 
                                {{ Str::ucfirst($attendance->member->gender) }}
                            </td>
                            <td>
                                {{ $attendance->trainer->name }}
                            </td>
                            <td>
                                {{ $attendance->created_at->format('g:i A') }}
                            </td>
                        </tr>
                        @endif
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@empty
    <h4 class="text-center font-weight-bold text-danger my-3">
        {{ trans('global.no_data_available') }}
    </h4>
@endforelse
@endsection