<div class="tab-pane fade" id="membershipSchedules" role="tabpanel" aria-labelledby="membershipSchedules-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover zero-configuration">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Membership</th>
                        <th>Rest Amounts</th>
                        <th>Start Date - End Date</th>
                        <th>Sessions</th>
                        <th>Last Attend</th>
                        <th>
                            {{ trans('global.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($member->membership_schedules as $index => $membership_sch)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $membership_sch->membership->service_pricelist->name ?? '-' }}
                            </td>
                            <td>{{ $membership_sch->membership->invoice->rest ?? 0 }} LE</td>
                            <td>
                                Start Date : {{ $membership_sch->membership->start_date }} <br>
                                End Date : {{ $membership_sch->membership->end_date }} <br>
                                Status : {{ $membership_sch->membership->status }} <br>
                            </td>
                            <td id="count-{{ $index }}">
                                {{ $membership_sch->membership->trainer_attendances_count }} /
                                {{ $membership_sch->membership->service_pricelist->session_count }}
                            </td>
                            <td>
                                {{ $membership_sch->membership->trainer_attendances->count() > 0 ? date('Y-m-d', strtotime($membership_sch->membership->trainer_attendances->last()->created_at)) : '-' }}
                            </td>
                            <td>
                                {{-- @if ($membership_sch->membership->trainer_attendances && $membership_sch->membership->trainer_attendances->last()->schedule_id != $schedule->id && date('Y-m-d', strtotime($membership_sch->membership->trainer_attendances->last()->created_at)) < date('Y-m-d')) --}}
                                {{-- <form action="{{ route('admin.membership-schedule.attend') }}" onsubmit="event.preventDefault(); toSubmit(this,{{$index}});" method="POST"
                                                                 style="display: inline-block;">
                                                                <input type="hidden" name="member_id" value="{{ $membership_sch->membership->member_id }}">
                                                                <input type="hidden" name="membership_id" value="{{ $membership_sch->membership_id }}">
                                                                <input type="hidden" name="schedule_id" value="{{ $membership_sch->schedule_id }}">
                                                                <input type="hidden" name="trainer_id" value="{{ $membership_sch->schedule->trainer_id }}">
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                <button type="submit" class="btn btn-info btn-sm">
                                                                    <i class="fa fa-check-circle"></i> Attend
                                                                </button>
                                                            </form> --}}
                                {{-- @endif --}}

                                <form action="{{ route('admin.membership-schedule.destroy', $membership_sch->id) }}"
                                    method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                    style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i> {{ trans('global.delete') }}
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
