<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body" id="printMe">
                <a href="{{ route('admin.reports.sessionsAttendancesReport') }}" class="btn btn-sm float-right mb-2 btn-success">
                    <i class="fas fa-fingerprint"></i> {{ trans('global.session_attendances') }}
                </a>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="bg-primary " width="150">{{ trans('global.timeslots') }}</th>
                                @foreach ([
                                    'Sat'  => trans('global.saturday'),
                                    'Sun'  => trans('global.sunday'),
                                    'Mon'  => trans('global.monday'),
                                    'Tue'  => trans('global.tuesday'),
                                    'Wed'  => trans('global.wednesday'),
                                    'Thu'  => trans('global.thursday'),
                                    'Fri'  => trans('global.friday'),
                                ] as $key => $day)
                                    <th class="font-weight-bold">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $branch_id = Auth()->user()->employee && Auth()->user()->employee->branch_id != NULL ?
                                 Auth()->user()->employee->branch_id : NULL;
                            @endphp
                                @forelse ($timeslots as $timeslot)
                                    <tr>
                                        <td class="font-weight-bold">
                                            {{ date('g:i A', strtotime($timeslot->from)) }}  - {{ date('g:i A', strtotime($timeslot->to))}}
                                        </td>
                                        @foreach (App\Models\Schedule::DAY_SELECT as $k => $d)
                                            @if ($branch_id != NULL)
                                                @if ($sch_day = $timeslot->schedules()
                                                ->whereHas('schedule_main.schedule_main_group',fn($q) => $q->whereStatus('active'))
                                                ->whereHas('schedule_main',fn($x) => $x->whereStatus('active')->whereBranchId($branch_id))
                                                ->where('day', $k)->get())
                                                    <td class="font-weight-bold">
                                                        @forelse ($sch_day as $item)
                                                            <!-- <a href="{{ route('admin.membership-schedule.index',$item->id) }}" target="_blank" rel="noopener noreferrer"> -->
                                                                <span class="badge d-block mb-1 py-2 px-2 text-white"
                                                                style="background: {{ $item->session->color }}">
                                                                    {{ $item->session->name }} ( {{ $item->trainer->name }} )
                                                                </span>
                                                            <!-- </a> -->
                                                        @empty
                                                            ----
                                                        @endforelse
                                                    </td>
                                                @endif
                                            @else
                                                @if ($sch_day = $timeslot->schedules()->where('day', $k)->get())

                                                    <td class="font-weight-bold">
                                                        @forelse ($sch_day as $item)
                                                            <!-- <a href="{{ route('admin.membership-schedule.index',$item->id) }}" target="_blank" rel="noopener noreferrer"> -->
                                                                <span class="badge d-block mb-1 py-2 px-2 text-white"
                                                                style="background: {{ $item->session->color }}">
                                                                    {{ $item->session->name }} ( {{ $item->trainer->name }} )
                                                                </span>
                                                            <!-- </a> -->
                                                        @empty
                                                            ----
                                                        @endforelse
                                                    </td>
                                                @endif
                                            @endif
                                            
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ trans('global.no_data_available') }}
                                        </td>
                                    </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>