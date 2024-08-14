@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-6 text-left">
        <a href="{{ \URL::previous() }}" class="btn btn-danger mb-2">
            <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
        </a>
    </div>
    <div class="col-md-6 text-right mb-2">
        <form action="{{ route('admin.cache.remove') }}" method="POST">
            @csrf
            <button class="btn btn-lg btn-primary" type="submit">
                <i class="fas fa-sync-alt"></i>
            </button>
        </form>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fas fa-fingerprint"></i> {{ trans('global.session_attendances') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.athlete_name') }}</th>
                        <th>{{ trans('global.no_of_attendances') }}</th>
                        <th>{{ trans('global.subscription') }}</th>
                        <th>{{ trans('global.cost_per_session') }}</th>
                        <th>{{ trans('global.revenue') }}</th>
                    </tr>
                </thead>
                <tbody class="font-weight-bold text-dark">
                    @forelse ($session->trainer_attendants()->get()->groupBy('member_id') as $key => $attendant)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $attendant->first()->member->name }}</td>
                        <td>{{ $no_of_attendances[$loop->iteration-1] }}</td>
                        <td>{{ $subscription[$loop->iteration-1] }}</td>
                        <td>
                            {{ $cost_per_session[$loop->iteration-1] }} EGP
                        </td>
                        <td>
                            {{ $revenue[$loop->iteration-1] }} EGP
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection