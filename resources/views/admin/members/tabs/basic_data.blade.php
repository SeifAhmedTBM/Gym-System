<div class="tab-pane fade show active" id="basic_data" role="tabpanel" aria-labelledby="basic_data-tab">
    <div class="row ">
        <div class="col-md-10 mx-auto mb-5">
            <div class="row">
                <div class="col-md-3 text-left">
                    @if ($member->photo)
                        <a href="{{ $member->photo->getUrl() }}" target="_blank" style="display: inline-block">
                            <img src="{{ $member->photo->getUrl() }}" class="rounded-circle"
                                style="width: 200px;height:200px;object-fit:cover;">
                        </a>
                    @else
                        <a href="{{ asset('images/user.png') }}" target="_blank" style="display: inline-block">
                            <img src="{{ asset('images/user.png') }}" class="rounded-circle"
                                style="width: 200px;height:200px;object-fit:cover;">
                        </a>
                    @endif
                </div>
                <div class="col-md-9 mx-auto">
                    <ul class="list-group shadow-sm text-left">
                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.lead.fields.member_code') }} :
                            </span>
                            {{ ($member->branch ? $member->branch->member_prefix : '') . $member->member_code }}
                        </li>
                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.lead.fields.name') }} :
                            </span>
                            {{ $member->name }}
                        </li>
                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.lead.fields.phone') }} :
                            </span>
                            {{ $member->phone }}
                        </li>

                        @if (!is_null($member->parent_phone))
                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('global.parent_phone') }} :
                                </span>
                                {{ $member->parent_phone }}
                            </li>
                        @endif

                        @if (!is_null($member->parent_phone_two))
                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('global.parent_phone') . ' 2' }} :
                                </span>
                                {{ $member->parent_phone_two }}
                            </li>
                        @endif

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.branch.title_singular') }} :
                            </span>
                            {{ $member->branch->name ?? '-' }}
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.status.title_singular') }} :
                            </span>
                            {{ $member->status->name ?? '-' }}
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                Area :
                            </span>
                            @if ($member->address)
                                {{ $member->address->name }}
                            @else
                                -
                            @endif
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.user.fields.email') }} :
                            </span>
                            {{ $member->user->email ?? '-' }}
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.lead.fields.dob') }} :
                            </span>
                            {{ $member->dob ?? '-' }}
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('global.gender') }} :
                            </span>
                            {{ ucfirst($member->gender) ?? '-' }}
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('cruds.lead.fields.national') }} :
                            </span>
                            {{ $member->national ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                Sales By :
                            </span>
                            {{ $member->sales_by->name ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                {{ trans('global.coach') }} :
                            </span>
                            {{ $member->coach_by->name ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                PT Coach :
                            </span>
                            {{ $member->pt_coach_by->name ?? '-' }}
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold">
                                Created At :
                            </span>
                            {{ $member->created_at ?? '-' }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if (config('domains')[config('app.url')]['add_to_class_in_invoice'] == false)
        <h3 class="text-primary font-weight-bold mb-3">{{ trans('global.memberships_details') }} :
        </h3>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('global.last_attendance') }} :
                                </span>
                                @if (isset($last_membership) && $last_membership->service_pricelist)
                                    {!! $last_membership->last_attendance
                                        ? date('Y-m-d', strtotime($last_membership->last_attendance)) .
                                            '  ' .
                                            '<span class="text-danger">' .
                                            date('h:iA', strtotime($last_membership->last_attendance)) .
                                            '</span>'
                                        : '<span class="badge badge-danger">No Attendance</span>' !!}
                                @else
                                    <span class="badge badge-danger">{{ trans('global.no_data_available') }}</span>
                                @endif
                            </li>

                            @if (isset($last_membership) && $last_membership->service_pricelist)
                                <li class="list-group-item">
                                    <span class="font-weight-bold">
                                        {{ trans('global.main_service') }} :
                                    </span>
                                    {{ $main_membership->service_pricelist->name ?? $last_membership->service_pricelist->name }}

                                    @if (config('domains')[config('app.url')]['sports_option'] == true)
                                        <span class="d-block font-weight-bold">
                                            {{ $main_membership->sport->name ?? '-' }}
                                        </span>
                                    @endif
                                </li>

                                <li class="list-group-item">
                                    <span class="font-weight-bold">
                                        Service Attendance :
                                    </span>
                                    @if (isset($last_membership) && !is_null($last_membership) && $last_membership->service_pricelist)
                                        @if (isset($main_membership))
                                            {{ $main_membership->attendances->count() }}
                                        @else
                                            {{ $last_membership->attendances->count() }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </li>

                                <li class="list-group-item">
                                    <span class="font-weight-bold">
                                        {{ trans('global.start_date') . ' / ' . trans('global.end_date') }}
                                    </span>
                                    {{ $main_membership->start_date ?? $last_membership->start_date }}
                                    /
                                    {{ $main_membership->end_date ?? $last_membership->end_date }}
                                </li>

                                @if (
                                    $main_membership &&
                                        $main_membership->service_pricelist &&
                                        $main_membership->service_pricelist->service &&
                                        $main_membership->service_pricelist->service->service_type->session_type == 'sessions')
                                    <li class="list-group-item">
                                        <span class="font-weight-bold">
                                            {{ trans('global.membership_sessions') }} :
                                        </span>
                                        {{ $main_membership->attendances->count() }} /
                                        {{ $main_membership->service_pricelist->session_count }}
                                        ({{ $main_membership->service_pricelist->session_count - $main_membership->attendances->count() }}
                                        Sessions Left )
                                    </li>
                                @endif
                            @endif

                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('cruds.lead.fields.address_details') }} :
                                </span>
                                {{ $member->address_details ?? '-' }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('cruds.lead.fields.status') }} :
                                </span>
                                {{ $member->status->name ?? trans('global.no_data_available') }}
                            </li>

                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('cruds.lead.fields.source') }} :
                                </span>
                                {{ $member->source->name ?? '-' }}
                            </li>

                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('cruds.lead.fields.sales_by') }} :
                                </span>
                                {{ $member->sales_by->name ?? '-' }}
                            </li>

                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('cruds.lead.fields.referral_member') }} :
                                </span>
                                {{ $member->referral_member ?? trans('global.no_data_available') }}
                            </li>

                            <li class="list-group-item">
                                <span class="font-weight-bold">
                                    {{ trans('global.main_notes') }} :
                                </span>
                                {{ $member->notes ?? 'No Notes' }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
