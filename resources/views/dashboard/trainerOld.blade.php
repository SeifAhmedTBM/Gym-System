<div class="row">
    <div class="col-sm-4 col-lg-4">
        <a href="{{ route('admin.memberships.index', [
            'trainer_id' => auth()->id(),
            'created_at[from]'  => date('Y-m-01'),
            'created_at[to]'    => date('Y-m-t'),
        ]) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body bg-primary text-white text-center">
                    <div>
                        <h5 class="fs-4 fw-semibold">{{ $memberships->count() }}<span class="fs-6 fw-normal"></h5>
                        <h5><i class="fa fa-users"></i> {{ trans('cruds.membership.title') }}</h5>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4 col-lg-4">
        <a href="{{ route('admin.invoices.index',
        [
            'created_at[from]'  => date('Y-m-01'),
            'created_at[to]'  => date('Y-m-t'),
            'relations' => [
                'membership.trainer' => [
                    'name' => auth()->user()->name
                ]
            ],
            // 'filter_by' => 'month'

        ]
        ) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body bg-success text-white text-center">
                    <div>
                        <h5 class="fs-4 fw-semibold">
                            {{ number_format($payments) .' EGP' }}</h5>
                        <h5><i class="fas fa-wallet"></i> {{ trans('payments') }}</h5>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4 col-lg-4">
        <div class="card">
            <div class="card-body bg-success text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ $payments * ($commission / 100).' EGP' .' ( '.$commission.' % ) ' }}</h5>
                    {{-- <h5 class="fs-4 fw-semibold">{{ $commission_value * ($commission / 100).' EGP' .' ( '.$commission.' % ) ' }}</h5> --}}
                    <h5>{{ trans('global.commission') }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-6">
        <a href="{{ route('admin.membership-attendances.index',[
                'created_at[from]'  => date('Y-m-01'),
                'created_at[to]'  => date('Y-m-t'),
                'relations' => [
                    'membership.trainer' => [
                        'name' => auth()->user()->name
                    ]
                ],
            ]) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body bg-primary text-white text-center">
                    <div>
                        <h5 class="fs-4 fw-semibold">
                            {{ $attendances->count() }}</h5>
                        <h5><i class="fas fa-fingerprint"></i> {{ trans('global.attendances') }}</h5>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-6">
        <a href="{{ route('admin.membership-attendances.index',[
                'created_at[from]'  => date('Y-m-d'),
                'relations' => [
                    'membership.trainer' => [
                        'name' => auth()->user()->name
                    ]
                ],
            ]) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body bg-primary text-white text-center">
                    <div>
                        <h5 class="fs-4 fw-semibold">
                            {{ $daily_attendances->count() }}</h5>
                        <h5><i class="fas fa-fingerprint"></i> {{ trans('global.daily_attendances') }}</h5>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-hover zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('cruds.member.fields.photo') }}</th>
                                    <th>
                                        {{ trans('cruds.lead.fields.member_code') }}
                                    </th>
                                    <th>{{ trans('global.name') }}</th>
                                    <th>{{ trans('cruds.service.title_singular') }}</th>
                                    <th>{{ trans('cruds.membershipAttendance.fields.sign_in') }}</th>
                                    @if (\App\Models\Setting::first()->has_lockers == true)
                                        <th>{{ trans('cruds.membershipAttendance.fields.sign_out') }}</th>
                                    @endif
                                    <th>{{ trans('global.end_date') }}</th>
                                    <th>{{ trans('global.attendance_count') }}</th>
                                    <th>{{ trans('cruds.status.title_singular') }}</th>
                                    @if (\App\Models\Setting::first()->has_lockers == true)
                                        <th>{{ trans('cruds.locker.title_singular') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($daily_attendances as $attend)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($attend->membership->member->photo)
                                                <a href="{{ $attend->membership->member->photo->getUrl() }}" target="_blank"
                                                    style="display: inline-block">
                                                    <img src="{{ $attend->membership->member->photo->getUrl() }}"
                                                        class="rounded-circle" style="width: 50px;height:50px">
                                                </a>
                                            @else
                                                <a href="{{ asset('images/user.png') }}" target="_blank"
                                                    style="display: inline-block">
                                                    <img src="{{ asset('images/user.png') }}" class="rounded-circle"
                                                        style="width: 50px;height:50px">
                                                </a>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">
                                            <a href="{{ route('admin.members.show', $attend->membership->member->id) }}"
                                                target="_blank">
                                                {{ \App\Models\Setting::first()->member_prefix . $attend->membership->member->member_code }}
                                            </a>
                                        </td>
                                        <td class="font-weight-bold">
                                            <a href="{{ route('admin.members.show', $attend->membership->member->id) }}"
                                                target="_blank">
                                                {{ $attend->membership->member->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $attend->membership->service_pricelist->name ?? '-' }}
                                        </td>
                                        <td>{{ date('g:i A', strtotime($attend->sign_in)) }}</td>
                                        @if (\App\Models\Setting::first()->has_lockers == true)
                                            <td>{{ date('g:i A', strtotime($attend->sign_out)) }}</td>
                                        @endif
                                        <td>
                                            {{ $attend->membership->end_date ?? '-' }}
                                        </td>
                                        <td>
                                            {{ ($attend->membership->service_pricelist->service->service_type->session_type == "sessions") || ($attend->membership->service_pricelist->service->service_type->session_type == "group_sessions") ? $attend->membership->attendances_count ."/".$attend->membership->service_pricelist->session_count : $attend->membership->attendances_count }}
                                        </td>
                                        <th>
                                            <span
                                                class="badge badge-{{ \App\Models\Membership::STATUS[$attend->membership->status] }} p-2">
                                                <i class="fa fa-recycle"></i> {{ $attend->membership->status ?? '-' }}
                                            </span>
                                        </th>
                                        @if (\App\Models\Setting::first()->has_lockers == true)
                                            <th>
                                                {!! $attend->locker ?? '<span class="badge badge-danger">No locker</span>' !!}
                                            </th>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
