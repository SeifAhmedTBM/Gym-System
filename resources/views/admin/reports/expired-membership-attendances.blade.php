@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12 mb-2">
            @include('admin_includes.filters', [
            'columns' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'related_to' => 'membership.member'],
                'phone' => ['label' => 'Phone', 'type' => 'number', 'related_to' => 'membership.member'],
                'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
            ],
                'route' => 'admin.reports.expired-membership-attendances'
            ])
            {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.expired-membership-attendances.export', request()->all())]) !!}
            <button type="submit" class="btn btn-success">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </button>
        {!! Form::close() !!}
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header font-weight-bold">
            <i class="fa fa-times-circle"></i> {{ trans('global.exp_attendances') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center table-bordered table-hover table-outline">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-dark font-weight-bold">#</th>
                            <th class="text-dark font-weight-bold">{{ trans('cruds.membershipAttendance.fields.name') }}</th>
                            <th class="text-dark font-weight-bold">{{ trans('cruds.membershipAttendance.fields.membership') }}</th>
                            <th class="text-dark font-weight-bold">{{ trans('global.trainer') }}</th>
                            <th class="text-dark font-weight-bold">{{ trans('cruds.membershipAttendance.fields.sign_in') }}</th>
                            <th class="text-dark font-weight-bold">{{ trans('cruds.membershipAttendance.fields.sign_out') }}</th>
                            <th class="text-dark font-weight-bold">{{ trans('global.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($membershipAttendances as $attendance)
                            <tr>
                                <td>#{{ $loop->iteration }}</td>
                                <td>
                                    <a class="font-weight-bold" href="{{ route('admin.members.show', $attendance->membership->member_id) }}">
                                        {{ (App\Models\Setting::first()->member_prefix ?? '') . $attendance->membership->member->member_code }}<br>
                                        {{ $attendance->membership->member->name }}<br>
                                        {{ $attendance->membership->member->phone }}
                                    </a>
                                </td>
                                <td>
                                    <span class="text-success font-weight-bold">
                                        {{ trans('cruds.pricelist.title_singular') }}
                                    </span> : {{ $attendance->membership->service_pricelist->name }} <br>
                                    <span class="text-danger font-weight-bold">
                                        {{ trans('cruds.invoice.fields.service_fee') }}    
                                    </span> : {{ $attendance->membership->service_pricelist->amount . ' EGP' }}
                                </td>
                                <td>
                                    @if ($attendance->membership->trainer_id != NULL)
                                        <a href="{{ route('admin.users.show', $attendance->membership->trainer_id) }}" class="font-weight-bold text-decoration-none">
                                            <i class="far fa-user"></i> {{ $attendance->membership->trainer->name }}
                                        </a>
                                    @else
                                        <span class="badge badge-danger">
                                            {{ trans('global.no_data_available') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success px-3 py-2 rounded-pill">
                                        {{ date('g:i A', strtotime($attendance->sign_in)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-danger px-3 py-2 rounded-pill">
                                        {{ date('g:i A', strtotime($attendance->sign_out)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $attendance->created_at->toFormattedDateString() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">{{ trans('global.no_data_available') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                {{ $membershipAttendances->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
@endsection
