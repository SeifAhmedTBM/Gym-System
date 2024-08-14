@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <div class="btn-group">
            <a href="{{ route('admin.membership-schedule.create', $schedule->id) }}" class="btn btn-sm btn-success">
                <i class="fa fa-plus"></i> Add Membership
            </a>
            <a class="btn btn-primary" href="{{ route('admin.membership-schedule.attendances', $schedule->id) }}">
                <i class="fas fa-fingerprint"></i> {{ trans('global.attendances') }}
            </a>
        </div>
    </div>

    <div class="form-group">
        <div class="card">
            <div class="card-header">
                {{ $schedule->session->name ?? '-' }} - {{ $schedule->trainer->name ?? '-' }} -
                {{ date('h:i A', strtotime($schedule->timeslot->from)) . ' To ' . date('h:i A', strtotime($schedule->timeslot->to)) }}
            </div>
            <div class="card-body">
                <div class="form-group">
                    <form action="{{ route('admin.membership-schedule.swip-attend', $schedule->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        <input type="hidden" name="trainer_id" value="{{ $schedule->trainer_id }}">
                        <input type="number" class="form-control form-control-lg bg-white text-dark" name="card_number"
                            id="card_number" autofocus>
                    </form>
                </div>
                <table class="table table-striped table-bordered table-hover zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
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
                        @foreach ($schedule->schedule_main->membership_schedules as $index => $membership_sch)
                            @if (isset($membership_sch->membership->member))
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a class="text-decoration-none"
                                            href="{{ route('admin.members.show', $membership_sch->membership->member_id) }}">
                                            {{ $membership_sch->membership->member->member_code ?? '-' }} <br>
                                            {{ $membership_sch->membership->member->name ?? '-' }} <br>
                                            {{ $membership_sch->membership->member->phone ?? '-' }}
                                        </a>
                                    </td>
                                    <td>{{ $membership_sch->membership->service_pricelist->name ?? '-' }}
                                        <br>
                                        Status: <span
                                            class="badge badge-{{ \App\Models\Membership::STATUS[$membership_sch->membership->status] }}">
                                            {{ \App\Models\Membership::SELECT_STATUS[$membership_sch->membership->status] }}
                                        </span> <br>
                                    </td>
                                    <td>{{ $membership_sch->membership->invoice->rest ?? 0 }} LE</td>
                                    <td>
                                        Start Date : {{ $membership_sch->membership->start_date }} <br>
                                        End Date : {{ $membership_sch->membership->end_date }} <br>

                                    </td>
                                    <td id="count-{{ $index }}">
                                        {{ $membership_sch->membership->trainer_attendances_count }} /
                                        {{ $membership_sch->membership->service_pricelist->session_count }}
                                    </td>
                                    <td>
                                        {{ $membership_sch->membership->trainer_attendances->count() > 0 ? date('Y-m-d', strtotime($membership_sch->membership->trainer_attendances->last()->created_at)) : '-' }}
                                    </td>
                                    <td>
                                        {{-- @if (($membership_sch->membership->trainer_attendances_count > 0 && $membership_sch->membership->trainer_attendances->last()->schedule_id != $schedule->id && date('Y-m-d', strtotime($membership_sch->membership->trainer_attendances->last()->created_at)) < date('Y-m-d')) || $membership_sch->membership->trainer_attendances->last() == null) --}}
                                        @if (
                                            $membership_sch->membership->trainer_attendances_count <
                                                $membership_sch->membership->service_pricelist->session_count &&
                                                $membership_sch->membership->status != 'expired')
                                            <form action="{{ route('admin.membership-schedule.attend') }}"
                                                onsubmit="event.preventDefault(); toSubmit(this,{{ $index }});"
                                                method="POST" style="display: inline-block;">
                                                <input type="hidden" name="member_id"
                                                    value="{{ $membership_sch->membership->member_id }}">
                                                <input type="hidden" name="membership_id"
                                                    value="{{ $membership_sch->membership_id }}">
                                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                <input type="hidden" name="trainer_id"
                                                    value="{{ $schedule->trainer_id }}">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-info btn-sm"
                                                    onclick="incrementCount({{ $index }})">
                                                    <i class="fa fa-check-circle"></i> Attend
                                                </button>
                                            </form>
                                        @endif
                                        {{-- @endif --}}
                                        <a href="{{ route('admin.showAttendanceDetails.index', $membership_sch->id) }}"
                                            id="showAttendanceDetails" class="btn btn-success btn-sm">
                                            Show Attendance Details
                                        </a>
                                        <form
                                            action="{{ route('admin.membership-schedule.destroy', $membership_sch->id) }}"
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
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function toSubmit(obj, index) {

            var formdata = $(obj).serialize(); // here $(this) refere to the form its submitting
            let url = $(obj).attr("action");
            $.ajax({
                type: 'POST',
                url: url,
                data: formdata, // here $(this) refers to the ajax object not form
                success: function(data) {
                    console.log('data' + data);
                    // $('#addAppointmentModal').modal('toggle');
                    // $('#calendar').fullCalendar('refetchEvents');
                    // $(this).trigger('reset');
                    counter = parseFloat($("#count-" + index).text().split('/')[0]);
                    console.log(counter)
                    all = parseFloat($("#count-" + index).text().split('/')[1]);
                    $("#count-" + index).html((counter + 1) + " / " + all);
                    $('button[type="submit"]').prop("disabled", false);
                    swal("Good job!", 'Save Succefully', "success");
                    // console.log('data'+data.responseJSON);
                    // console.dir(data);


                },
                error: function(data) {
                    // swal("Error", , "error");
                    $('button[type="submit"]').prop("disabled", false);
                    // console.log('data'+data.responseJSON);
                    console.dir(data);


                },
            });
            $('button[type="submit"]').prop("disabled", false);
        }
    </script>
@endsection
