@if (config('domains')[config('app.url')]['employees_schedule'] == true)
    @include('partials.schedule')
@endif

<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-primary text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ $memberships->count() }}<span class="fs-6 fw-normal"></h5>
                    <h5><i class="fa fa-users"></i> {{ trans('cruds.membership.title') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-primary text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">
                        {{ $attendances->count() }}</h5>
                    <h5><i class="fas fa-fingerprint"></i> {{ trans('global.attendances') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-primary text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">
                        {{ number_format($payments->sum('amount')) .' EGP' }}</h5>
                    <h5><i class="fas fa-wallet"></i> {{ trans('payments') }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                {{ trans('cruds.transactions.title') }}
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                        <a class="nav-link active" id="scheduale-tab" data-toggle="pill" href="#scheduale" role="tab"
                            aria-controls="scheduale" aria-selected="false"> <i class="fas fa-fingerprint"></i>
                            {{ trans('global.attendances') }}</a>

                        <a class="nav-link " id="memberships-tab" data-toggle="pill" href="#memberships" role="tab"
                            aria-controls="memberships" aria-selected="false"> <i class="fa fa-users"></i>
                            {{ trans('cruds.membership.title') }}</a>

                            <a class="nav-link " id="payments-tab" data-toggle="pill" href="#payments" role="tab"
                            aria-controls="payments" aria-selected="false"> <i class="fa fa-users"></i>
                            {{ trans('cruds.payment.title') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <div class="tab-content" id="v-pills-tabContent">

                    <div class="tab-pane fade show active"  id="scheduale" role="tabpanel" aria-labelledby="memberships-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover zero-configuration">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ trans('cruds.member.title_singular') }}</th>
                                            <th>{{ trans('cruds.membership.title_singular') }}</th>
                                            <th>{{ trans('cruds.membershipAttendance.fields.sign_in') }}</th>
                                            <th>{{ trans('cruds.membershipAttendance.fields.sign_out') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendances as $attendance)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $attendance->membership->member->name ?? '' }}</td>
                                                <td>{{ $attendance->membership->service_pricelist->service->name .' @ '.$attendance->membership->service_pricelist->amount .' - '.$attendance->membership->service_pricelist->service->service_type->name }}</td>
                                                <td>{{ $attendance->sign_in }}</td>
                                                <td>{{ $attendance->sign_out }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade " id="memberships" role="tabpanel" aria-labelledby="memberships-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover zero-configuration">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ trans('cruds.membership.fields.id') }}</th>
                                            <th>{{ trans('cruds.membership.fields.service') }}</th>
                                            <th>{{ trans('global.amount') }}</th>
                                            <th>{{ trans('global.collected_amount') }}</th>
                                            <th>{{ trans('cruds.membership.fields.start_date') }}</th>
                                            <th>{{ trans('cruds.membership.fields.end_date') }}</th>
                                            <th>{{ trans('cruds.membership.fields.trainer') }}</th>
                                            <th>{{ trans('cruds.membership.fields.created_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($memberships as $membership)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $membership->id }}</td>
                                                <td>{{ $membership->service_pricelist->service->name ?? '-' }}
                                                </td>
                                                <td>{{ $membership->service_pricelist->amount ?? '-' }}</td>
                                                <td>{{ $membership->invoice->payments->sum('amount') ?? '-' }}</td>
                                                <td>{{ $membership->start_date }}</td>
                                                <td>{{ $membership->end_date }}</td>
                                                <td>{{ $membership->trainer->name ?? '-' }}</td>
                                                <td>{{ $membership->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade " id="payments" role="tabpanel" aria-labelledby="payments-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('cruds.membership.title_singular') }}</th>
                                            <th>{{ trans('cruds.member.title_singular') }}</th>
                                            <th>{{ trans('cruds.account.title_singular') }}</th>
                                            <th>{{ trans('global.amount') }}</th>
                                            <th>{{ trans('cruds.member.fields.sales_by') }}</th>
                                            <th>{{ trans('cruds.membership.fields.created_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->invoice->membership->service_pricelist->service->name .' @ '.$payment->invoice->membership->service_pricelist->amount .' - '.$payment->invoice->membership->service_pricelist->service->service_type->name }}</td>
                                                <td>{{ $payment->invoice->membership->member->name ?? '-' }}</td>
                                                <td>{{ $payment->account->name ?? '-' }}</td>
                                                <td>{{ $payment->amount }}</td>
                                                <td>{{ $payment->sales_by->name ?? '-' }}</td>
                                                <td>{{ $payment->created_at ?? '-' }}</td>
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
    </div>
</div> --}}

{{-- @include('partials.searchMember') --}}

@if (config('domains')[config('app.url')]['profile_attendance'] == true)
    @include('partials.profile_attendance')
@endif


<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fa fa-user"></i> {{ trans('global.attendance_data') }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover text-center zero-configuration">
            <thead class="thead-light">
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
                    <th>{{ trans('cruds.locker.title_singular') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($today_attendants as $attendant)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($attendant->membership->member->photo)
                                <a href="{{ $attendant->membership->member->photo->getUrl() }}" target="_blank"
                                    style="display: inline-block">
                                    <img src="{{ $attendant->membership->member->photo->getUrl() }}"
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
                            <a href="{{ route('admin.members.show', $attendant->membership->member->id) }}"
                                target="_blank">
                                {{ \App\Models\Setting::first()->member_prefix . $attendant->membership->member->member_code }}
                            </a>
                        </td>
                        <td class="font-weight-bold">
                            <a href="{{ route('admin.members.show', $attendant->membership->member->id) }}"
                                target="_blank">
                                {{ $attendant->membership->member->name }}
                            </a>
                        </td>
                        <td>
                            {{ $attendant->membership->service_pricelist->name ?? '-' }}
                        </td>
                        <td>{{ date('g:i A', strtotime($attendant->sign_in)) }}</td>
                        @if (\App\Models\Setting::first()->has_lockers == true)
                            <td>{{ date('g:i A', strtotime($attendant->sign_out)) }}</td>
                        @endif
                        <td>
                            {{ $attendant->membership->end_date ?? '-' }}
                        </td>
                        <td>
                            {{ ($attendant->membership->service_pricelist->service->service_type->session_type == "sessions") || ($attendant->membership->service_pricelist->service->service_type->session_type == "group_sessions") ? $attendant->membership->attendances_count ."/".$attendant->membership->service_pricelist->session_count : $attendant->membership->attendances_count }}
                        </td>
                        <th>
                            <span
                                class="badge badge-{{ \App\Models\Membership::STATUS[$attendant->membership->status] }} p-2">
                                <i class="fa fa-recycle"></i> {{ $attendant->membership->status ?? '-' }}
                            </span>
                        </th>
                        <th>
                            {!! $attendant->locker ?? '<span class="badge badge-danger">No locker</span>' !!}
                        </th>
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
