<h4><i class="fa fa-bell"></i> {{ trans('cruds.reminder.title') }}</h4>
<div class="row">
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('admin.reminders.index') }}" class="text-decoration-none" target="_blank">
            <div class="card ">
                <div class="card-body bg-success text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $today_reminders->count() }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.today_reminders') }}</h5>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card ">
            <a href="{{ route('admin.reminders.upcomming', ['due_date[from]' => date('Y-m-01'), 'due_date[to]' => date('Y-m-t')]) }}"
                class="text-decoration-none" target="_blank">
                <div class="card-body bg-warning text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $upcomming_reminders->count() }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('cruds.reminder.fields.upcomming_reminders') }}</h5>
                </div>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card ">
            <a href="{{ route('admin.reminders.overdue') }}" class="text-decoration-none" target="_blank">
                <div class="card-body bg-danger text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $overdue_reminders->count() }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('cruds.reminder.fields.overdue_remiders') }}</h5>
                </div>
            </a>
        </div>
    </div>
</div>
<hr>
<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-user"></i> Monthly PT MemberShips</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered zero-configuration">
                        <thead>
                            <th>#</th>
                            <th>{{ trans('cruds.lead.fields.member_code') }}</th>
                            <th>
                                {{ trans('cruds.membership.fields.member') }}
                            </th>
                            <th>{{ trans('cruds.membership.title') }}</th>
                            <th>{{ trans('cruds.service.fields.service_type') }}</th>
                            <th>{{ trans('global.date') }}</th>
                            <th>{{ trans('global.trainer') }}</th>
                            <th>Remaining Sessions</th>
                            <td>Payments</td>
                            <th>{{ trans('global.created_at') }}</th>
                            <th>{{ trans('global.action') }}</th>
                        </thead>
                        <tbody>
                            @foreach ($pt_members as $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td> 
                                        {{ $value->member->branch->member_prefix . '' . $value->member->member_code }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.members.show', $value->member->id) }}">
                                            {{ $value->member->name }} <br>
                                            {{ $value->member->phone }} 

                                            {{-- Memberships : {{ $value->member->memberships_count }} --}}
                                        </a>
                                    </td>
                                    {{-- <td>{{ $value->branch->name ?? '-' }}</td> --}}
                                    <td>
                                        {{ $value->service_pricelist->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $value->service_pricelist->service->service_type->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ 'Start Date : ' . $value->start_date ?? '-' }} <br>
                                        {{ 'End Date : ' . $value->end_date ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $value->trainer->name ?? '-' }}
                                    </td>
                                    <td>

                                        {{ $value->attendances_count ?? 0 }} \
                                        {{ $value->service_pricelist->session_count ?? 0 }}
                                    </td>
                                    <td>{{ $value->invoice->net_amount }}</td>
                                    <td>
                                        {{ $value->created_at->toFormattedDateString() ?? '-' }}
                                    </td>
                                    <td>
                                        <form action="{{ route('attendance.take') }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            {{-- <input type="hidden" name="_method" value="PUT"> --}}
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="membership_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-fingerprint"></i> Attend
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-user"></i> Monthly Assigned Members</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('cruds.lead.fields.member_code') }}</th>
                                <th>
                                    {{ trans('cruds.membership.fields.member') }}
                                </th>
                                {{-- <th>{{ trans('cruds.branch.title_singular') }}</th> --}}
                                <th>{{ trans('cruds.membership.title') }}</th>
                                <th>{{ trans('cruds.service.fields.service_type') }}</th>
                                <th>{{ trans('global.date') }}</th>
                                {{-- <th>{{ trans('global.trainer') }}</th> --}}
                                <th>Status</th>
                                <th>{{ trans('global.created_at') }}</th>
                                <th>Assigned Coach </th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($non_pt_members as $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $value->member->branch->member_prefix . '' . $value->member->member_code }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.members.show', $value->member->id) }}">
                                            {{ $value->member->name }} <br>
                                            {{ $value->member->phone }}
                                            {{-- Memberships : {{ $value->member->memberships_count }} --}}
                                        </a>
                                    </td>
                                    {{-- <td>{{ $value->branch->name ?? '-' }}</td> --}}
                                    <td>
                                        {{ $value->service_pricelist->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $value->service_pricelist->service->service_type->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ 'Start Date : ' . $value->start_date ?? '-' }} <br>
                                        {{ 'End Date : ' . $value->end_date ?? '-' }}
                                    </td>
                                    {{-- <td>
                                         {{ $value->trainer->name ?? 'Assign One' }}
                                     </td> --}}
                                    <td>
                                        <span
                                            class='badge badge-{{ App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$value->membership_status] }} " p-2'>

                                            {{ $value->membership_status }}</span>
                                    </td>

                                    <td>
                                        {{ $value->created_at->toFormattedDateString() ?? '-' }}
                                    </td>
                                    <td>

                                        {{ $value->assigned_coach->name ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
